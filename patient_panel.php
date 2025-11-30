<?php
session_start();

// === SECURITY: Must be logged in ===
if (!isset($_SESSION['pid']) || !isset($_SESSION['fname'])) {
    header("Location: patientlogin.php");
    exit();
}

include('func.php');
include('newfunc.php'); // For display_docs() & display_specs()

$con = mysqli_connect("localhost", "root", "", "hospitaldatabase");
if (!$con) {
    die("<div class='bg-red-100 p-6 rounded-xl text-red-700'>Database Connection Failed!</div>");
}

$pid = (int)$_SESSION['pid'];
$fname = htmlspecialchars($_SESSION['fname']);
$lname = htmlspecialchars($_SESSION['lname'] ?? '');
$email = htmlspecialchars($_SESSION['email'] ?? '');
$contact = htmlspecialchars($_SESSION['contact'] ?? '');
$gender = htmlspecialchars($_SESSION['gender'] ?? '');

// === CANCEL APPOINTMENT ===
if (isset($_GET['cancel']) && isset($_GET['ID'])) {
    $id = (int)$_GET['ID'];
    $stmt = mysqli_prepare($con, "UPDATE appointmenttb SET userStatus = 0 WHERE ID = ? AND pid = ? AND userStatus = 1");
    mysqli_stmt_bind_param($stmt, "ii", $id, $pid);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['msg'] = "Appointment cancelled successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Failed to cancel appointment.";
        $_SESSION['msg_type'] = "error";
    }
    header("Location: patient_panel.php");
    exit();
}

// === GENERATE PDF BILL (TCPDF) ===
if (isset($_GET['bill']) && isset($_GET['ID'])) {
    require_once("TCPDF/tcpdf.php");
    
    $id = (int)$_GET['ID'];
    $stmt = mysqli_prepare($con, "
        SELECT p.disease, p.prescription, p.allergy, a.doctor, a.appdate, a.apptime, a.docFees
        FROM prestb p 
        JOIN appointmenttb a ON p.ID = a.ID 
        WHERE p.ID = ? AND p.pid = ?
    ");
    mysqli_stmt_bind_param($stmt, "ii", $id, $pid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('KMC Hospital');
        $pdf->SetAuthor('KMC Hospital');
        $pdf->SetTitle('Medical Bill & Prescription');
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 12);

        $html = '
        <h1 style="color:#059669; text-align:center; font-size:30px; margin-bottom:5px;">KMC HOSPITAL</h1>
        <p style="text-align:center; color:#065f46; font-size:14px;">Your Health, Our Priority</p>
        <hr style="border:2px solid #10b981; margin:15px 0;">
        <h2 style="text-align:center; color:#064e3b;">Medical Prescription & Bill</h2>
        
        <table border="1" cellpadding="12" cellspacing="0" style="width:100%; font-size:13px; margin-top:20px;">
            <tr style="background:#ecfdf5;">
                <td width="35%"><strong>Patient Name</strong></td>
                <td width="65%"><strong>' . $fname . ' ' . $lname . '</strong></td>
            </tr>
            <tr>
                <td><strong>Doctor</strong></td>
                <td>Dr. ' . htmlspecialchars($row['doctor']) . '</td>
            </tr>
            <tr>
                <td><strong>Visit Date</strong></td>
                <td>' . date('d F Y', strtotime($row['appdate'])) . ' at ' . date('g:i A', strtotime($row['apptime'])) . '</td>
            </tr>
            <tr>
                <td><strong>Diagnosis</strong></td>
                <td>' . nl2br(htmlspecialchars($row['disease'] ?: 'Routine Checkup')) . '</td>
            </tr>
            <tr>
                <td><strong>Allergies</strong></td>
                <td>' . nl2br(htmlspecialchars($row['allergy'] ?: 'None declared')) . '</td>
            </tr>
            <tr>
                <td><strong>Prescription</strong></td>
                <td style="line-height:1.8;">' . nl2br(htmlspecialchars($row['prescription'])) . '</td>
            </tr>
            <tr style="background:#d1fae5; font-size:16px;">
                <td><strong>Consultation Fee</strong></td>
                <td><strong>रुपैयाँ ' . number_format($row['docFees']) . ' Only</strong></td>
            </tr>
        </table>
        <br><br>
        <p style="text-align:center; color:#059669; font-size:12px;">
            Thank you for trusting KMC Hospital<br>
            Get well soon! For queries: +977-1-XXXXXXX
        </p>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output("KMC_Hospital_Bill_{$pid}_{$id}.pdf", "I");
        exit();
    } else {
        $_SESSION['msg'] = "Prescription not found!";
        $_SESSION['msg_type'] = "error";
    }
}

