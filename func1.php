<?php
// =============================================================================
// DOCTOR LOGIN HANDLER - SECURE VERSION
// =============================================================================
require_once 'config.php';

startSecureSession();
$con = getDBConnection();

// =============================================================================
// DOCTOR LOGIN
// =============================================================================
if (isset($_POST['docsub1'])) {
    $username = trim(sanitizeInput($_POST['username3']));
    $password = trim(sanitizeInput($_POST['password3']));

    // Validate inputs
    if (empty($username) || empty($password)) {
        alertAndRedirect('Username and password are required!', 'index.php');
    }

    // SECURE: Use prepared statement to prevent SQL injection
    $query = "SELECT username, password, spec, email FROM doctb WHERE username = ?";
    $stmt = executeQuery($con, $query, "s", [$username]);

    if ($stmt) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // SECURE: Verify password (supports both hashed and plain text for migration)
            $passwordValid = false;
            $passwordInfo = password_get_info($row['password']);

            // FIXED: Check if algo is 0 (plain text) or null (plain text)
            // algo = 0 means no hashing algorithm, algo = 1 means bcrypt
            if ($passwordInfo['algo'] !== 0 && $passwordInfo['algo'] !== null) {
                // Password is hashed - use password_verify
                $passwordValid = verifyPassword($password, $row['password']);
            } else {
                // Plain text password - direct comparison
                $passwordValid = ($password === $row['password']);

                // Auto-upgrade to hashed password
                if ($passwordValid) {
                    $hashedPassword = hashPassword($password);
                    $updateQuery = "UPDATE doctb SET password = ? WHERE username = ?";
                    executeQuery($con, $updateQuery, "ss", [$hashedPassword, $username]);
                }
            }

            if ($passwordValid) {
                // Set session variables
                $_SESSION['dname'] = $row['username'];
                $_SESSION['doctor'] = $row['username'];
                $_SESSION['spec'] = $row['spec'] ?? 'Doctor';
                $_SESSION['demail'] = $row['email'] ?? '';
                $_SESSION['user_type'] = 'doctor';
                $_SESSION['last_activity'] = time();

                redirectTo('doctor-panel.php');
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

// =============================================================================
// DISPLAY DOCTORS FUNCTION (For dropdown in appointment booking)
// =============================================================================
function display_docs()
{
    global $con;
    $query = "SELECT username, docFees, spec FROM doctb ORDER BY username";
    $result = mysqli_query($con, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $name = htmlspecialchars($row['username'], ENT_QUOTES);
            $fee = htmlspecialchars($row['docFees'] ?? '0', ENT_QUOTES);
            $spec = htmlspecialchars($row['spec'] ?? 'General', ENT_QUOTES);
            echo '<option value="' . $name . '" data-value="' . $fee . '" data-spec="' . $spec . '">Dr. ' . $name . ' - रु.' . $fee . ' (' . $spec . ')</option>';
        }
    }
}
?>