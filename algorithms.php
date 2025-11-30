<?php
/**
 * Hospital Management System - Best-Fit Algorithms
 * This file contains optimized algorithms for:
 * - Appointment scheduling
 * - Doctor workload distribution
 * - Resource allocation
 * - Patient-Doctor optimal matching
 */

// Note: session_start() should be called in the main file before including this file
$con = mysqli_connect("localhost", "root", "", "hospitaldatabase");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// ============================================================================
// 1. BEST-FIT ALGORITHM - For Time Slot Allocation
// ============================================================================
/**
 * Best-Fit Algorithm: Assigns appointments to the time slot with minimum 
 * remaining capacity (but enough for the appointment)
 * 
 * @param array $timeSlots - Available time slots with capacities
 * @param int $appointmentDuration - Duration of appointment in minutes
 * @return array - Best fitting slot or null if no fit found
 */
function bestFitTimeSlot($timeSlots, $appointmentDuration) {
    $bestFit = null;
    $minRemainingCapacity = PHP_INT_MAX;
    
    foreach ($timeSlots as $slot) {
        $remainingCapacity = $slot['capacity'] - $slot['booked'];
        
        // Check if slot has enough capacity
        if ($remainingCapacity >= $appointmentDuration) {
            // Pick slot with minimum wasted space
            if ($remainingCapacity < $minRemainingCapacity) {
                $minRemainingCapacity = $remainingCapacity;
                $bestFit = $slot;
            }
        }
    }
    
    return $bestFit;
}

// ============================================================================
// 2. FIRST-FIT DECREASING (FFD) - For Doctor Workload Balancing
// ============================================================================
/**
 * First-Fit Decreasing Algorithm: Sorts doctors by workload (descending)
 * and assigns new appointments to the first doctor with available capacity
 * 
 * @param int $doctorId - Doctor ID (optional for new assignment)
 * @param array $appointmentDetails - Details of appointment to schedule
 * @return array - Recommended doctor and time slot
 */
function firstFitDecreasingDoctor($appointmentDetails = null) {
    global $con;
    
    $query = "SELECT d.id, d.username, d.name, 
              COUNT(a.id) as appointmentCount,
              MAX(CAST(a.appointmentDate AS DATE)) as lastAppointment
              FROM doctb d 
              LEFT JOIN appointmenttb a ON d.id = a.docid 
              GROUP BY d.id 
              ORDER BY appointmentCount DESC";
    
    $result = mysqli_query($con, $query);
    $doctors = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $doctors[] = $row;
    }
    
    // Sort by workload (descending)
    usort($doctors, function($a, $b) {
        return $b['appointmentCount'] - $a['appointmentCount'];
    });
    
    return $doctors;
}

// ============================================================================
// 3. WORST-FIT ALGORITHM - For Load Balancing
// ============================================================================
/**
 * Worst-Fit Algorithm: Assigns to the resource with most available capacity
 * Useful for balancing load across doctors
 * 
 * @return array - Doctor with most available capacity
 */
function worstFitDoctor() {
    global $con;
    
    $query = "SELECT d.id, d.username, d.name,
              COALESCE(COUNT(a.id), 0) as currentLoad,
              (COALESCE(COUNT(a.id), 0) * 30) as estimatedMinutes
              FROM doctb d 
              LEFT JOIN appointmenttb a ON d.id = a.docid 
              AND DATE(a.appointmentDate) = CURDATE()
              GROUP BY d.id 
              ORDER BY currentLoad ASC 
              LIMIT 1";
    
    $result = mysqli_query($con, $query);
    return mysqli_fetch_assoc($result);
}

// ============================================================================
// 4. OPTIMAL DOCTOR-PATIENT ASSIGNMENT (Hungarian Algorithm Simplified)
// ============================================================================
/**
 * Simplified Hungarian Algorithm: Finds optimal doctor assignment based on:
 * - Doctor specialization
 * - Current workload
 * - Patient requirements
 * - Availability
 * 
 * @param string $patientSpecialization - Required specialization
 * @return array - Ranked list of optimal doctors
 */
function hungarianOptimalAssignment($patientSpecialization = null) {
    global $con;
    
    $specializationFilter = "";
    if ($patientSpecialization) {
        $specializationFilter = "WHERE d.specialization LIKE '%$patientSpecialization%'";
    }
    
    // Calculate optimization score for each doctor
    $query = "SELECT d.id, d.username, d.name, d.specialization,
              COALESCE(COUNT(a.id), 0) as workload,
              COALESCE(AVG(r.rating), 0) as averageRating,
              d.availability,
              COALESCE(COUNT(DISTINCT DATE(a.appointmentDate)), 0) as daysWorked
              FROM doctb d 
              LEFT JOIN appointmenttb a ON d.id = a.docid 
              LEFT JOIN ratings r ON d.id = r.docid
              $specializationFilter
              GROUP BY d.id
              ORDER BY (COALESCE(AVG(r.rating), 3) * 0.4 + 
                       (1 / (COALESCE(COUNT(a.id), 1))) * 0.4 + 
                       d.availability * 0.2) DESC";
    
    $result = mysqli_query($con, $query);
    $doctors = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['optimizationScore'] = ($row['averageRating'] * 0.4) + 
                                    ((1 / ($row['workload'] + 1)) * 0.4) + 
                                    ($row['availability'] * 0.2);
        $doctors[] = $row;
    }
    
    return $doctors;
}

