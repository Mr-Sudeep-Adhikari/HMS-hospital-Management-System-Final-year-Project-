<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Login - KMC Hospital</title>
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
            flex-direction: column;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.1);
            padding: 1rem 3rem;
            display: flex;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #0d9488;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .card {
            background: #f0fdf4;
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 850px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            justify-content: center;
        }

        .tab {
            padding: 0.8rem 2rem;
            border: none;
            background: white;
            color: #0d9488;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .tab.active {
            background: #0d9488;
            color: white;
        }

        .tab:hover {
            transform: translateY(-2px);
        }

        .login-content {
            text-align: center;
        }

        .icon {
            font-size: 5rem;
            color: #0d9488;
            margin-bottom: 1.5rem;
        }

        h2 {
            color: #0d9488;
            margin-bottom: 2rem;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            max-width: 400px;
            padding: 1rem 1.2rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            background: #e0f2fe;
            color: #333;
        }

        input:focus {
            outline: 2px solid #0d9488;
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
            max-width: 400px;
            margin-top: 1rem;
        }

        .login-btn:hover {
            background: #0f766e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 148, 136, 0.4);
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <div class="logo-icon">🏥</div>
            <span>KMC Hospital</span>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="tabs">
                <a href="index.php" class="tab">Patient Registration</a>
                <a href="doctor-login.php" class="tab">Doctor Login</a>
                <span class="tab active">Receptionist Login</span>
            </div>

            <div class="login-content">
                <div class="icon">👨‍⚕️</div>
                <h2>Receptionist Login</h2>
                <form method="post" action="func3.php" autocomplete="off">
                    <div class="form-group">
                        <input type="text" name="username1" placeholder="Email or Username" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password2" placeholder="Password" required
                            autocomplete="new-password">
                    </div>
                    <button type="submit" name="adsub" class="login-btn">Login as Receptionist</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>