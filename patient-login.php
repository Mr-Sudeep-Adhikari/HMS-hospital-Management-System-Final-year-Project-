<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login - KMC Hospital</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            min-height: 100vh;
            display: flex;
        }

        .left-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            color: white;
        }

        .ambulance-icon {
            font-size: 8rem;
            margin-bottom: 2rem;
        }

        .left-panel h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .left-panel p {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            text-align: center;
            max-width: 500px;
        }

        .quick-links {
            display: flex;
            gap: 3rem;
            margin-top: 2rem;
        }

        .quick-link {
            text-align: center;
            color: white;
        }

        .quick-link-icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .right-panel {
            flex: 0 0 500px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
        }

        .navbar-links {
            position: absolute;
            top: 2rem;
            right: 2rem;
            display: flex;
            gap: 1.5rem;
        }

        .navbar-links a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .navbar-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .login-card {
            text-align: center;
        }

        .user-icon {
            width: 100px;
            height: 100px;
            background: #0d9488;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 3rem;
            color: white;
        }

        h2 {
            color: #0d9488;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .subtitle {
            color: #666;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #0d9488;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 1rem 1rem 1rem 45px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            background: #e0f2fe;
            color: #333;
        }

        input:focus {
            outline: 2px solid #0d9488;
        }

        .show-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #0d9488;
            cursor: pointer;
            font-size: 0.9rem;
            user-select: none;
        }

        .login-btn {
            background: #0d9488;
            color: white;
            padding: 1rem 3rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }

        .login-btn:hover {
            background: #0f766e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 148, 136, 0.4);
        }

        @media (max-width: 968px) {
            body {
                flex-direction: column;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                flex: 1;
            }
        }
    </style>
</head>

<body>
    <div class="navbar-links">
        <a href="index.php">Back to Home</a>
        <a href="#">Contact</a>
    </div>

    <div class="left-panel">
        <div class="ambulance-icon">🚑</div>
        <h1>Welcome Back!</h1>
        <p>Your health journey continues here. Login to book appointments, view prescriptions & more.</p>
        <div class="quick-links">
            <div class="quick-link">
                <div class="quick-link-icon">📅</div>
                <div>Book Appointments</div>
            </div>
            <div class="quick-link">
                <div class="quick-link-icon">💊</div>
                <div>View Prescriptions</div>
            </div>
        </div>
    </div>

    <div class="right-panel">
        <div class="login-card">
            <div class="user-icon">👤</div>
            <h2>Patient Login</h2>
            <p class="subtitle">Sign in to access your account</p>
            <form method="post" action="func.php">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">📧</span>
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password2" id="password" placeholder="Enter your password"
                            required>
                        <span class="show-password" onclick="togglePassword()">Show</span>
                    </div>
                </div>
                <button type="submit" name="patsub" class="login-btn">Login to Dashboard</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const btn = event.target;
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = 'Hide';
            } else {
                input.type = 'password';
                btn.textContent = 'Show';
            }
        }
    </script>
</body>

</html>