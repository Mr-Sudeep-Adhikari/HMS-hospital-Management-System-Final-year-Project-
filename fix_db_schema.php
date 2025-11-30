<?php
require_once 'config.php';
$con = getDBConnection();

echo "<h1>Password Hash Fixer</h1>";

// Define tables and default passwords
$tables = [
    'admintb' => ['username' => 'admin', 'password' => 'admin123'],
    'doctb' => ['username' => null, 'password' => 'doc123'],
    'patreg' => ['username' => null, 'password' => 'pass123'],
];

// Loop through each table and update passwords
foreach ($tables as $table => $info) {
    echo "<h3>Updating $table...</h3>";

    $password = $info['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    if ($info['username']) {
        // Update single user (e.g., admin)
        $update = "UPDATE $table SET password='$hash' WHERE username='{$info['username']}'";
    } else {
        // Update all users in table
        $update = "UPDATE $table SET password='$hash'";
    }

    if (mysqli_query($con, $update)) {
        echo "✅ Passwords in <b>$table</b> updated successfully.<br>";
    } else {
        echo "❌ Failed to update $table: " . mysqli_error($con) . "<br>";
    }
}

echo "<h2>All passwords are now valid bcrypt hashes.</h2>";
echo "<p>You can now log in with the following credentials:</p>";
echo "<ul>
    <li><b>Admin:</b> admin / admin123</li>
    <li><b>Doctors:</b> all set to doc123</li>
    <li><b>Patients:</b> all set to pass123</li>
</ul>";
?>