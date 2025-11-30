<?php
// =============================================================================
// DOCTOR PANEL - SECURE VERSION
// =============================================================================
require_once 'config.php';
require_once 'func1.php'; // For display_docs function

startSecureSession();
$con = getDBConnection();

// Session validation - redirect if not logged in as doctor
if (!isset($_SESSION['dname']) || $_SESSION['user_type'] != 'doctor') {
    alertAndRedirect('Please login as doctor to access this page!', 'doctor-login.php');
}

// Check session timeout
if (!isSessionValid()) {
    destroySession();
    alertAndRedirect('Session expired. Please login again!', 'doctor-login.php');
}

// Get doctor information from session
$doctor = $_SESSION['doctor'];
$dname = $_SESSION['dname'];
$spec = $_SESSION['spec'] ?? 'Doctor';

// =============================================================================
// CANCEL APPOINTMENT
// =============================================================================