// ============================================================================
// 5. APPOINTMENT TIME SLOT OPTIMIZATION
// ============================================================================
/**
 * Finds optimal appointment time slots based on:
 * - Doctor availability
 * - Patient availability
 * - Current bookings
 * - Buffer time between appointments
 * 
 * @param int $doctorId - Doctor ID
 * @param string $preferredDate - Preferred date (YYYY-MM-DD)
 * @return array - Available time slots ranked by optimality
 */
function optimalTimeSlots($doctorId, $preferredDate) {
    global $con;
    
    $APPOINTMENT_DURATION = 30; // minutes
    $BUFFER_TIME = 15; // minutes between appointments
    
    // Get doctor's schedule
    $docQuery = "SELECT workStartTime, workEndTime FROM doctb WHERE id = $doctorId";
    $docResult = mysqli_query($con, $docQuery);
    $docSchedule = mysqli_fetch_assoc($docResult);
    
    // Get existing appointments for the day
    $appointQuery = "SELECT appointmentTime, duration FROM appointmenttb 
                     WHERE docid = $doctorId AND DATE(appointmentDate) = '$preferredDate'
                     ORDER BY appointmentTime ASC";
    
    $appointResult = mysqli_query($con, $appointQuery);
    $bookedSlots = array();
    
    while ($row = mysqli_fetch_assoc($appointResult)) {
        $bookedSlots[] = $row;
    }
    
    // Generate available slots
    $availableSlots = array();
    $startTime = strtotime($docSchedule['workStartTime']);
    $endTime = strtotime($docSchedule['workEndTime']);
    $currentTime = $startTime;
    $slotRank = 0;
    
    while ($currentTime + ($APPOINTMENT_DURATION * 60) <= $endTime) {
        $slotStart = date('H:i', $currentTime);
        $slotEnd = date('H:i', $currentTime + ($APPOINTMENT_DURATION * 60));
        
        // Check if slot conflicts with booked appointments
        $isAvailable = true;
        foreach ($bookedSlots as $booked) {
            $bookedStart = strtotime($booked['appointmentTime']);
            $bookedEnd = $bookedStart + ($booked['duration'] * 60) + ($BUFFER_TIME * 60);
            
            if (!($currentTime >= $bookedEnd || ($currentTime + ($APPOINTMENT_DURATION * 60)) <= $bookedStart)) {
                $isAvailable = false;
                break;
            }
        }
        
        if ($isAvailable) {
            $availableSlots[] = array(
                'time' => $slotStart,
                'endTime' => $slotEnd,
                'rank' => ++$slotRank,
                'preference' => calculateTimePreference($slotStart)
            );
        }
        
        $currentTime += ($APPOINTMENT_DURATION + $BUFFER_TIME) * 60;
    }
    
    return $availableSlots;
}

/**
 * Helper function: Calculate preference score for time slot
 * (Peak hours get lower preference)
 */
function calculateTimePreference($timeStr) {
    $hour = (int)date('H', strtotime($timeStr));
    
    // Prefer mid-morning (9-11) and early afternoon (14-15)
    if (($hour >= 9 && $hour < 11) || ($hour >= 14 && $hour < 15)) {
        return 1.0; // Highest preference
    } elseif ($hour >= 11 && $hour < 13) {
        return 0.8; // Medium preference
    } elseif ($hour >= 15 && $hour < 17) {
        return 0.9; // High preference
    } else {
        return 0.5; // Lower preference
    }
}

// ============================================================================
// 6. RESOURCE ALLOCATION - Best-Fit for Equipment/Room Assignment
// ============================================================================
/**
 * Allocates medical equipment/rooms using Best-Fit algorithm
 * Minimizes fragmentation of resources
 * 
 * @param int $requiredCapacity - Capacity needed
 * @param string $resourceType - Type of resource (room, equipment, etc.)
 * @return array - Best fitting resource
 */
function bestFitResourceAllocation($requiredCapacity, $resourceType) {
    global $con;
    
    $query = "SELECT * FROM resources 
              WHERE type = '$resourceType' 
              AND available = 1 
              AND capacity >= $requiredCapacity
              ORDER BY (capacity - $requiredCapacity) ASC 
              LIMIT 1";
    
    $result = mysqli_query($con, $query);
    return mysqli_fetch_assoc($result);
}

// ============================================================================
// 7. APPOINTMENT SCHEDULING OPTIMIZATION
// ============================================================================
/**
 * Comprehensive appointment scheduling using multiple algorithms
 * 
 * @param int $doctorId - Doctor to assign (null for automatic)
 * @param string $appointmentDate - Desired date
 * @param string $specializationNeeded - Required specialization
 * @return array - Optimized scheduling suggestion
 */
