<?php
// Test database connection and doctor account
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!$con) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

echo "✅ Database connected successfully!<br><br>";

// Check if doctor account exists
$query = "SELECT * FROM doctb WHERE username='doctor' AND password='password123'";
$result = mysqli_query($con, $query);

echo "<h3>Doctor Account Test:</h3>";
echo "Query: " . $query . "<br><br>";

if (mysqli_num_rows($result) == 1) {
    echo "✅ <strong>Doctor account found!</strong><br><br>";
    $row = mysqli_fetch_assoc($result);
    echo "<pre>";
    print_r($row);
    echo "</pre>";
    echo "<br><a href='doctor-login.php' style='background:#0d9488; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Go to Doctor Login</a>";
} else {
    echo "❌ <strong>Doctor account NOT found!</strong><br>";
    echo "Rows found: " . mysqli_num_rows($result) . "<br><br>";

    // Show all doctors in database
    $all_query = "SELECT username, email, spec FROM doctb";
    $all_result = mysqli_query($con, $all_query);
    echo "<h4>All doctors in database:</h4>";
    while ($doc = mysqli_fetch_assoc($all_result)) {
        echo "Username: " . $doc['username'] . ", Email: " . $doc['email'] . "<br>";
    }
}

mysqli_close($con);
?>