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

if (isset($_POST['app-submit'])) {
    $pid = $_SESSION['pid'] ?? 0;
    $email = mysqli_real_escape_string($con, $_SESSION['email']);
    $contact = mysqli_real_escape_string($con, $_SESSION['contact']);
    $doctor = mysqli_real_escape_string($con, $_POST['doctor']);
    $docFees = mysqli_real_escape_string($con, $_POST['docFees']);
    $appdate = mysqli_real_escape_string($con, $_POST['appdate']);
    $apptime = mysqli_real_escape_string($con, $_POST['apptime']);

    $cur_date = date("Y-m-d");
    date_default_timezone_set('Asia/Kathmandu');
    $cur_time = date("H:i:s");
    $apptime1 = strtotime($apptime);
    $appdate1 = strtotime($appdate);

    if (date("Y-m-d", $appdate1) >= $cur_date) {
        if ((date("Y-m-d", $appdate1) == $cur_date && date("H:i:s", $apptime1) > $cur_time) || date("Y-m-d", $appdate1) > $cur_date) {
            $check_query = mysqli_query($con, "select apptime from appointmenttb where doctor='$doctor' and appdate='$appdate' and apptime='$apptime'");

            if (mysqli_num_rows($check_query) == 0) {
                $query = mysqli_query($con, "insert into appointmenttb(pid,fname,lname,gender,email,contact,doctor,docFees,appdate,apptime,userStatus,doctorStatus) values($pid,'$fname','$lname','" . $_SESSION['gender'] . "','$email','$contact','$doctor','$docFees','$appdate','$apptime','1','1')");

                if ($query) {
                    echo "<script>alert('Your appointment successfully booked'); window.location.href='appointment-history.php';</script>";
                } else {
                    echo "<script>alert('Unable to process your request. Please try again!'); window.location.href='book-appointment.php';</script>";
                }
            } else {
                echo "<script>alert('We are sorry to inform that the doctor is not available in this time or date. Please choose different time or date!');</script>";
            }
        } else {
            echo "<script>alert('Select a time or date in the future!');</script>";
        }
    } else {
        echo "<script>alert('Select a time or date in the future!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Book Appointment - KMC Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'IBM Plex Sans', sans-serif;
        }

        .btn-green {
            background: linear-gradient(135deg, #10b981 0%, #6ee7b7 100%);
        }

        .nav-green {
            background: linear-gradient(135deg, #10b981 0%, #6ee7b7 100%);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-green-500 to-green-300">
    <nav class="nav-green fixed top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <a class="text-white font-bold text-lg" href="dashboard.php"><i class="fa fa-hospital-o"></i> KMC
                HOSPITAL</a>
            <div class="flex gap-6">
                <a class="text-white hover:opacity-80" href="dashboard.php">Dashboard</a>
                <a class="text-white hover:opacity-80" href="book-appointment.php">Book Appointment</a>
                <a class="text-white hover:opacity-80" href="appointment-history.php">My Appointments</a>
                <a class="text-white hover:opacity-80" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto mt-32 mb-20 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h3 class="text-2xl font-bold text-green-700 mb-8">Book an Appointment</h3>
            <form method="POST" action="book-appointment.php">
                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Select Specialization</label>
                    <select
                        class="w-full px-4 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:border-green-500"
                        id="spec" name="spec" required>
                        <option value="">Choose Specialization</option>
                        <?php display_specs(); ?>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Select Doctor</label>
                    <select
                        class="w-full px-4 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:border-green-500"
                        id="doctor" name="doctor" required>
                        <option value="">Choose Doctor</option>
                        <?php display_docs(); ?>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Consultancy Fees</label>
                    <input type="text"
                        class="w-full px-4 py-2 border-2 border-green-200 rounded-lg bg-gray-100 cursor-not-allowed"
                        id="docFees" name="docFees" readonly>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Select Date</label>
                    <input type="date"
                        class="w-full px-4 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:border-green-500"
                        id="appdate" name="appdate" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Select Time</label>
                    <select
                        class="w-full px-4 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:border-green-500"
                        id="apptime" name="apptime" required>
                        <option value="">Choose Time</option>
                        <option value="08:00:00">8:00 AM</option>
                        <option value="10:00:00">10:00 AM</option>
                        <option value="12:00:00">12:00 PM</option>
                        <option value="14:00:00">2:00 PM</option>
                        <option value="16:00:00">4:00 PM</option>
                    </select>
                </div>

                <button type="submit" name="app-submit"
                    class="btn-green text-white w-full py-3 rounded-lg font-semibold hover:opacity-90 mb-4">Book
                    Appointment</button>
                <a href="dashboard.php"
                    class="block text-center border-2 border-green-500 text-green-600 py-3 rounded-lg font-semibold hover:bg-green-50">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('spec').addEventListener('change', function () {
            let spec = this.value;
            let docs = [...document.getElementById('doctor').options];
            docs.forEach((el) => {
                el.style.display = el.getAttribute('data-spec') == spec ? 'block' : 'none';
            });
        });

        document.getElementById('doctor').addEventListener('change', function () {
            let selection = document.querySelector(`[value="${this.value}"]`).getAttribute('data-value');
            document.getElementById('docFees').value = selection;
        });
    </script>
</body>

</html>