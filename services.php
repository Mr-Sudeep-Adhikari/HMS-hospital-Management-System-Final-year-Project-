<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$con = mysqli_connect("localhost", "root", "", "hospitaldatabase");
if (!$con) die("Database Connection Failed!");

// Auto-create services table with real data (only runs once)
if (mysqli_num_rows(mysqli_query($con, "SHOW TABLES LIKE 'hospital_services'")) == 0) {
    mysqli_query($con, "CREATE TABLE hospital_services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100),
        icon VARCHAR(50),
        description TEXT,
        status ENUM('available','soon') DEFAULT 'available'
    )");

    $xyz_services = [
        ["Online Appointment Booking", "fa-calendar-check", "Book appointments with specialist doctors 24/7", "available"],
        ["Digital Prescription & Bills", "fa-file-medical", "Download medical records and bills as PDF instantly", "available"],
        ["24/7 Emergency Service", "fa-ambulance", "Fully equipped ICU and emergency response team", "available"],
        ["Advanced Laboratory", "fa-vials", "Accurate pathology and diagnostic reports online", "available"],
        ["Digital X-Ray & Ultrasound", "fa-x-ray", "High-resolution imaging with instant reports", "available"],
        ["Telemedicine Consultation", "fa-video", "Consult doctors from home (Video Call)", "soon"],
        ["Home Medicine Delivery", "fa-truck-medical", "Get prescribed medicines delivered", "soon"],
        ["Patient Health Records", "fa-folder-open", "Lifetime access to all medical history", "available"]
    ];

    $stmt = mysqli_prepare($con, "INSERT INTO hospital_services (title, icon, description, status) VALUES (?, ?, ?, ?)");
    foreach ($xyz_services as $s) {
        mysqli_stmt_bind_param($stmt, "ssss", $s[0], $s[1], $s[2], $s[3]);
        mysqli_stmt_execute($stmt);
    }
}
$services = mysqli_query($con, "SELECT * FROM hospital_services ORDER BY id");
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | XYZ Hospital - MediPulse</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #064e3b 0%, #059669 40%, #10b981 100%); background-size: 200% 200%; animation: gradient 20s ease infinite; }
        @keyframes gradient { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
        .card { background: rgba(255,255,255,0.98); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.4); transition: all 0.6s; }
        .card:hover { transform: translateY(-25px) scale(1.05); box-shadow: 0 50px 100px rgba(5,150,105,0.5); border-color: #10b981; }
        .btn { background: linear-gradient(to right, #059669, #10b981); }
        .btn:hover { transform: translateY(-12px) scale(1.1); box-shadow: 0 40px 80px rgba(5,150,105,0.7); }
        .float { animation: float 8s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-35px)} }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.7} }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">

    <!-- Floating Icons -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-20">
        <i class="fas fa-heartbeat text-9xl absolute top-20 left-10 float"></i>
        <i class="fas fa-stethoscope text-8xl absolute bottom-32 right-20 float" style="animation-delay:2s;"></i>
    </div>

    <!-- Navbar -->
    <nav class="bg-emerald-800/95 backdrop-blur-xl fixed w-full top-0 z-50 shadow-2xl">
        <div class="max-w-7xl mx-auto px-6 py-5 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <i class="fas fa-hospital text-5xl pulse"></i>
                <h1 class="text-4xl font-extrabold tracking-wider">XYZ Hospital</h1>
            </div>
            <div class="flex gap-8 text-lg font-medium">
                <a href="index.php" class="hover:text-emerald-200 transition">Home</a>
                <a href="services.php" class="text-emerald-200 font-bold underline">Services</a>
                <a href="patientlogin.php" class="bg-white text-emerald-700 px-8 py-3 rounded-full font-bold hover:bg-emerald-100 transition">Patient Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-32 pb-20 px-6 text-center">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-7xl md:text-9xl font-extrabold mb-8">
                Our <span class="text-emerald-200">Services</span>
            </h1>
            <p class="text-2xl md:text-4xl text-emerald-100 max-w-5xl mx-auto">
                Advanced healthcare solutions at <strong>XYZ Hospital</strong> — powered by MediPulse
            </p>
        </div>
    </section>

    <!-- Dynamic Services -->
    <section class="py-20 px-6 bg-white/10 backdrop-blur-lg">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-5xl md:text-7xl font-extrabold text-center mb-20">Healthcare Services at XYZ Hospital</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-12">
                <?php while ($s = mysqli_fetch_assoc($services)): ?>
                <div class="card p-10 rounded-3xl shadow-2xl text-center group">
                    <div class="w-28 h-28 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-8 group-hover:scale-110 transition">
                        <i class="fas <?= $s['icon'] ?> text-5xl text-emerald-700"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-emerald-800"><?= htmlspecialchars($s['title']) ?></h3>
                    <p class="text-gray-700 text-lg leading-relaxed"><?= htmlspecialchars($s['description']) ?></p>
                    <div class="mt-6">
                        <?php if ($s['status'] == 'soon'): ?>
                            <span class="px-6 py-3 bg-orange-100 text-orange-700 rounded-full font-bold">Coming Soon</span>
                        <?php else: ?>
                            <span class="px-6 py-3 bg-emerald-100 text-emerald-700 rounded-full font-bold">Available</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 px-6 bg-white/10 backdrop-blur-lg">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="text-6xl md:text-8xl font-extrabold mb-10">Welcome to XYZ Hospital</h2>
            <p class="text-3xl text-emerald-100 mb-12">Your Health. Our Priority. Always.</p>
            <a href="patientlogin.php" class="btn text-white font-extrabold text-3xl px-32 py-8 rounded-3xl shadow-2xl transition transform hover:scale-110 inline-block">
                Patient Portal Login
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-emerald-900/90 py-16 text-center">
        <div class="max-w-7xl mx-auto px-6">
            <h1 class="text-6xl font-extrabold mb-4">XYZ Hospital</h1>
            <p class="text-2xl opacity-90 mb-4">Kathmandu, Nepal</p>
            <p class="text-emerald-200 text-lg">
                Powered by <strong>MediPulse</strong> • UI: <strong>Emerald Pulse</strong><br>
                © 2025 XYZ Hospital • All Rights Reserved
            </p>
        </div>
    </footer>
</body>
</html>