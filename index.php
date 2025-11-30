<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC Hospital | Login Portal</title>
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

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .btn-primary {
            background: linear-gradient(to right, #059669, #10b981);
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(5, 150, 105, 0.4);
        }

        .tab-active {
            background: #059669;
            color: white;
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.3);
        }

        .form-input {
            transition: all 0.3s;
        }

        .form-input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-emerald-900/30 backdrop-blur-md border-b border-white/10 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center text-white">
            <div class="flex items-center gap-3">
                <div class="bg-white text-emerald-700 p-2 rounded-lg">
                    <i class="fas fa-hospital-symbol text-2xl"></i>
                </div>
                <span class="text-2xl font-bold tracking-wide">KMC Hospital</span>
            </div>
            <div class="flex gap-6 font-medium">
                <a href="index.php" class="hover:text-emerald-300 transition">Home</a>
                <a href="about.html" class="hover:text-emerald-300 transition">About</a>
                <a href="contact.html" class="hover:text-emerald-300 transition">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center p-6">
        <div
            class="glass-card w-full max-w-5xl rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[600px]">

            <!-- Left Side: Branding -->
            <div
                class="md:w-2/5 bg-emerald-900/80 text-white p-10 flex flex-col justify-center relative overflow-hidden">
                <div
                    class="absolute top-0 left-0 w-full h-full opacity-20 bg-[url('https://images.unsplash.com/photo-1538108149393-fbbd81895907?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80')] bg-cover bg-center">
                </div>
                <div class="relative z-10">
                    <h2 class="text-4xl font-extrabold mb-6">Welcome Back!</h2>
                    <p class="text-emerald-100 text-lg mb-8 leading-relaxed">Access your dashboard to manage
                        appointments, view medical records, and connect with our specialists.</p>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-700 flex items-center justify-center">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>24/7 Online Booking</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-700 flex items-center justify-center">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <span>Expert Doctors</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-700 flex items-center justify-center">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <span>Secure Records</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Forms -->
            <div class="md:w-3/5 p-10 bg-white/80">

                <!-- Tabs -->
                <div class="flex flex-wrap gap-3 mb-8 justify-center">
                    <button onclick="switchTab('patient-reg')"
                        class="tab-btn tab-active px-6 py-2 rounded-full font-bold text-sm transition-all border border-emerald-100 hover:bg-emerald-50 text-emerald-700">Register</button>
                    <button onclick="switchTab('patient-login')"
                        class="tab-btn px-6 py-2 rounded-full font-bold text-sm transition-all border border-emerald-100 hover:bg-emerald-50 text-emerald-700">Patient</button>
                    <button onclick="switchTab('doctor-login')"
                        class="tab-btn px-6 py-2 rounded-full font-bold text-sm transition-all border border-emerald-100 hover:bg-emerald-50 text-emerald-700">Doctor</button>
                    <button onclick="switchTab('admin-login')"
                        class="tab-btn px-6 py-2 rounded-full font-bold text-sm transition-all border border-emerald-100 hover:bg-emerald-50 text-emerald-700">Admin</button>
                </div>

                <!-- Patient Registration Form -->
                <form id="patient-reg" action="func2.php" method="post" class="space-y-4 animate-fade-in">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="fname" placeholder="First Name" required
                            class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                        <input type="text" name="lname" placeholder="Last Name" required
                            class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                    </div>
                    <input type="email" name="email" placeholder="Email Address" required
                        class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                    <input type="tel" name="contact" placeholder="Phone Number" required
                        class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">

                    <div class="grid grid-cols-2 gap-4">
                        <input type="password" name="password" id="password" placeholder="Password" required
                            onkeyup="check()"
                            class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                        <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password" required
                            onkeyup="check()"
                            class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                    </div>
                    <div id="message" class="text-sm font-bold text-center h-5"></div>

                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="Male" checked
                                class="text-emerald-600 focus:ring-emerald-500"> Male
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="Female"
                                class="text-emerald-600 focus:ring-emerald-500"> Female
                        </label>
                    </div>

                    <button type="submit" name="patsub1"
                        class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-lg transition-transform">Register
                        Account</button>
                </form>

                <!-- Patient Login Form -->
                <form id="patient-login" action="func.php" method="post" class="space-y-6 hidden animate-fade-in">
                    <div class="text-center mb-6">
                        <i class="fas fa-user-injured text-5xl text-emerald-600 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-800">Patient Login</h3>
                    </div>
                    <input type="email" name="email" placeholder="Email Address" required
                        class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                    <input type="password" name="password2" placeholder="Password" required
                        class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                    <button type="submit" name="patsub"
                        class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-lg transition-transform">Login</button>
                </form>

                <!-- Doctor Login Form -->
                <form id="doctor-login" action="func1.php" method="post" class="space-y-6 hidden animate-fade-in">
                    <div class="text-center mb-6">
                        <i class="fas fa-user-md text-5xl text-emerald-600 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-800">Doctor Login</h3>
                    </div>
                    <input type="text" name="username3" placeholder="Username" required
                        class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                    <input type="password" name="password3" placeholder="Password" required
                        class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                    <button type="submit" name="docsub1"
                        class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-lg transition-transform">Login</button>
                </form>

                <!-- Admin Login Form -->
                <form id="admin-login" action="func3.php" method="post" class="space-y-6 hidden animate-fade-in">
                    <div class="text-center mb-6">
                        <i class="fas fa-user-shield text-5xl text-emerald-600 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-800">Admin Login</h3>
                    </div>
                    <input type="text" name="username1" placeholder="Username" required
                        class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                    <input type="password" name="password2" placeholder="Password" required
                        class="form-input w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:outline-none">
                    <button type="submit" name="adsub"
                        class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-lg transition-transform">Login</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        // Switch tabs
        function switchTab(tabId) {
            // Hide all forms
            document.querySelectorAll('form').forEach(f => f.classList.add('hidden'));

            // Show selected form
            document.getElementById(tabId).classList.remove('hidden');

            // Remove highlight from all tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('tab-active', 'bg-emerald-600', 'text-white');
                btn.classList.add('text-emerald-700');
            });

            // Add highlight to clicked tab
            const activeTab = document.querySelector(`[data-tab="${tabId}"]`);
            if (activeTab) {
                activeTab.classList.add('tab-active', 'bg-emerald-600', 'text-white');
                activeTab.classList.remove('text-emerald-700');
            }
        }

        // Password match check
        function checkPasswordMatch() {
            const pass = document.getElementById('password');
            const cpass = document.getElementById('cpassword');
            const msg = document.getElementById('message');

            if (!pass || !cpass || !msg) return;

            if (pass.value === cpass.value) {
                msg.style.color = 'green';
                msg.innerHTML = 'Passwords Match';
            } else {
                msg.style.color = 'red';
                msg.innerHTML = 'Passwords Do Not Match';
            }
        }
    </script>

</body>

</html>