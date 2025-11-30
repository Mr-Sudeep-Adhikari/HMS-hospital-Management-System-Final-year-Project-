<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logged Out | KMC Hospital</title>
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
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
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

<body class="gradient-bg min-h-screen flex items-center justify-center px-6">
  <div
    class="card p-12 rounded-3xl shadow-2xl text-center max-w-lg w-full transform hover:scale-105 transition-all duration-500">
    <div class="bg-emerald-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8">
      <i class="fas fa-sign-out-alt text-5xl text-emerald-600 ml-2"></i>
    </div>
    <h1 class="text-4xl font-extrabold text-emerald-800 mb-4">Logged Out</h1>
    <p class="text-gray-600 text-lg mb-10">You have been successfully logged out of the system.</p>

    <a href="index.php"
      class="btn text-white font-bold text-xl px-10 py-4 rounded-2xl shadow-lg inline-block transition-all w-full">
      <i class="fas fa-arrow-left mr-2"></i> Return to Login
    </a>
  </div>
</body>

</html>