// === BOOK APPOINTMENT ===
if (isset($_POST['app-submit'])) {
    $doctor   = mysqli_real_escape_string($con, $_POST['doctor']);
    $docFees  = (int)$_POST['docFees'];
    $appdate  = $_POST['appdate'];
    $apptime  = $_POST['apptime'];

    if (strtotime($appdate) < strtotime('today')) {
        $_SESSION['msg'] = "Cannot book past date!";
        $_SESSION['msg_type'] = "error";
    } else {
        $check = mysqli_query($con, "SELECT ID FROM appointmenttb WHERE doctor='$doctor' AND appdate='$appdate' AND apptime='$apptime' AND userStatus=1");
        if (mysqli_num_rows($check) > 0) {
            $_SESSION['msg'] = "This slot is already booked!";
            $_SESSION['msg_type'] = "error";
        } else {
            $stmt = mysqli_prepare($con, "
                INSERT INTO appointmenttb 
                (pid, fname, lname, gender, email, contact, doctor, docFees, appdate, apptime, userStatus, doctorStatus)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)
            ");
            mysqli_stmt_bind_param($stmt, "isssssisss", $pid, $fname, $lname, $gender, $email, $contact, $doctor, $docFees, $appdate, $apptime);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['msg'] = "Appointment booked successfully with Dr. $doctor!";
                $_SESSION['msg_type'] = "success";
            } else {
                $_SESSION['msg'] = "Booking failed!";
                $_SESSION['msg_type'] = "error";
            }
        }
    }
    header("Location: patient_panel.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal | KMC Hospital</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #064e3b 0%, #059669 35%, #10b981 100%);
            background-size: 200% 200%;
            animation: gradient 12s ease infinite;
        }
        @keyframes gradient { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
        .card { background: rgba(255,255,255,0.98); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.4); }
        .btn { background: linear-gradient(to right, #059669, #10b981); }
        .btn:hover { transform: translateY(-6px); box-shadow: 0 25px 50px rgba(5,150,105,0.5); }
        .tab-btn { transition: all 0.4s; }
        .tab-active { background: white; color: #059669; font-weight: bold; box-shadow: 0 20px 40px rgba(0,0,0,0.2); border: 3px solid #059669; }
        .alert { animation: slideDown 0.6s ease-out; }
        @keyframes slideDown { from { transform: translateY(-100px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body class="gradient-bg min-h-screen text-gray-800">

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['msg'])): ?>
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 alert <?= $_SESSION['msg_type'] == 'success' ? 'bg-green-100 border-green-500 text-green-800' : 'bg-red-100 border-red-500 text-red-800' ?> px-10 py-6 rounded-2xl shadow-2xl border-2 font-bold text-lg">
            <i class="fas <?= $_SESSION['msg_type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-3"></i>
            <?= $_SESSION['msg'] ?>
        </div>
        <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="bg-emerald-800/95 backdrop-blur-xl text-white fixed w-full top-0 z-40 shadow-2xl">
        <div class="max-w-7xl mx-auto px-6 py-5 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <i class="fas fa-hospital-symbol text-4xl animate-pulse"></i>
                <h1 class="text-3xl font-extrabold">KMC Hospital</h1>
            </div>
            <div class="flex items-center gap-8">
                <span class="hidden md:block text-xl">Welcome, <strong><?= $fname ?></strong></span>
                <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-8 py-4 rounded-full font-bold text-lg transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="pt-32 pb-20 px-6 max-w-7xl mx-auto">
        <h1 class="text-6xl md:text-8xl font-extrabold text-white text-center mb-6">
            Hello, <span class="text-emerald-200"><?= $fname ?>!</span>
        </h1>
        <p class="text-center text-emerald-100 text-2xl mb-16">Your health. Our care. Always.</p>

        <div class="grid lg:grid-cols-4 gap-10">

            <!-- Sidebar -->
            <div class="space-y-6">
                <button onclick="showTab('home')" class="tab-btn tab-active w-full text-left px-8 py-6 rounded-3xl text-xl font-bold flex items-center gap-4">
                    <i class="fas fa-home text-2xl"></i> Dashboard
                </button>
                <button onclick="showTab('book')" class="tab-btn w-full text-left px-8 py-6 rounded-3xl bg-white/90 hover:bg-white shadow-2xl text-xl font-bold flex items-center gap-4">
                    <i class="fas fa-calendar-plus text-2xl"></i> Book Appointment
                </button>
                <button onclick="showTab('appointments')" class="tab-btn w-full text-left px-8 py-6 rounded-3xl bg-white/90 hover:bg-white shadow-2xl text-xl font-bold flex items-center gap-4">
                    <i class="fas fa-calendar-check text-2xl"></i> My Appointments
                </button>
                <button onclick="showTab('bills')" class="tab-btn w-full text-left px-8 py-6 rounded-3xl bg-white/90 hover:bg-white shadow-2xl text-xl font-bold flex items-center gap-4">
                    <i class="fas fa-file-invoice-dollar text-2xl"></i> Bills & Prescriptions
                </button>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">

                <!-- Home Dashboard -->
                <div id="home" class="grid md:grid-cols-3 gap-8">
                    <div onclick="showTab('book')" class="card p-12 rounded-3xl text-center cursor-pointer hover:scale-105 transition-all duration-300 shadow-2xl">
                        <i class="fas fa-calendar-plus text-8xl text-emerald-600 mb-6"></i>
                        <h3 class="text-3xl font-bold">Book Appointment</h3>
                    </div>
                    <div onclick="showTab('appointments')" class="card p-12 rounded-3xl text-center cursor-pointer hover:scale-105 transition-all duration-300 shadow-2xl">
                        <i class="fas fa-calendar-check text-8xl text-emerald-600 mb-6"></i>
                        <h3 class="text-3xl font-bold">My Appointments</h3>
                    </div>
                    <div onclick="showTab('bills')" class="card p-12 rounded-3xl text-center cursor-pointer hover:scale-105 transition-all duration-300 shadow-2xl">
                        <i class="fas fa-file-medical text-8xl text-emerald-600 mb-6"></i>
                        <h3 class="text-3xl font-bold">Download Bills</h3>
                    </div>
                </div>

                <!-- Book Appointment -->
                <div id="book" class="hidden card rounded-3xl p-12 shadow-3xl">
                    <h2 class="text-5xl font-extrabold text-emerald-700 text-center mb-12">Book New Appointment</h2>
                    <form method="post" class="space-y-10">
                        <div class="grid md:grid-cols-2 gap-10">
                            <div>
                                <label class="block text-xl font-bold mb-4">Specialization</label>
                                <select id="spec" onchange="filterDoctors()" class="w-full px-8 py-5 rounded-2xl border-2 border-emerald-300 focus:border-emerald-600 outline-none text-lg">
                                    <option value="">All Specializations</option>
                                    <?php display_specs(); ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xl font-bold mb-4">Select Doctor</label>
                                <select name="doctor" id="doctor" onchange="updateFee()" required class="w-full px-8 py-5 rounded-2xl border-2 border-emerald-300 focus:border-emerald-600 outline-none text-lg">
                                    <option value="">Choose Doctor</option>
                                    <?php display_docs(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-10">
                            <div>
                                <label class="block text-xl font-bold mb-4">Fee</label>
                                <input type="text" id="feeDisplay" readonly class="w-full px-8 py-5 bg-emerald-50 rounded-2xl font-bold text-emerald-700 text-xl text-center">
                                <input type="hidden" name="docFees" id="docFees" required>
                            </div>
                            <div>
                                <label class="block text-xl font-bold mb-4">Date</label>
                                <input type="date" name="appdate" min="<?= date('Y-m-d') ?>" required class="w-full px-8 py-5 rounded-2xl border-2 border-emerald-300 focus:border-emerald-600 outline-none text-lg">
                            </div>
                            <div>
                                <label class="block text-xl font-bold mb-4">Time</label>
                                <select name="apptime" required class="w-full px-8 py-5 rounded-2xl border-2 border-emerald-300 focus:border-emerald-600 outline-none text-lg">
                                    <option value="">Select Time</option>
                                    <option value="09:00:00">9:00 AM</option>
                                    <option value="11:00:00">11:00 AM</option>
                                    <option value="14:00:00">2:00 PM</option>
                                    <option value="16:00:00">4:00 PM</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center pt-8">
                            <button type="submit" name="app-submit" class="btn text-white font-extrabold text-2xl px-24 py-7 rounded-3xl shadow-2xl transition transform hover:scale-110">
                                Book Now
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Appointments List -->
                <div id="appointments" class="hidden card rounded-3xl p-12 shadow-3xl">
                    <h2 class="text-5xl font-extrabold text-emerald-700 mb-10">My Appointments</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-emerald-100 text-emerald-800">
                                <tr>
                                    <th class="px-8 py-5">Doctor</th>
                                    <th class="px-8 py-5">Date</th>
                                    <th class="px-8 py-5">Time</th>
                                    <th class="px-8 py-5">Fee</th>
                                    <th class="px-8 py-5">Status</th>
                                    <th class="px-8 py-5">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q = mysqli_query($con, "SELECT ID, doctor, appdate, apptime, docFees, userStatus, doctorStatus FROM appointmenttb WHERE pid = $pid ORDER BY appdate DESC");
                                while ($r = mysqli_fetch_assoc($q)) {
                                    $status = ($r['userStatus'] == 1 && $r['doctorStatus'] == 1) ? '<span class="text-green-600 font-bold">Confirmed</span>' : '<span class="text-red-600 font-bold">Cancelled</span>';
                                    echo "<tr class='border-b hover:bg-emerald-50'>
                                        <td class='px-8 py-6'>Dr. {$r['doctor']}</td>
                                        <td class='px-8 py-6'>" . date('d M Y', strtotime($r['appdate'])) . "</td>
                                        <td class='px-8 py-6'>" . date('g:i A', strtotime($r['apptime'])) . "</td>
                                        <td class='px-8 py-6 font-bold'>रुपैयाँ{$r['docFees']}</td>
                                        <td class='px-8 py-6'>$status</td>
                                        <td class='px-8 py-6'>
                                            " . ($r['userStatus'] == 1 && $r['doctorStatus'] == 1 ? 
                                            "<a href='?cancel=1&ID={$r['ID']}' onclick='return confirm(\"Cancel this appointment?\")' class='bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold'>Cancel</a>" : 
                                            "<span class='text-gray-500'>Cancelled</span>") . "
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bills & Prescriptions -->
                <div id="bills" class="hidden card rounded-3xl p-12 shadow-3xl">
                    <h2 class="text-5xl font-extrabold text-emerald-700 mb-10">Medical Bills & Prescriptions</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-emerald-100 text-emerald-800">
                                <tr>
                                    <th class="px-8 py-5">Doctor</th>
                                    <th class="px-8 py-5">Date</th>
                                    <th class="px-8 py-5">Diagnosis</th>
                                    <th class="px-8 py-5">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q = mysqli_query($con, "SELECT ID, doctor, appdate, disease FROM prestb WHERE pid = $pid ORDER BY appdate DESC");
                                while ($r = mysqli_fetch_assoc($q)) {
                                    echo "<tr class='border-b hover:bg-emerald-50'>
                                        <td class='px-8 py-6 font-medium'>Dr. {$r['doctor']}</td>
                                        <td class='px-8 py-6'>" . date('d M Y', strtotime($r['appdate'])) . "</td>
                                        <td class='px-8 py-6'>" . htmlspecialchars($r['disease']) . "</td>
                                        <td class='px-8 py-6'>
                                            <a href='?bill=1&ID={$r['ID']}' class='bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-5 rounded-xl font-bold flex items-center gap-3'>
                                                <i class='fas fa-download'></i> Download PDF
                                            </a>
                                        </td>
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
            document.querySelectorAll('#home, #book, #appointments, #bills').forEach(el => el.classList.add('hidden'));
            document.getElementById(tab).classList.remove('hidden');
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('tab-active');
                btn.classList.add('bg-white/90');
            });
            event.target.classList.add('tab-active');
            event.target.classList.remove('bg-white/90');
        }

        function filterDoctors() {
            const spec = document.getElementById('spec').value;
            document.querySelectorAll('#doctor option').forEach(o => {
                if (o.value === "" || !spec || o.dataset.spec === spec) {
                    o.style.display = '';
                } else {
                    o.style.display = 'none';
                }
            });
            document.getElementById('doctor').value = '';
            updateFee();
        }

        function updateFee() {
            const selected = document.querySelector('#doctor option:checked');
            if (selected && selected.value) {
                document.getElementById('feeDisplay').value = 'रुपैयाँ' + selected.dataset.fee;
                document.getElementById('docFees').value = selected.dataset.fee;
            } else {
                document.getElementById('feeDisplay').value = '';
                document.getElementById('docFees').value = '';
            }
        }

        // Auto-hide alerts
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.style.opacity = '0';
        }, 5000);
    </script>
</body>
</html>