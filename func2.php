<?php
// =============================================================================
// PATIENT REGISTRATION HANDLER - SECURE VERSION
// =============================================================================
require_once 'config.php';

startSecureSession();
$con = getDBConnection();

// =============================================================================
// PATIENT REGISTRATION
// =============================================================================
if (isset($_POST['patsub1'])) {
  $fname = sanitizeInput($_POST['fname']);
  $lname = sanitizeInput($_POST['lname']);
  $gender = sanitizeInput($_POST['gender']);
  $email = sanitizeInput($_POST['email']);
  $contact = sanitizeInput($_POST['contact']);
  $password = $_POST['password'];
  $cpassword = $_POST['cpassword'];

  // Validate inputs
  if (empty($fname) || empty($lname) || empty($email) || empty($password)) {
    alertAndRedirect('All fields are required!', 'index.php');
  }

  if (!validateEmail($email)) {
    alertAndRedirect('Invalid email format!', 'index.php');
  }

  if (!validatePhone($contact)) {
    alertAndRedirect('Invalid phone number! Must be 10 digits.', 'index.php');
  }

  if (strlen($password) < PASSWORD_MIN_LENGTH) {
    alertAndRedirect('Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long!', 'index.php');
  }

  if ($password == $cpassword) {
    // SECURE: Hash password
    $hashedPassword = hashPassword($password);

    // Check if email already exists
    $checkQuery = "SELECT pid FROM patreg WHERE email = ?";
    $checkStmt = executeQuery($con, $checkQuery, "s", [$email]);

    if ($checkStmt) {
      $checkResult = mysqli_stmt_get_result($checkStmt);
      if (mysqli_num_rows($checkResult) > 0) {
        mysqli_stmt_close($checkStmt);
        alertAndRedirect('Email already registered! Please login.', 'index.php');
      }
      mysqli_stmt_close($checkStmt);
    }

    // SECURE: Use prepared statement
    $query = "INSERT INTO patreg(fname, lname, gender, email, contact, password, cpassword) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = executeQuery($con, $query, "sssssss", [$fname, $lname, $gender, $email, $contact, $hashedPassword, $hashedPassword]);

    if ($stmt) {
      // Get the inserted patient ID
      $pid = mysqli_insert_id($con);

      // Set session variables
      $_SESSION['pid'] = $pid;
      $_SESSION['username'] = $fname . " " . $lname;
      $_SESSION['fname'] = $fname;
      $_SESSION['lname'] = $lname;
      $_SESSION['gender'] = $gender;
      $_SESSION['contact'] = $contact;
      $_SESSION['email'] = $email;
      $_SESSION['user_type'] = 'patient';
      $_SESSION['last_activity'] = time();

      mysqli_stmt_close($stmt);

      // FIXED: Redirect to correct patient panel
      alertAndRedirect('Registration successful! Welcome to ' . APP_NAME, 'patient_panel.php');
    } else {
      alertAndRedirect('Registration failed! Please try again.', 'index.php');
    }
  } else {
    alertAndRedirect('Passwords do not match!', 'index.php');
  }
}

// =============================================================================
// UPDATE PAYMENT STATUS (Receptionist) - Duplicate from func.php
// =============================================================================
// COMMENTED OUT: This is duplicate code from func.php
/*
if(isset($_POST['update_data']))
{
	$contact=$_POST['contact'];
	$status=$_POST['status'];
	$query="update appointmenttb set payment='$status' where contact='$contact';";
	$result=mysqli_query($con,$query);
	if($result)
		header("Location:updated.php");
}
*/

// =============================================================================
// DISPLAY DOCTORS FUNCTION (UNUSED - Moved to func1.php)
// =============================================================================
// COMMENTED OUT: This function is duplicated in func1.php which has better implementation
// function display_docs()
// {
// 	global $con;
// 	$query="select * from doctb";
// 	$result=mysqli_query($con,$query);
// 	while($row=mysqli_fetch_array($result))
// 	{
// 		$name=$row['name'];
// 		# echo'<option value="" disabled selected>Select Doctor</option>';
// 		echo '<option value="'.$name.'">'.$name.'</option>';
// 	}
// }

// =============================================================================
// ADD DOCTOR FUNCTION (UNUSED - Duplicate from func.php)
// =============================================================================
// COMMENTED OUT: This is duplicate code from func.php
/*
if(isset($_POST['doc_sub']))
{
	$name=$_POST['name'];
	$query="insert into doctb(name)values('$name')";
	$result=mysqli_query($con,$query);
	if($result)
		header("Location:adddoc.php");
}
*/

// =============================================================================
// DISPLAY ADMIN PANEL FUNCTION (UNUSED - COMMENTED OUT)
// =============================================================================
// COMMENTED OUT: This entire function is not used anywhere and contains 200+ lines of HTML
// This is duplicate code that should be in separate view files
// Same as in func.php - removed to reduce file size
/*
function display_admin_panel(){
	// ... 200+ lines of HTML code ...
}
*/
?>