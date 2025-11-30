<?php
// =============================================================================
// DATABASE CONFIGURATION - CENTRALIZED
// =============================================================================
// This file contains all database connection settings
// Include this file in all PHP files that need database access

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'myhmsdb');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'KMC Hospital');
define('APP_VERSION', '2.0');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Security Configuration
define('PASSWORD_MIN_LENGTH', 6);

// =============================================================================
// DATABASE CONNECTION FUNCTION
// =============================================================================
function getDBConnection()
{
    static $conn = null;

    if ($conn === null) {
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if (!$conn) {
            error_log("Database Connection Failed: " . mysqli_connect_error());
            die("<div style='background:#fee; color:#c00; padding:20px; border-radius:10px; font-family:Arial; text-align:center;'>
                 <h3>Database Connection Error</h3>
                 <p>Unable to connect to the database. Please contact the administrator.</p>
                 </div>");
        }

        // Set charset to prevent encoding issues
        mysqli_set_charset($conn, DB_CHARSET);
    }

    return $conn;
}

// =============================================================================
// SECURE QUERY EXECUTION FUNCTIONS
// =============================================================================

/**
 * Execute a prepared statement safely
 * @param mysqli $conn Database connection
 * @param string $query SQL query with placeholders
 * @param string $types Parameter types (e.g., "ss" for two strings)
 * @param array $params Parameters to bind
 * @return mysqli_stmt|false Statement object or false on failure
 */
function executeQuery($conn, $query, $types = "", $params = [])
{
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        error_log("Query Preparation Failed: " . mysqli_error($conn));
        return false;
    }

    if (!empty($types) && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
        error_log("Query Execution Failed: " . mysqli_stmt_error($stmt));
        return false;
    }

    return $stmt;
}

// =============================================================================
// SESSION MANAGEMENT FUNCTIONS
// =============================================================================

/**
 * Start session securely
 */
function startSecureSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

        session_start();

        // Regenerate session ID periodically to prevent session fixation
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Check if session is valid and not expired
 * @return bool
 */
function isSessionValid()
{
    if (!isset($_SESSION['last_activity'])) {
        return false;
    }

    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        return false;
    }

    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Destroy session completely
 */
function destroySession()
{
    session_start();
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

// =============================================================================
// INPUT SANITIZATION FUNCTIONS
// =============================================================================

/**
 * Sanitize string input
 * @param string $input
 * @return string
 */
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Validate email address
 * @param string $email
 * @return bool
 */
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (10 digits)
 * @param string $phone
 * @return bool
 */
function validatePhone($phone)
{
    return preg_match('/^[0-9]{10}$/', $phone);
}

// =============================================================================
// PASSWORD SECURITY FUNCTIONS
// =============================================================================

/**
 * Hash password securely
 * @param string $password
 * @return string
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password against hash
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

// =============================================================================
// REDIRECT FUNCTIONS
// =============================================================================

/**
 * Redirect to a page
 * @param string $page
 */
function redirectTo($page)
{
    header("Location: " . $page);
    exit();
}

/**
 * Show alert and redirect
 * @param string $message
 * @param string $page
 */
function alertAndRedirect($message, $page)
{
    echo "<script>
        alert('" . addslashes($message) . "');
        window.location.href = '" . $page . "';
    </script>";
    exit();
}

// =============================================================================
// ERROR LOGGING
// =============================================================================
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

?>