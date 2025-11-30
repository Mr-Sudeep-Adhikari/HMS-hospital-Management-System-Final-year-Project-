<?php
// =============================================================================
// DOCTOR PANEL - SECURE VERSION  
// =============================================================================
require_once 'config.php';

startSecureSession();
$con = getDBConnection();

// Session validation
if (!isset($_SESSION['dname']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'doctor') {
  header("Location: index.php");
  exit();
}

// Check session timeout
if (!isSessionValid()) {
  destroySession();
  header("Location: index.php");
  exit();
}

// Get doctor information
$doctor = $_SESSION['doctor'];
$dname = $_SESSION['dname'];
$spec = $_SESSION['spec'] ?? 'Doctor';

// Cancel appointment handler
if (isset($_GET['cancel']) && isset($_GET['ID'])) {
  $appointmentID = intval($_GET['ID']);
  $query = "UPDATE appointmenttb SET doctorStatus='0' WHERE ID = ? AND doctor = ?";
  $stmt = executeQuery($con, $query, "is", [$appointmentID, $doctor]);
  if ($stmt) {
    mysqli_stmt_close($stmt);
    $_SESSION['msg'] = "Appointment cancelled successfully!";
    $_SESSION['msg_type'] = "success";
    header("Location: doctor-panel.php");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Portal | KMC Hospital</title>
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
      transform: translateY(-6px);
      box-shadow: 0 25px 50px rgba(5, 150, 105, 0.5);
    }

    .tab-btn {
      transition: all 0.4s;
    }

    .tab-active {
      background: white;
      color: #059669;
      font-weight: bold;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
      border: 3px solid #059669;
    }

    .alert {
      animation: slideDown 0.6s ease-out;
    }

    @keyframes slideDown {
      from {
        transform: translateY(-100px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }
  </style>
</head>

<body class="gradient-bg min-h-screen text-gray-800">

  <!-- Alert Messages -->
  <?php if (isset($_SESSION['msg'])): ?>
    <div
      class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 alert <?= $_SESSION['msg_type'] == 'success' ? 'bg-green-100 border-green-500 text-green-800' : 'bg-red-100 border-red-500 text-red-800' ?> px-10 py-6 rounded-2xl shadow-2xl border-2 font-bold text-lg">
      <i class="fas <?= $_SESSION['msg_type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-3"></i>
      <?= $_SESSION['msg'] ?>
    </div>
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
  <?php endif; ?>

  <!-- Navbar -->
  <nav class="bg-emerald-800/95 backdrop-blur-xl text-white fixed w-full top-0 z-40 shadow-2xl">
    <div class="max-w-7xl mx-auto px-6 py-5 flex justify-between items-center">
      <div class="flex items-center gap-4">
        <i class="fas fa-user-md text-4xl animate-pulse"></i>
        <h1 class="text-3xl font-extrabold">KMC Hospital</h1>
      </div>
      <div class="flex items-center gap-8">
        <span class="hidden md:block text-xl">Dr. <strong><?= htmlspecialchars($dname) ?></strong></span>
        <a href="logout1.php" class="bg-red-600 hover:bg-red-700 px-8 py-4 rounded-full font-bold text-lg transition">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </div>
    </div>
  </nav>

  <div class="pt-32 pb-20 px-6 max-w-7xl mx-auto">
    <h1 class="text-6xl md:text-8xl font-extrabold text-white text-center mb-6">
      Welcome, <span class="text-emerald-200">Dr. <?= htmlspecialchars($dname) ?></span>
    </h1>
    <p class="text-center text-emerald-100 text-2xl mb-16">Manage your appointments and patients efficiently.</p>

    <div class="grid lg:grid-cols-4 gap-10">

      <!-- Sidebar -->
      <div class="space-y-6">
        <button onclick="showTab('home')"
          class="tab-btn tab-active w-full text-left px-8 py-6 rounded-3xl text-xl font-bold flex items-center gap-4">
          <i class="fas fa-home text-2xl"></i> Dashboard
        </button>
        <button onclick="showTab('appointments')"
          class="tab-btn w-full text-left px-8 py-6 rounded-3xl bg-white/90 hover:bg-white shadow-2xl text-xl font-bold flex items-center gap-4">
          <i class="fas fa-calendar-check text-2xl"></i> Appointments
        </button>
        <button onclick="showTab('prescriptions')"
          class="tab-btn w-full text-left px-8 py-6 rounded-3xl bg-white/90 hover:bg-white shadow-2xl text-xl font-bold flex items-center gap-4">
          <i class="fas fa-prescription text-2xl"></i> Prescriptions
        </button>
      </div>

      <!-- Main Content -->
      <div class="lg:col-span-3">

        <!-- Home Dashboard -->
        <div id="home" class="grid md:grid-cols-2 gap-8">
          <div onclick="showTab('appointments')"
            class="card p-12 rounded-3xl text-center cursor-pointer hover:scale-105 transition-all duration-300 shadow-2xl">
            <i class="fas fa-calendar-check text-8xl text-emerald-600 mb-6"></i>
            <h3 class="text-3xl font-bold">View Appointments</h3>
            <p class="text-gray-600 mt-4">Check your upcoming schedule</p>
          </div>
          <div onclick="showTab('prescriptions')"
            class="card p-12 rounded-3xl text-center cursor-pointer hover:scale-105 transition-all duration-300 shadow-2xl">
            <i class="fas fa-prescription text-8xl text-emerald-600 mb-6"></i>
            <h3 class="text-3xl font-bold">Prescription List</h3>
            <p class="text-gray-600 mt-4">View past prescriptions</p>
          </div>
        </div>

        <!-- Appointments List -->
        <div id="appointments" class="hidden card rounded-3xl p-12 shadow-3xl">
          <h2 class="text-5xl font-extrabold text-emerald-700 mb-10">Appointment List</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-emerald-100 text-emerald-800">
                <tr>
                  <th class="px-6 py-4">ID</th>
                  <th class="px-6 py-4">Patient</th>
                  <th class="px-6 py-4">Gender</th>
                  <th class="px-6 py-4">Contact</th>
                  <th class="px-6 py-4">Date/Time</th>
                  <th class="px-6 py-4">Status</th>
                  <th class="px-6 py-4">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // SECURE: Use prepared statement
                $query = "SELECT pid,ID,fname,lname,gender,email,contact,appdate,apptime,userStatus,doctorStatus FROM appointmenttb WHERE doctor = ?";
                $stmt = executeQuery($con, $query, "s", [$dname]);

                if ($stmt) {
                  $result = mysqli_stmt_get_result($stmt);
                  while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr class="border-b hover:bg-emerald-50">
                      <td class="px-6 py-4"><?php echo htmlspecialchars($row['ID']); ?></td>
                      <td class="px-6 py-4">
                        <div class="font-bold"><?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['pid']); ?></div>
                      </td>
                      <td class="px-6 py-4"><?php echo htmlspecialchars($row['gender']); ?></td>
                      <td class="px-6 py-4">
                        <div><?php echo htmlspecialchars($row['contact']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['email']); ?></div>
                      </td>
                      <td class="px-6 py-4">
                        <div><?php echo htmlspecialchars($row['appdate']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['apptime']); ?></div>
                      </td>
                      <td class="px-6 py-4">
                        <?php
                        if (($row['userStatus'] == 1) && ($row['doctorStatus'] == 1)) {
                          echo '<span class="text-green-600 font-bold">Active</span>';
                        }
                        if (($row['userStatus'] == 0) && ($row['doctorStatus'] == 1)) {
                          echo '<span class="text-red-600 font-bold">Cancelled by Patient</span>';
                        }
                        if (($row['userStatus'] == 1) && ($row['doctorStatus'] == 0)) {
                          echo '<span class="text-orange-600 font-bold">Cancelled by You</span>';
                        }
                        ?>
                      </td>
                      <td class="px-6 py-4 space-y-2">
                        <?php if (($row['userStatus'] == 1) && ($row['doctorStatus'] == 1)) { ?>
                          <a href="doctor-panel.php?ID=<?php echo $row['ID'] ?>&cancel=update"
                            onClick="return confirm('Are you sure you want to cancel this appointment?')"
                            class="block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-center text-sm font-bold transition">
                            Cancel
                          </a>
                          <a href="prescribe.php?pid=<?php echo $row['pid'] ?>&ID=<?php echo $row['ID'] ?>&fname=<?php echo $row['fname'] ?>&lname=<?php echo $row['lname'] ?>&appdate=<?php echo $row['appdate'] ?>&apptime=<?php echo $row['apptime'] ?>"
                            class="block bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-center text-sm font-bold transition">
                            Prescribe
                          </a>
                        <?php } else {
                          echo '<span class="text-gray-400">No Actions</span>';
                        } ?>
                      </td>
                    </tr>
                    <?php
                  }
                  mysqli_stmt_close($stmt);
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Prescriptions List -->
        <div id="prescriptions" class="hidden card rounded-3xl p-12 shadow-3xl">
          <h2 class="text-5xl font-extrabold text-emerald-700 mb-10">Prescription List</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-emerald-100 text-emerald-800">
                <tr>
                  <th class="px-6 py-4">Patient</th>
                  <th class="px-6 py-4">Appt ID</th>
                  <th class="px-6 py-4">Date</th>
                  <th class="px-6 py-4">Disease</th>
                  <th class="px-6 py-4">Allergy</th>
                  <th class="px-6 py-4">Prescription</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // SECURE: Use prepared statement
                $query = "SELECT pid,fname,lname,ID,appdate,apptime,disease,allergy,prescription FROM prestb WHERE doctor = ?";
                $stmt = executeQuery($con, $query, "s", [$doctor]);

                if ($stmt) {
                  $result = mysqli_stmt_get_result($stmt);
                  while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr class="border-b hover:bg-emerald-50">
                      <td class="px-6 py-4">
                        <div class="font-bold"><?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['pid']); ?></div>
                      </td>
                      <td class="px-6 py-4"><?php echo htmlspecialchars($row['ID']); ?></td>
                      <td class="px-6 py-4">
                        <div><?php echo htmlspecialchars($row['appdate']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['apptime']); ?></div>
                      </td>
                      <td class="px-6 py-4"><?php echo htmlspecialchars($row['disease']); ?></td>
                      <td class="px-6 py-4"><?php echo htmlspecialchars($row['allergy']); ?></td>
                      <td class="px-6 py-4"><?php echo htmlspecialchars($row['prescription']); ?></td>
                    </tr>
                    <?php
                  }
                  mysqli_stmt_close($stmt);
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    function showTab(tab) {
      document.querySelectorAll('#home, #appointments, #prescriptions').forEach(el => el.classList.add('hidden'));
      document.getElementById(tab).classList.remove('hidden');

      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('tab-active');
        btn.classList.add('bg-white/90');
      });
      event.target.classList.add('tab-active');
      event.target.classList.remove('bg-white/90');
    }

    // Auto-hide alerts
    setTimeout(() => {
      const alert = document.querySelector('.alert');
      if (alert) alert.style.opacity = '0';
    }, 5000);
  </script>
</body>

</html>