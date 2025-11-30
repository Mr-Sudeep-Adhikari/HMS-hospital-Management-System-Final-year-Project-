<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal | KMC Hospital</title>
    <link rel="shortcut icon" href="images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #065f46 0%, #047857 30%, #10b981 70%, #34d399 100%);
            min-height: 100vh;
        }

        .card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            border-radius: 2rem;
        }

        .tab-btn {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tab-active {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white !important;
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(5, 150, 105, 0.4);
        }

        .btn-login {
            background: linear-gradient(135deg, #059669, #10b981);
            transition: all 0.4s ease;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #047857, #059669);
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(5, 150, 105, 0.5);
        }

        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #a7f3d0;
        }

        .input-field:focus {
            border-color: #059669;
            box-shadow: 0 0 0 5px rgba(5, 150, 105, 0.15);
            outline: none;
        }

        .glow-text {
            text-shadow: 0 0 30px rgba(16, 185, 129, 0.6);
        }
    </style>
</head>

<body class="gradient-bg flex items-center justify-center p-4 md:p-8">

    <div class="max-w-6xl w-full grid lg:grid-cols-2 gap-10 items-center">

        <!-- Left Side: Welcome Section -->
        <div class="text-white space-y-8 text-center lg:text-left">
            <div class="space-y-4">
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight glow-text">
                    Welcome<br>Back!
                </h1>
                <p class="text-xl md:text-2xl text-emerald-100 font-medium opacity-95">
                    Secure access to your healthcare portal
                </p>
            </div>

            <p class="text-lg text-emerald-200 max-w-lg mx-auto lg:mx-0">
                Manage appointments, view medical records, and connect with your care team — all in one place.
            </p>

            <div class="grid grid-cols-3 gap-6 mt-10">
                <div
                    class="bg-white/15 backdrop-blur-lg p-6 rounded-2xl border border-white/30 text-center transform hover:scale-110 transition">
                    <i class="fas fa-user-injured text-4xl mb-3 text-emerald-200"></i>
                    <p class="font-bold text-lg">Patients</p>
                </div>
                <div
                    class="bg-white/15 backdrop-blur-lg p-6 rounded-2xl border border-white/30 text-center transform hover:scale-110 transition">
                    <i class="fas fa-stethoscope text-4xl mb-3 text-emerald-200"></i>
                    <p class="font-bold text-lg">Doctors</p>
                </div>
                <div
                    class="bg-white/15 backdrop-blur-lg p-6 rounded-2xl border border-white/30 text-center transform hover:scale-110 transition">
                    <i class="fas fa-user-shield text-4xl mb-3 text-emerald-200"></i>
                    <p class="font-bold text-lg">Admin</p>
                </div>
            </div>

            <a href="index.php"
                class="inline-flex items-center mt-10 text-emerald-200 hover:text-white text-lg font-semibold transition">
                <i class="fas fa-arrow-left mr-3"></i> Back to Home
            </a>
        </div>

        <!-- Right Side: Login Card -->
        <div class="card p-8 lg:p-10">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-emerald-400 to-green-600 rounded-full mb-6 shadow-2xl">
                    <i class="fas fa-hospital text-4xl text-white"></i>
                </div>
                <h2 class="text-4xl font-bold text-gray-800">KMC Hospital</h2>
                <p class="text-emerald-600 font-medium mt-2">Secure Login Portal</p>
            </div>

            <!-- Tabs -->
            <div class="flex bg-emerald-50 rounded-2xl p-2 mb-10 shadow-inner">
                <button onclick="switchTab('patient')" id="tab-patient"
                    class="tab-btn tab-active flex-1 py-4 rounded-xl font-bold text-lg">
                    <i class="fas fa-user-injured mr-2"></i> Patient
                </button>
                <button onclick="switchTab('doctor')" id="tab-doctor"
                    class="tab-btn flex-1 py-4 rounded-xl font-bold text-gray-700 text-lg">
                    <i class="fas fa-stethoscope mr-2"></i> Doctor
                </button>
                <button onclick="switchTab('admin')" id="tab-admin"
                    class="tab-btn flex-1 py-4 rounded-xl font-bold text-gray-700 text-lg">
                    <i class="fas fa-user-shield mr-2"></i> Admin
                </button>
            </div>

            <!-- Patient Login -->
            <form id="form-patient" action="func.php" method="POST" class="space-y-7">
                <div>
                    <label class="block font-bold text-gray-700 text-lg mb-3">Email Address</label>
                    <input type="email" name="email" placeholder="you@example.com" required
                        class="input-field w-full px-6 py-4 rounded-2xl text-lg">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 text-lg mb-3">Password</label>
                    <input type="password" name="password2" placeholder="Enter your password" required
                        class="input-field w-full px-6 py-4 rounded-2xl text-lg">
                </div>
                <button type="submit" name="pat_sub"
                    class="w-full btn-login text-white py-5 rounded-2xl text-xl shadow-xl transform hover:scale-105">
                    Login as Patient
                </button>
                <p class="text-center text-gray-600">
                    Don't have an account? <a href="index.php#register"
                        class="text-emerald-600 font-bold hover:underline">Register here</a>
                </p>
            </form>

            <!-- Doctor Login -->
            <form id="form-doctor" action="func.php" method="POST" class="space-y-7 hidden">
                <div>
                    <label class="block font-bold text-gray-700 text-lg mb-3">Doctor Username</label>
                    <input type="text" name="username" placeholder="dr.smith" required
                        class="input-field w-full px-6 py-4 rounded-2xl text-lg">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 text-lg mb-3">Password</label>
                    <input type="password" name="password3" placeholder="Enter password" required
                        class="input-field w-full px-6 py-4 rounded-2xl text-lg">
                </div>
                <button type="submit" name="doc_sub"
                    class="w-full btn-login text-white py-5 rounded-2xl text-xl shadow-xl">
                    Login as Doctor
                </button>
            </form>

            <!-- Admin Login -->
            <form id="form-admin" action="func.php" method="POST" class="space-y-7 hidden">
                <div>
                    <label class="block font-bold text-gray-700 text-lg mb-3">Admin Username</label>
                    <input type="text" name="username" placeholder="admin" required
                        class="input-field w-full px-6 py-4 rounded-2xl text-lg">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 text-lg mb-3">Password</label>
                    <input type="password" name="password" placeholder="Enter admin password" required
                        class="input-field w-full px-6 py-4 rounded-2xl text-lg">
                </div>
                <button type="submit" name="admin_sub"
                    class="w-full btn-login text-white py-5 rounded-2xl text-xl shadow-xl">
                    Login as Admin
                </button>
            </form>

        </div>
    </div>

    <script>
        function switchTab(role) {
            // Hide all forms
            ['patient', 'doctor', 'admin'].forEach(r => {
                document.getElementById('form-' + r).classList.add('hidden');
                document.getElementById('tab-' + r).classList.remove('tab-active');
            });

            // Show selected
            document.getElementById('form-' + role).classList.remove('hidden');
            document.getElementById('tab-' + role).classList.add('tab-active');
        }

        // Default: Patient tab active
        switchTab('patient');
    </script>

</body>

</html>