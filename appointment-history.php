<?php
session_start();
include('header.php');

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "hospitaldatabase");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$fname = $_SESSION['fname'] ?? '';
$lname = $_SESSION['lname'] ?? '';

if (isset($_GET['cancel']) && isset($_GET['ID'])) {
    $id = mysqli_real_escape_string($con, $_GET['ID']);
    $query = mysqli_query($con, "update appointmenttb set userStatus='0' where ID = '$id'");
    if ($query) {
        echo "<script>alert('Your appointment successfully cancelled'); window.location.href='appointment-history.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Appointment History - KMC Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <style>
        body { font-family: 'IBM Plex Sans', sans-serif; }
        .nav-green { background: linear-gradient(135deg, #10b981 0%, #6ee7b7 100%); }
    </style>
</head>
<body class="bg-gradient-to-br from-green-500 to-green-300">
    <nav class="nav-green fixed top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a class="text-white font-bold text-lg" href="dashboard.php"><i class="fa fa-hospital-o"></i> KMC HOSPITAL</a>
            <div class="flex gap-6">
                <a class="text-white hover:opacity-80" href="dashboard.php">Dashboard</a>
                <a class="text-white hover:opacity-80" href="book-appointment.php">Book Appointment</a>
                <a class="text-white hover:opacity-80" href="appointment-history.php">My Appointments</a>
                <a class="text-white hover:opacity-80" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-28 mb-20 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h3 class="text-2xl font-bold text-green-700 mb-8">My Appointments</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="nav-green text-white">
                            <th class="px-4 py-3 text-left">Doctor Name</th>
                            <th class="px-4 py-3 text-left">Consultancy Fees</th>
                            <th class="px-4 py-3 text-left">Appointment Date</th>
                            <th class="px-4 py-3 text-left">Appointment Time</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                                    <?php
                                    $query = "select ID,doctor,docFees,appdate,apptime,userStatus,doctorStatus from appointmenttb where fname ='$fname' and lname='$lname' ORDER BY appdate DESC;";
                                    $result = mysqli_query($con, $query);
                                    
                                    if (mysqli_num_rows($result) == 0) {
                                        echo '<tr><td colspan="6" class="text-center py-8 text-gray-600">No appointments found. <a href="book-appointment.php" class="text-green-600 hover:text-green-800 font-semibold">Book one now!</a></td></tr>';
                                    }
                                    
                                    while ($row = mysqli_fetch_array($result)) {
                                        $status = '';
                                        $statusClass = '';
                                        
                                        if ($row['userStatus'] == 1 && $row['doctorStatus'] == 1) {
                                            $status = 'Active';
                                            $statusClass = 'bg-green-100 text-green-800';
                                        } elseif ($row['userStatus'] == 0 && $row['doctorStatus'] == 1) {
                                            $status = 'Cancelled by You';
                                            $statusClass = 'bg-red-100 text-red-800';
                                        } elseif ($row['userStatus'] == 1 && $row['doctorStatus'] == 0) {
                                            $status = 'Cancelled by Doctor';
                                            $statusClass = 'bg-red-100 text-red-800';
                                        } else {
                                            $status = 'Cancelled';
                                            $statusClass = 'bg-red-100 text-red-800';
                                        }
                                    ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($row['doctor']); ?></td>
                                            <td class="px-4 py-3">RS<?php echo htmlspecialchars($row['docFees']); ?></td>
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($row['appdate']); ?></td>
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($row['apptime']); ?></td>
                                            <td class="px-4 py-3"><span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                                            <td class="px-4 py-3 text-center">
                                                <?php
                                                if ($row['userStatus'] == 1 && $row['doctorStatus'] == 1) {
                                                    echo '<a href="appointment-history.php?ID=' . $row['ID'] . '&cancel=update" onclick="return confirm(\'Are you sure you want to cancel this appointment?\')" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 inline-block">Cancel</a>';
                                                } else {
                                                    echo '<span class="text-gray-400 text-sm">N/A</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="dashboard.php" class="mt-8 inline-block border-2 border-green-500 text-green-600 px-6 py-2 rounded-lg font-semibold hover:bg-green-50">Back to Dashboard</a>
                    </div>
                </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
</body>
</html>
