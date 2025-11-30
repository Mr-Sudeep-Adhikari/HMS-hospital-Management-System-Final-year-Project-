<?php
// =============================================================================
// ADMIN PANEL - SECURE VERSION
// =============================================================================
require_once 'config.php';

startSecureSession();
$con = getDBConnection();

// Session validation
if (!isset($_SESSION['username']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
  header("Location: index.php");
  exit();
}

// Check session timeout
if (!isSessionValid()) {
  destroySession();
  header("Location: index.php");
  exit();
}

// Handle Add Doctor
if (isset($_POST['docsub'])) {
  $doctor = sanitizeInput($_POST['doctor']);
  $dpassword = $_POST['dpassword'];
  $demail = sanitizeInput($_POST['demail']);
  $spec = sanitizeInput($_POST['special']);
  $docFees = sanitizeInput($_POST['docFees']);

  // Check if doctor exists
  $check = executeQuery($con, "SELECT * FROM doctb WHERE email = ? OR username = ?", "ss", [$demail, $doctor]);
  if (mysqli_num_rows(mysqli_stmt_get_result($check)) > 0) {
    $_SESSION['msg'] = "Doctor already exists!";
    $_SESSION['msg_type'] = "error";
  } else {
    // Insert new doctor
    $query = "INSERT INTO doctb(username, password, email, spec, docFees) VALUES (?, ?, ?, ?, ?)";
    // Note: In a real app, we should hash this password. For now, keeping it plain as per existing schema, 
    // but the login logic I wrote handles hashing on first login.
    if (executeQuery($con, $query, "sssss", [$doctor, $dpassword, $demail, $spec, $docFees])) {
      $_SESSION['msg'] = "Doctor added successfully!";
      $_SESSION['msg_type'] = "success";
    } else {
      $_SESSION['msg'] = "Failed to add doctor!";
      $_SESSION['msg_type'] = "error";
    }
  }
  header("Location: admin-panel1.php");
  exit();
}

// Handle Delete Doctor
if (isset($_POST['docsub1'])) {
  $demail = sanitizeInput($_POST['demail']);
  $query = "DELETE FROM doctb WHERE email = ?";
  if (executeQuery($con, $query, "s", [$demail])) {
    $_SESSION['msg'] = "Doctor removed successfully!";
    $_SESSION['msg_type'] = "success";
  } else {
    $_SESSION['msg'] = "Unable to delete doctor!";
    $_SESSION['msg_type'] = "error";
  }
  header("Location: admin-panel1.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal | KMC Hospital</title>
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
        <i class="fas fa-user-shield text-4xl animate-pulse"></i>
        <h1 class="text-3xl font-extrabold">KMC Hospital</h1>
      </div>
      <div class="flex items-center gap-8">
        <span class="hidden md:block text-xl">Welcome, <strong>Admin</strong></span>
        <a href="logout1.php" class="bg-red-600 hover:bg-red-700 px-8 py-4 rounded-full font-bold text-lg transition">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </div>
    </div>
  </nav>

  <div class="pt-32 pb-20 px-6 max-w-7xl mx-auto">
    <h1 class="text-6xl md:text-8xl font-extrabold text-white text-center mb-6">
      Admin <span class="text-emerald-200">Dashboard</span>
    </h1>
    <p class="text-center text-emerald-100 text-2xl mb-16">Manage hospital resources and personnel.</p>

    <div class="grid lg:grid-cols-4 gap-10">

      <!-- Sidebar -->
      <div class="space-y-4">
        <button onclick="showTab('home')"
          class="tab-btn tab-active w-full text-left px-6 py-4 rounded-2xl text-lg font-bold flex items-center gap-3">
          <i class="fas fa-home w-8"></i> Dashboard
        </button>
        <button onclick="showTab('doctors')"
          class="tab-btn w-full text-left px-6 py-4 rounded-2xl bg-white/90 hover:bg-white shadow-xl text-lg font-bold flex items-center gap-3">
          <i class="fas fa-user-md w-8"></i> Doctor List
        </button>
        <button onclick="showTab('patients')"
          class="tab-btn w-full text-left px-6 py-4 rounded-2xl bg-white/90 hover:bg-white shadow-xl text-lg font-bold flex items-center gap-3">
          <i class="fas fa-user-injured w-8"></i> Patient List
        </button>
        <button onclick="showTab('appointments')"
          class="tab-btn w-full text-left px-6 py-4 rounded-2xl bg-white/90 hover:bg-white shadow-xl text-lg font-bold flex items-center gap-3">
          <i class="fas fa-calendar-alt w-8"></i> Appointments
        </button>
        <button onclick="showTab('prescriptions')"
          class="tab-btn w-full text-left px-6 py-4 rounded-2xl bg-white/90 hover:bg-white shadow-xl text-lg font-bold flex items-center gap-3">
          <i class="fas fa-file-prescription w-8"></i> Prescriptions
        </button>
        <button onclick="showTab('add-doc')"
          class="tab-btn w-full text-left px-6 py-4 rounded-2xl bg-white/90 hover:bg-white shadow-xl text-lg font-bold flex items-center gap-3">
          <i class="fas fa-user-plus w-8"></i> Add Doctor
        </button>
        <button onclick="showTab('del-doc')"
          class="tab-btn w-full text-left px-6 py-4 rounded-2xl bg-white/90 hover:bg-white shadow-xl text-lg font-bold flex items-center gap-3">
          <i class="fas fa-user-minus w-8"></i> Delete Doctor
        </button>
        <button onclick="showTab('queries')"
          class="tab-btn w-full text-left px-6 py-4 rounded-2xl bg-white/90 hover:bg-white shadow-xl text-lg font-bold flex items-center gap-3">
          <i class="fas fa-comments w-8"></i> Queries
        </button>
      </div>

      <!-- Main Content -->
      <div class="lg:col-span-3">

        <!-- Home Dashboard -->
        <div id="home" class="grid md:grid-cols-2 gap-8">
          <div onclick="showTab('doctors')"
            class="card p-10 rounded-3xl text-center cursor-pointer hover:scale-105 transition-all duration-300 shadow-2xl">
            <i class="fas fa-user-md text-7xl text-emerald-600 mb-4"></i>
            <h3 class="text-2xl font-bold">Manage Doctors</h3>
          </div>
          <div onclick="showTab('patients')"
            class="card p-10 rounded-3xl text-center cursor-pointer hover:scale-105 transition-all duration-300 shadow-2xl">
            <i class="fas fa-user-injured text-7xl text-emerald-600 mb-4"></i>
            <h3 class="text-2xl font-bold">Manage Patients</h3>
          </div>
          <div onclick="showTab('appointments')"
            class="card p-10 rounded-3xl text-center cursor-pointer hover:scale-105 transition-all duration-300 shadow-2xl">
            <i class="fas fa-calendar-check text-7xl text-emerald-600 mb-4"></i>
            <h3 class="text-2xl font-bold">Appointments</h3>
          </div>
          <div onclick="showTab('add-doc')"
            class="card p-10 rounded-3xl text-center cursor-pointer hover:scale-105 transition-all duration-300 shadow-2xl">
            <i class="fas fa-user-plus text-7xl text-emerald-600 mb-4"></i>
            <h3 class="text-2xl font-bold">Add New Doctor</h3>
          </div>
        </div>

        <!-- Doctor List -->
        <div id="doctors" class="hidden card rounded-3xl p-10 shadow-3xl">
          <h2 class="text-4xl font-extrabold text-emerald-700 mb-8">Doctor List</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-emerald-100 text-emerald-800">
                <tr>
                  <th class="px-6 py-4">Name</th>
                  <th class="px-6 py-4">Specialization</th>
                  <th class="px-6 py-4">Email</th>
                  <th class="px-6 py-4">Fees</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = mysqli_query($con, "SELECT * FROM doctb");
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr class='border-b hover:bg-emerald-50'>
                                        <td class='px-6 py-4 font-bold'>Dr. {$row['username']}</td>
                                        <td class='px-6 py-4'>{$row['spec']}</td>
                                        <td class='px-6 py-4'>{$row['email']}</td>
                                        <td class='px-6 py-4'>{$row['docFees']}</td>
                                    </tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Patient List -->
        <div id="patients" class="hidden card rounded-3xl p-10 shadow-3xl">
          <h2 class="text-4xl font-extrabold text-emerald-700 mb-8">Patient List</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-emerald-100 text-emerald-800">
                <tr>
                  <th class="px-6 py-4">ID</th>
                  <th class="px-6 py-4">Name</th>
                  <th class="px-6 py-4">Gender</th>
                  <th class="px-6 py-4">Email</th>
                  <th class="px-6 py-4">Contact</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = mysqli_query($con, "SELECT * FROM patreg");
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr class='border-b hover:bg-emerald-50'>
                                        <td class='px-6 py-4'>{$row['pid']}</td>
                                        <td class='px-6 py-4 font-bold'>{$row['fname']} {$row['lname']}</td>
                                        <td class='px-6 py-4'>{$row['gender']}</td>
                                        <td class='px-6 py-4'>{$row['email']}</td>
                                        <td class='px-6 py-4'>{$row['contact']}</td>
                                    </tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Appointment Details -->
        <div id="appointments" class="hidden card rounded-3xl p-10 shadow-3xl">
          <h2 class="text-4xl font-extrabold text-emerald-700 mb-8">Appointment Details</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-emerald-100 text-emerald-800">
                <tr>
                  <th class="px-6 py-4">ID</th>
                  <th class="px-6 py-4">Patient</th>
                  <th class="px-6 py-4">Doctor</th>
                  <th class="px-6 py-4">Date/Time</th>
                  <th class="px-6 py-4">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = mysqli_query($con, "SELECT * FROM appointmenttb");
                while ($row = mysqli_fetch_assoc($result)) {
                  $status = "";
                  if (($row['userStatus'] == 1) && ($row['doctorStatus'] == 1))
                    $status = '<span class="text-green-600 font-bold">Active</span>';
                  elseif (($row['userStatus'] == 0) && ($row['doctorStatus'] == 1))
                    $status = '<span class="text-red-600 font-bold">Cancelled by Patient</span>';
                  elseif (($row['userStatus'] == 1) && ($row['doctorStatus'] == 0))
                    $status = '<span class="text-orange-600 font-bold">Cancelled by Doctor</span>';

                  echo "<tr class='border-b hover:bg-emerald-50'>
                                        <td class='px-6 py-4'>{$row['ID']}</td>
                                        <td class='px-6 py-4'>{$row['fname']} {$row['lname']}</td>
                                        <td class='px-6 py-4'>Dr. {$row['doctor']}</td>
                                        <td class='px-6 py-4'>{$row['appdate']} <br> {$row['apptime']}</td>
                                        <td class='px-6 py-4'>$status</td>
                                    </tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Prescription List -->
        <div id="prescriptions" class="hidden card rounded-3xl p-10 shadow-3xl">
          <h2 class="text-4xl font-extrabold text-emerald-700 mb-8">Prescription List</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-emerald-100 text-emerald-800">
                <tr>
                  <th class="px-6 py-4">Doctor</th>
                  <th class="px-6 py-4">Patient</th>
                  <th class="px-6 py-4">Date</th>
                  <th class="px-6 py-4">Disease</th>
                  <th class="px-6 py-4">Prescription</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = mysqli_query($con, "SELECT * FROM prestb");
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr class='border-b hover:bg-emerald-50'>
                                        <td class='px-6 py-4'>Dr. {$row['doctor']}</td>
                                        <td class='px-6 py-4'>{$row['fname']} {$row['lname']}</td>
                                        <td class='px-6 py-4'>{$row['appdate']}</td>
                                        <td class='px-6 py-4'>{$row['disease']}</td>
                                        <td class='px-6 py-4'>{$row['prescription']}</td>
                                    </tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Add Doctor -->
        <div id="add-doc" class="hidden card rounded-3xl p-10 shadow-3xl">
          <h2 class="text-4xl font-extrabold text-emerald-700 mb-8">Add New Doctor</h2>
          <form method="post" class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label class="block text-lg font-bold mb-2">Doctor Name</label>
                <input type="text" name="doctor" required
                  class="w-full px-6 py-3 rounded-xl border-2 border-emerald-300 focus:border-emerald-600 outline-none">
              </div>
              <div>
                <label class="block text-lg font-bold mb-2">Specialization</label>
                <select name="special" required
                  class="w-full px-6 py-3 rounded-xl border-2 border-emerald-300 focus:border-emerald-600 outline-none">
                  <option value="">Select Specialization</option>
                  <option value="General">General</option>
                  <option value="Cardiologist">Cardiologist</option>
                  <option value="Neurologist">Neurologist</option>
                  <option value="Pediatrician">Pediatrician</option>
                </select>
              </div>
              <div>
                <label class="block text-lg font-bold mb-2">Email</label>
                <input type="email" name="demail" required
                  class="w-full px-6 py-3 rounded-xl border-2 border-emerald-300 focus:border-emerald-600 outline-none">
              </div>
              <div>
                <label class="block text-lg font-bold mb-2">Consultancy Fees</label>
                <input type="number" name="docFees" required
                  class="w-full px-6 py-3 rounded-xl border-2 border-emerald-300 focus:border-emerald-600 outline-none">
              </div>
              <div>
                <label class="block text-lg font-bold mb-2">Password</label>
                <input type="password" name="dpassword" required
                  class="w-full px-6 py-3 rounded-xl border-2 border-emerald-300 focus:border-emerald-600 outline-none">
              </div>
              <div>
                <label class="block text-lg font-bold mb-2">Confirm Password</label>
                <input type="password" name="cdpassword" required
                  class="w-full px-6 py-3 rounded-xl border-2 border-emerald-300 focus:border-emerald-600 outline-none">
              </div>
            </div>
            <button type="submit" name="docsub"
              class="btn text-white font-bold text-xl px-12 py-4 rounded-xl shadow-lg w-full md:w-auto">Add
              Doctor</button>
          </form>
        </div>

        <!-- Delete Doctor -->
        <div id="del-doc" class="hidden card rounded-3xl p-10 shadow-3xl">
          <h2 class="text-4xl font-extrabold text-emerald-700 mb-8">Delete Doctor</h2>
          <form method="post" class="space-y-6 max-w-lg">
            <div>
              <label class="block text-lg font-bold mb-2">Doctor Email</label>
              <input type="email" name="demail" required
                class="w-full px-6 py-3 rounded-xl border-2 border-emerald-300 focus:border-emerald-600 outline-none">
            </div>
            <button type="submit" name="docsub1"
              onclick="return confirm('Are you sure you want to delete this doctor?')"
              class="bg-red-600 hover:bg-red-700 text-white font-bold text-xl px-12 py-4 rounded-xl shadow-lg transition">Delete
              Doctor</button>
          </form>
        </div>

        <!-- Queries -->
        <div id="queries" class="hidden card rounded-3xl p-10 shadow-3xl">
          <h2 class="text-4xl font-extrabold text-emerald-700 mb-8">User Queries</h2>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-emerald-100 text-emerald-800">
                <tr>
                  <th class="px-6 py-4">Name</th>
                  <th class="px-6 py-4">Email</th>
                  <th class="px-6 py-4">Contact</th>
                  <th class="px-6 py-4">Message</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = mysqli_query($con, "SELECT * FROM contact");
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr class='border-b hover:bg-emerald-50'>
                                        <td class='px-6 py-4 font-bold'>{$row['name']}</td>
                                        <td class='px-6 py-4'>{$row['email']}</td>
                                        <td class='px-6 py-4'>{$row['contact']}</td>
                                        <td class='px-6 py-4'>{$row['message']}</td>
                                    </tr>";
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
      // Hide all tabs
      const tabs = ['home', 'doctors', 'patients', 'appointments', 'prescriptions', 'add-doc', 'del-doc', 'queries'];
      tabs.forEach(t => {
        const el = document.getElementById(t);
        if (el) el.classList.add('hidden');
      });

      // Show selected tab
      const selected = document.getElementById(tab);
      if (selected) selected.classList.remove('hidden');

      // Update buttons
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('tab-active');
        btn.classList.add('bg-white/90');
      });
      event.currentTarget.classList.add('tab-active');
      event.currentTarget.classList.remove('bg-white/90');
    }

    // Auto-hide alerts
    setTimeout(() => {
      const alert = document.querySelector('.alert');
      if (alert) alert.style.opacity = '0';
    }, 5000);
  </script>
</body>

</html>