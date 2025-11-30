<!DOCTYPE html>
<?php
include('func1.php');
$pid = '';
$ID = '';
$appdate = '';
$apptime = '';
$fname = '';
$lname = '';
$doctor = $_SESSION['dname'];
if (isset($_GET['pid']) && isset($_GET['ID']) && isset($_GET['appdate']) && isset($_GET['apptime']) && isset($_GET['fname']) && isset($_GET['lname'])) {
  $pid = mysqli_real_escape_string($con, $_GET['pid']);
  $ID = mysqli_real_escape_string($con, $_GET['ID']);
  $fname = mysqli_real_escape_string($con, $_GET['fname']);
  $lname = mysqli_real_escape_string($con, $_GET['lname']);
  $appdate = mysqli_real_escape_string($con, $_GET['appdate']);
  $apptime = mysqli_real_escape_string($con, $_GET['apptime']);
}

if (isset($_POST['prescribe']) && isset($_POST['pid']) && isset($_POST['ID']) && isset($_POST['appdate']) && isset($_POST['apptime']) && isset($_POST['lname']) && isset($_POST['fname'])) {
  $appdate = mysqli_real_escape_string($con, $_POST['appdate']);
  $apptime = mysqli_real_escape_string($con, $_POST['apptime']);
  $disease = mysqli_real_escape_string($con, $_POST['disease']);
  $allergy = mysqli_real_escape_string($con, $_POST['allergy']);
  $fname = mysqli_real_escape_string($con, $_POST['fname']);
  $lname = mysqli_real_escape_string($con, $_POST['lname']);
  $pid = mysqli_real_escape_string($con, $_POST['pid']);
  $ID = mysqli_real_escape_string($con, $_POST['ID']);
  $prescription = mysqli_real_escape_string($con, $_POST['prescription']);

  $query = mysqli_query($con, "insert into prestb(doctor,pid,ID,fname,lname,appdate,apptime,disease,allergy,prescription) values ('$doctor','$pid','$ID','$fname','$lname','$appdate','$apptime','$disease','$allergy','$prescription')");
  if ($query) {
    echo "<script>alert('Prescribed successfully!'); window.location.href = 'doctor-panel.php';</script>";
  } else {
    echo "<script>alert('Unable to process your request. Try again!');</script>";
  }
}
?>

<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Prescribe Medication | KMC Hospital</title>
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
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

<body class="gradient-bg min-h-screen text-gray-800 font-sans">

  <!-- Navbar -->
  <nav class="bg-emerald-800/95 backdrop-blur-xl text-white fixed w-full top-0 z-40 shadow-2xl">
    <div class="max-w-7xl mx-auto px-6 py-5 flex justify-between items-center">
      <div class="flex items-center gap-4">
        <i class="fas fa-user-md text-4xl animate-pulse"></i>
        <h1 class="text-3xl font-extrabold">KMC Hospital</h1>
      </div>
      <div class="flex items-center gap-8">
        <span class="hidden md:block text-xl">Dr. <strong><?= htmlspecialchars($doctor) ?></strong></span>
        <a href="doctor-panel.php"
          class="bg-white/20 hover:bg-white/30 px-6 py-3 rounded-full font-bold text-lg transition">
          <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
        <a href="logout1.php" class="bg-red-600 hover:bg-red-700 px-6 py-3 rounded-full font-bold text-lg transition">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </div>
    </div>
  </nav>

  <div class="pt-32 pb-20 px-6 max-w-4xl mx-auto">
    <div class="card p-10 rounded-3xl shadow-2xl animate-fade-in-up">
      <div class="text-center mb-10">
        <h2 class="text-4xl font-extrabold text-emerald-800 mb-2">Prescribe Medication</h2>
        <p class="text-gray-600 text-lg">Patient: <span
            class="font-bold text-emerald-700"><?= htmlspecialchars($fname) . ' ' . htmlspecialchars($lname) ?></span>
          (ID: <?= htmlspecialchars($pid) ?>)</p>
      </div>

      <form name="prescribeform" method="post" action="prescribe.php" class="space-y-8">

        <div>
          <label class="block text-xl font-bold text-emerald-800 mb-3">Disease / Symptoms</label>
          <textarea name="disease" required rows="3"
            class="w-full px-6 py-4 rounded-2xl bg-emerald-50 border-2 border-emerald-100 focus:border-emerald-500 focus:outline-none text-lg transition"></textarea>
        </div>

        <div>
          <label class="block text-xl font-bold text-emerald-800 mb-3">Allergies</label>
          <textarea name="allergy" required rows="3"
            class="w-full px-6 py-4 rounded-2xl bg-emerald-50 border-2 border-emerald-100 focus:border-emerald-500 focus:outline-none text-lg transition"></textarea>
        </div>

        <div>
          <label class="block text-xl font-bold text-emerald-800 mb-3">Prescription</label>
          <textarea name="prescription" required rows="6"
            class="w-full px-6 py-4 rounded-2xl bg-emerald-50 border-2 border-emerald-100 focus:border-emerald-500 focus:outline-none text-lg transition"></textarea>
        </div>

        <input type="hidden" name="fname" value="<?php echo $fname ?>" />
        <input type="hidden" name="lname" value="<?php echo $lname ?>" />
        <input type="hidden" name="appdate" value="<?php echo $appdate ?>" />
        <input type="hidden" name="apptime" value="<?php echo $apptime ?>" />
        <input type="hidden" name="pid" value="<?php echo $pid ?>" />
        <input type="hidden" name="ID" value="<?php echo $ID ?>" />

        <div class="text-center pt-4">
          <button type="submit" name="prescribe"
            class="btn text-white font-bold text-2xl px-16 py-5 rounded-2xl shadow-xl w-full md:w-auto transition-transform">
            <i class="fas fa-paper-plane mr-3"></i> Submit Prescription
          </button>
        </div>

      </form>
    </div>
  </div>

</body>

</html>