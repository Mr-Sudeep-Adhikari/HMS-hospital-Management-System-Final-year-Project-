<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "hospitaldatabase");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['entry_submit'])) {
    // Get and sanitize input data
    $fname = mysqli_real_escape_string($con, $_POST['fname']);
    $lname = mysqli_real_escape_string($con, $_POST['lname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $doctor = mysqli_real_escape_string($con, $_POST['doctor']);
    $payment = mysqli_real_escape_string($con, $_POST['payment']);
    
    // Generate appointment date and time if not provided
    $appdate = date('Y-m-d', strtotime('+1 day'));
    $apptime = isset($_POST['apptime']) ? mysqli_real_escape_string($con, $_POST['apptime']) : '10:00 AM';
    $userStatus = '1';
    $doctorStatus = '0';
    
    // Insert appointment into database
    $query = "INSERT INTO appointmenttb(fname, lname, email, contact, doctor, appdate, apptime, payment, userStatus, doctorStatus) 
              VALUES ('$fname', '$lname', '$email', '$contact', '$doctor', '$appdate', '$apptime', '$payment', '$userStatus', '$doctorStatus')";
    
    $result = mysqli_query($con, $query);
    
    if ($result) {
        // Get the inserted appointment ID
        $appointmentId = mysqli_insert_id($con);
        
        echo "<script>
                alert('Appointment booked successfully! Your appointment ID is: $appointmentId');
                window.location.href = 'admin-panel.php';
              </script>";
    } else {
        echo "<script>
                alert('Error booking appointment: " . mysqli_error($con) . "');
                window.location.href = 'admin-panel.php';
              </script>";
    }
} else {
    header("Location: admin-panel.php");
}

mysqli_close($con);
?>