function optimizeAppointmentScheduling($doctorId = null, $appointmentDate, $specializationNeeded = null) {
    global $con;
    
    $suggestion = array(
        'doctors' => array(),
        'timeSlots' => array(),
        'recommendation' => null
    );
    
    // If doctor not specified, use Hungarian algorithm to find best match
    if (!$doctorId) {
        $doctors = hungarianOptimalAssignment($specializationNeeded);
        if (!empty($doctors)) {
            $suggestion['doctors'] = array_slice($doctors, 0, 3); // Top 3 suggestions
            $doctorId = $doctors[0]['id'];
        }
    }
    
    // Get optimal time slots for selected doctor
    if ($doctorId) {
        $slots = optimalTimeSlots($doctorId, $appointmentDate);
        $suggestion['timeSlots'] = $slots;
        
        if (!empty($slots)) {
            $suggestion['recommendation'] = array(
                'doctorId' => $doctorId,
                'preferredTime' => $slots[0]['time'],
                'confidence' => 'high'
            );
        }
    }
    
    return $suggestion;
}

// ============================================================================
// 8. PATIENT QUEUE OPTIMIZATION - Using Priority Queue Algorithm
// ============================================================================
/**
 * Optimizes patient queue based on:
 * - Appointment time
 * - Patient priority (emergency, etc.)
 * - Doctor availability
 * 
 * @return array - Sorted patient queue
 */
function optimizePatientQueue() {
    global $con;
    
    $query = "SELECT p.pid, p.fname, p.lname, p.contact,
              a.appointmentDate, a.appointmentTime, a.priority,
              d.id as docid, d.username
              FROM patreg p
              JOIN appointmenttb a ON p.pid = a.pid
              JOIN doctb d ON a.docid = d.id
              WHERE DATE(a.appointmentDate) = CURDATE()
              AND a.status = 'scheduled'
              ORDER BY a.priority DESC, a.appointmentTime ASC";
    
    $result = mysqli_query($con, $query);
    $queue = array();
    $priority_score = 1;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row['queue_priority'] = $priority_score++;
        $queue[] = $row;
    }
    
    return $queue;
}

// ============================================================================
// 9. LOAD BALANCING ACROSS DEPARTMENTS
// ============================================================================
/**
 * Distributes appointments across departments to balance workload
 * 
 * @param string $specialization - Department/Specialization
 * @return array - Doctors sorted by load (ascending)
 */
function balancedDepartmentAllocation($specialization) {
    global $con;
    
    $query = "SELECT d.id, d.username, d.name,
              COUNT(a.id) as current_load,
              d.max_patients_per_day,
              (d.max_patients_per_day - COUNT(a.id)) as available_slots,
              (d.max_patients_per_day - COUNT(a.id)) / d.max_patients_per_day as load_ratio
              FROM doctb d
              LEFT JOIN appointmenttb a ON d.id = a.docid 
              AND DATE(a.appointmentDate) = CURDATE()
              WHERE d.specialization LIKE '%$specialization%'
              GROUP BY d.id
              ORDER BY load_ratio DESC, current_load ASC";
    
    $result = mysqli_query($con, $query);
    $doctors = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $doctors[] = $row;
    }
    
    return $doctors;
}

// ============================================================================
// 10. ANALYTICS - Algorithm Performance Metrics
// ============================================================================
/**
 * Generates analytics on algorithm effectiveness
 * 
 * @return array - Performance metrics
 */
function getAlgorithmMetrics() {
    global $con;
    
    $metrics = array();
    
    // Average appointment wait time
    $waitQuery = "SELECT AVG(DATEDIFF(appointmentDate, createdAt)) as avg_wait_days 
                  FROM appointmenttb";
    $waitResult = mysqli_query($con, $waitQuery);
    $metrics['avg_wait_days'] = mysqli_fetch_assoc($waitResult)['avg_wait_days'];
    
    // Doctor utilization rate
    $utilQuery = "SELECT AVG((booked_slots / total_slots) * 100) as utilization_rate
                  FROM (SELECT COUNT(*) as booked_slots, 
                               MAX(max_patients_per_day) as total_slots
                        FROM doctb d
                        LEFT JOIN appointmenttb a ON d.id = a.docid
                        WHERE DATE(a.appointmentDate) = CURDATE()
                        GROUP BY d.id) as util";
    $utilResult = mysqli_query($con, $utilQuery);
    $metrics['utilization_rate'] = mysqli_fetch_assoc($utilResult)['utilization_rate'];
    
    // Patient satisfaction (if ratings exist)
    $satQuery = "SELECT AVG(rating) as avg_satisfaction FROM ratings";
    $satResult = mysqli_query($con, $satQuery);
    $metrics['avg_satisfaction'] = mysqli_fetch_assoc($satResult)['avg_satisfaction'];
    
    return $metrics;
}

?>
