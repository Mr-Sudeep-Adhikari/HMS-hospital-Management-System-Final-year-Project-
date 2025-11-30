<?php
// =============================================================================
// ADMIN/RECEPTIONIST LOGIN HANDLER - SECURE VERSION
// =============================================================================
// require_once 'config.php';

// startSecureSession();
// $con = getDBConnection();

// // =============================================================================
// // ADMIN/RECEPTIONIST LOGIN
// // =============================================================================
// if (isset($_POST['adsub'])) {
// 	$username = sanitizeInput($_POST['username1']);
// 	$password = $_POST['password2'];

// 	// Validate inputs
// 	if (empty($username) || empty($password)) {
// 		alertAndRedirect('Username and password are required!', 'index.php');
// 	}

// 	// SECURE: Use prepared statement to prevent SQL injection
// 	$query = "SELECT username, password FROM admintb WHERE username = ?";
// 	$stmt = executeQuery($con, $query, "s", [$username]);

// 	if ($stmt) {
// 		$result = mysqli_stmt_get_result($stmt);

// 		if (mysqli_num_rows($result) == 1) {
// 			$row = mysqli_fetch_assoc($result);
// 			$hash = trim($row['password']);

// 			// SECURE: Verify password (supports both hashed and plain text for migration)
// 			// $passwordValid = false;
// 			// $passwordInfo = password_get_info($row['password']);

// 			// // FIXED: Check if algo is 0 (plain text) or null (plain text)
// 			// if ($passwordInfo['algo'] !== 0 && $passwordInfo['algo'] !== null) {
// 			// 	// Password is hashed
// 			// 	$passwordValid = verifyPassword($password, $row['password']);
// 			// } else {
// 			// 	// Legacy plain text password (for backward compatibility during migration)
// 			// 	$passwordValid = ($password === $row['password']);

// 			// 	// Auto-upgrade to hashed password
// 			// 	if ($passwordValid) {
// 			// 		$hashedPassword = hashPassword($password);
// 			// 		$updateQuery = "UPDATE admintb SET password = ? WHERE username = ?";
// 			// 		executeQuery($con, $updateQuery, "ss", [$hashedPassword, $username]);
// 			// 	}
// 			// }

// 			// if ($passwordValid) {
// 			if (password_verify($password, $hash)) {
// 				// Set session variables
// 				$_SESSION['username'] = $username;
// 				$_SESSION['admin'] = $username;
// 				$_SESSION['user_type'] = 'admin';
// 				$_SESSION['last_activity'] = time();

// 				redirectTo('admin-panel1.php');
// 			} else {
// 				alertAndRedirect('Invalid username or password. Try again!', 'index.php');
// 			}
// 		} else {
// 			alertAndRedirect('Invalid username or password. Try again!', 'index.php');
// 		}

// 		mysqli_stmt_close($stmt);
// 	} else {
// 		alertAndRedirect('System error. Please try again later.', 'index.php');
// 	}
// }

require_once 'config.php';

startSecureSession();
$con = getDBConnection();

if (isset($_POST['adsub'])) {
	$username = trim(sanitizeInput($_POST['username1']));
	$password = trim($_POST['password2']);

	if (!$username || !$password) {
		alertAndRedirect('Username and password are required!', 'index.php');
	}

	$stmt = executeQuery($con, "SELECT username, password FROM admintb WHERE username = ?", "s", [$username]);
	if ($stmt) {
		$result = mysqli_stmt_get_result($stmt);
		if ($row = mysqli_fetch_assoc($result)) {
			$hash = trim($row['password']); // remove any whitespace

			if (verifyPassword($password, $hash)) {
				$_SESSION['username'] = $username;
				$_SESSION['admin'] = $username;
				$_SESSION['user_type'] = 'admin';
				$_SESSION['last_activity'] = time();
				redirectTo('admin-panel1.php');
			} else {
				alertAndRedirect('Invalid username or password!', 'index.php');
			}
		} else {
			alertAndRedirect('Invalid username or password!', 'index.php');
		}
		mysqli_stmt_close($stmt);
	} else {
		alertAndRedirect('System error. Please try again later.', 'index.php');
	}
}

?>