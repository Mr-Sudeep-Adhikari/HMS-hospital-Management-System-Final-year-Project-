<?php
include("header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Patient Login - Global Hospitals</title>

    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">

    <style>
        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            min-height: 100vh;
            margin: 0;
        }

        /* Green Navbar */
        .navbar {
            background-color: #2e7d32 !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .navbar-brand h4, .nav-link h6 {
            color: white !important;
            font-weight: 600;
        }

        .nav-link:hover h6 {
            color: #c8e6c9 !important;
        }

        /* Main Content */
        .container-fluid {
            margin-top: 100px;
            margin-bottom: 100px;
        }

        /* Ambulance Animation */
        @keyframes mover {
            0% { transform: translateX(0); }
            100% { transform: translateX(30px); }
        }

        .ambulance-img {
            animation: mover 3s infinite alternate;
            width: 280px;
            margin-top: 100px;
        }

        .welcome-text h4 {
            color: #1b5e20;
            font-weight: 700;
            font-size: 2.8rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .welcome-text p {
            color: #2e7d32;
            font-size: 1.4rem;
        }

        /* Login Card - Green Style */
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(46, 125, 50, 0.2);
            overflow: hidden;
            border: none;
            margin-top: 50px;
        }

        .card-header {
            background: linear-gradient(45deg, #43a047, #66bb6a);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .card-header i {
            font-size: 4rem;
            margin-bottom: 15px;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .card-body {
            padding: 40px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #81c784;
            padding: 12px 18px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #43a047;
            box-shadow: 0 0 0 0.2rem rgba(67, 160, 71, 0.4);
        }

        label {
            font-weight: 600;
            color: #2e7d32;
            font-size: 1.1rem;
        }

        /* Green Login Button */
        #inputbtn {
            background: linear-gradient(45deg, #43a047, #66bb6a);
            border: none;
            color: white;
            padding: 12px 50px;
            border-radius: 30px;
            font-size: 1.2rem;
            font-weight: bold;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(67, 160, 71, 0.3);
        }

        #inputbtn:hover {
            background: linear-gradient(45deg, #388e3c, #4caf50);
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(67, 160, 71, 0.4);
        }

        .register-link {
            color: #43a047;
            font-weight: 600;
        }

        .register-link:hover {
            color: #2e7d32;
            text-decoration: underline;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <h4><i class="fa fa-user-plus"></i> GLOBAL HOSPITALS</h4>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><h6>HOME</h6></a></li>
                <li class="nav-item"><a class="nav-link" href="services.html"><h6>ABOUT US</h6></a></li>
                <li class="nav-item"><a class="nav-link" href="contact.html"><h6>CONTACT</h6></a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row align-items-center">

        <!-- Left Side - Welcome + Ambulance -->
        <div class="col-lg-7 text-center text-lg-left">
            <img src="images/ambulance1.png" alt="Ambulance" class="ambulance-img">
            <div class="welcome-text mt-4">
                <h4>We are here for you!</h4>
                <p>Your health is our priority. Login to manage your appointments.</p>
            </div>
        </div>

        <!-- Right Side - Login Card -->
        <div class="col-lg-5 col-xl-4 offset-xl-1">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-hospital-o"></i>
                    <h3>Patient Login</h3>
                </div>
                <div class="card-body">
                    <form class="form-group" method="POST" action="func.php">
                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label">Email ID</label>
                            <div class="col-sm-8">
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-4 col-form-label">Password</label>
                            <div class="col-sm-8">
                                <input type="password" name="password2" class="form-control" placeholder="Enter password" required>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" name="patsub" id="inputbtn" class="btn btn-success btn-lg">LOGIN</button>
                        </div>

                        <div class="text-center mt-3">
                            <p>New here? <a href="index.php" class="register-link">Register as Patient</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>