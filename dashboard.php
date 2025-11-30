<?php
session_start();
include('header.php');
include('func.php');

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - KMC Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <style>
        body { font-family: 'IBM Plex Sans', sans-serif; }
        .btn-green { background: linear-gradient(135deg, #10b981 0%, #6ee7b7 100%); }
        .nav-green { background: linear-gradient(135deg, #10b981 0%, #6ee7b7 100%); }
    </style>
</head>
<body class="bg-gradient-to-br from-green-500 to-green-300">
    <nav class="navbar navbar-expand-lg navbar-dark nav-green fixed top-0 w-full z-50">
        <div class="max-w-7xl mx-auto w-full px-4 py-3">
            <a class="text-white font-bold text-lg" href="dashboard.php"><i class="fa fa-hospital-o"></i> KMC HOSPITAL</a>
            <button class="md:hidden text-white ml-auto" type="button" onclick="document.getElementById('navMenu').classList.toggle('hidden')">
                <i class="fa fa-bars"></i>
            </button>
            <div class="hidden md:flex md:items-center md:justify-end gap-6 absolute right-4 top-1/2 -translate-y-1/2" id="navMenu">
                <a class="text-white hover:opacity-80" href="dashboard.php">Dashboard</a>
                <a class="text-white hover:opacity-80" href="book-appointment.php">Book Appointment</a>
                <a class="text-white hover:opacity-80" href="appointment-history.php">My Appointments</a>
                <a class="text-white hover:opacity-80" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-24 mb-20 px-4">
        <div class="mb-8">
            <h2 class="text-white text-4xl font-bold">Welcome, <?php echo ucfirst($fname) . ' ' . ucfirst($lname); ?>!</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="text-5xl text-green-600 mb-4"><i class="fa fa-calendar"></i></div>
                <h5 class="text-green-700 font-bold text-lg mb-2">Book Appointment</h5>
                <p class="text-gray-600 mb-4">Schedule a new appointment with our doctors</p>
                <a href="book-appointment.php" class="btn-green text-white px-6 py-2 rounded-lg inline-block hover:opacity-90">Book Now</a>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="text-5xl text-green-600 mb-4"><i class="fa fa-list"></i></div>
                <h5 class="text-green-700 font-bold text-lg mb-2">My Appointments</h5>
                <p class="text-gray-600 mb-4">View and manage your appointments</p>
                <a href="appointment-history.php" class="btn-green text-white px-6 py-2 rounded-lg inline-block hover:opacity-90">View</a>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="text-5xl text-green-600 mb-4"><i class="fa fa-heartbeat"></i></div>
                <h5 class="text-green-700 font-bold text-lg mb-2">Medical Records</h5>
                <p class="text-gray-600 mb-4">Access your medical prescriptions and records</p>
                <a href="admin-panel.php" class="btn-green text-white px-6 py-2 rounded-lg inline-block hover:opacity-90">View</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
</body>
</html>
