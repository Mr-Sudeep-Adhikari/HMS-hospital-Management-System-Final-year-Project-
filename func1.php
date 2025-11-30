<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

// DOCTOR LOGIN
if (isset($_POST['docsub1'])) {
    $username = $_POST['username3'];
    $password = $_POST['password3'];

    // Query for doctor login
    $query = "SELECT username, spec FROM doctb WHERE username='$username' AND password='$password'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);
        $_SESSION['dname'] = $row['username'];
        $_SESSION['doctor'] = $row['username'];
        $_SESSION['spec'] = $row['spec'] ?? 'Doctor';

        header("Location: doctor-panel.php");
        exit();
    } else {
        echo "<script>
            alert('Invalid username or password!');
            window.location = 'doctor-login.php';
        </script>";
        exit();
    }
}

// Function to display doctors in dropdown
function display_docs()
{
    global $con;
    $query = "SELECT username FROM doctb";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result)) {
        echo '<option value="' . $row['username'] . '">' . $row['username'] . '</option>';
    }
}
?>