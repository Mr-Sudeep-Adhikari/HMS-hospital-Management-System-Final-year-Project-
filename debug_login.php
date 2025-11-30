<?php
require_once 'config.php';
$con = getDBConnection();

echo "<h1>Login Debugger</h1>";

function check_login($table, $user_col, $pass_col, $username, $password)
{
    global $con;
    echo "<h2>Checking $table</h2>";
    echo "Input Username: " . htmlspecialchars($username) . "<br>";
    echo "Input Password: " . htmlspecialchars($password) . "<br>";

    $query = "SELECT * FROM $table WHERE $user_col = '$username'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        echo "Query Failed: " . mysqli_error($con) . "<br>";
        return;
    }

    if (mysqli_num_rows($result) == 0) {
        echo "User not found in $table.<br>";
    } else {
        $row = mysqli_fetch_assoc($result);
        $stored_hash = $row[$pass_col];
        echo "Stored Hash: " . htmlspecialchars($stored_hash) . "<br>";
        echo "Hash Length: " . strlen($stored_hash) . "<br>";

        $verify = password_verify($password, $stored_hash);
        echo "password_verify('$password', '$stored_hash') = " . ($verify ? "TRUE" : "FALSE") . "<br>";

        $info = password_get_info($stored_hash);
        echo "Hash Info: <pre>" . print_r($info, true) . "</pre>";

        // Try trimming
        $trimmed_hash = trim($stored_hash);
        if ($stored_hash !== $trimmed_hash) {
            echo "<b>Warning: Stored hash has whitespace!</b><br>";
            $verify_trimmed = password_verify($password, $trimmed_hash);
            echo "password_verify with trimmed hash = " . ($verify_trimmed ? "TRUE" : "FALSE") . "<br>";
        }
    }
    echo "<hr>";
}

// Check Admin
check_login('admintb', 'username', 'password', 'admin', 'admin123');

// Check Doctor (Example)
// check_login('doctb', 'username', 'password', 'testdoctor', 'testpassword');

// Check Patient (Example)
// check_login('patreg', 'email', 'password', 'test@example.com', 'testpassword');
?>