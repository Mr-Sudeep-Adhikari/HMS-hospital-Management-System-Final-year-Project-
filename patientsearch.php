<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Details | KMC Hospital</title>
  <link rel="shortcut icon" href="images/favicon.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
    .gradient-bg {
      background: linear-gradient(135deg, #064e3b 0%, #059669 35%, #10b981 100%);
      background-size: 200% 200%;
      animation: gradient 12s ease infinite;
    }

    @keyframes gradient {

      0%,
      100% {
        background-position: 0% 50%
      }

      50% {
        background-position: 100% 50%
      }
    }

    .card {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .btn {
      background: linear-gradient(to right, #059669, #10b981);
    }

    .btn:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 40px rgba(5, 150, 105, 0.4);
    }
  </style>
</head>

<body class="gradient-bg min-h-screen text-gray-800 p-6">

  <?php
  include("newfunc.php");
  if (isset($_POST['patient_search_submit'])) {
    $contact = $_POST['patient_contact'];
    // SECURE: Use prepared statement
    $query = "SELECT * FROM patreg WHERE contact = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $contact);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
      echo "<script> alert('No entries found! Please enter valid details'); window.location.href = 'admin-panel1.php#list-doc';</script>";
    } else {
      echo "<div class='max-w-6xl mx-auto mt-20 animate-fade-in-up'>
        <div class='card rounded-3xl shadow-2xl overflow-hidden'>
            <div class='bg-emerald-100 p-8 border-b border-emerald-200 flex justify-between items-center'>
                <h2 class='text-3xl font-extrabold text-emerald-800'>Patient Search Results</h2>
                <a href='admin-panel1.php' class='bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg'>
                    <i class='fas fa-arrow-left mr-2'></i> Back to Dashboard
                </a>
            </div>
            <div class='p-8 overflow-x-auto'>
                <table class='w-full text-left border-collapse'>
                    <thead class='bg-emerald-50 text-emerald-800'>
                        <tr>
                            <th class='px-6 py-4 rounded-l-xl'>First Name</th>
                            <th class='px-6 py-4'>Last Name</th>
                            <th class='px-6 py-4'>Email</th>
                            <th class='px-6 py-4'>Contact</th>
                            <th class='px-6 py-4 rounded-r-xl'>Password</th>
                        </tr>
                    </thead>
                    <tbody>";

      while ($row = $result->fetch_assoc()) {
        $fname = htmlspecialchars($row['fname']);
        $lname = htmlspecialchars($row['lname']);
        $email = htmlspecialchars($row['email']);
        $contact = htmlspecialchars($row['contact']);
        $password = htmlspecialchars($row['password']);
        echo "<tr class='border-b hover:bg-emerald-50 transition'>
                <td class='px-6 py-4 font-bold'>$fname</td>
                <td class='px-6 py-4'>$lname</td>
                <td class='px-6 py-4'>$email</td>
                <td class='px-6 py-4'>$contact</td>
                <td class='px-6 py-4'>$password</td>
            </tr>";
      }
      echo "</tbody></table></div></div></div>";
    }
  }
  ?>
</body>

</html>