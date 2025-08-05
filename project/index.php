<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Welcome | PictusCode Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      background: url('images/Purple sky.jpg') no-repeat center center fixed;
      background-size: cover;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .glass-box {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 50px 40px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      text-align: center;
      color: #fff;
      animation: fadeIn 1.5s ease;
    }

    .glass-box img {
      width: 80px;
      margin-bottom: 20px;
      animation: float 3s ease-in-out infinite;
    }

    .glass-box h1 {
      font-size: 32px;
      margin-bottom: 15px;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
      animation: slideIn 1.2s ease-in-out;
    }

    .glass-box p {
      font-size: 16px;
      margin-bottom: 30px;
      color: #f0f0f0;
    }

    .btn-custom {
      padding: 12px 28px;
      margin: 5px;
      border-radius: 30px;
      font-weight: bold;
      transition: 0.3s ease-in-out;
    }

    .btn-login {
      background: #920da1;
      color: white;
      box-shadow: 0 0 10px #1e88e5, 0 0 20px #1e88e5;
    }

    .btn-login:hover {
      background: #e010d9;
      box-shadow: 0 0 20px #1e88e5, 0 0 40px #1e88e5;
    }

    .btn-register {
      background: #870be6;
      color: white;
      box-shadow: 0 0 10px #43a047, 0 0 20px #43a047;
    }

    .btn-register:hover {
      background: #2e7d32;
      box-shadow: 0 0 20px #43a047, 0 0 40px #43a047;
    }

    .btn-admin {
      background: #ff5722;
      color: white;
      box-shadow: 0 0 10px #ff5722, 0 0 20px #ff5722;
    }

    .btn-admin:hover {
      background: #e64a19;
      box-shadow: 0 0 20px #ff5722, 0 0 40px #ff5722;
    }

    @keyframes fadeIn {
      0% { opacity: 0; transform: scale(0.95); }
      100% { opacity: 1; transform: scale(1); }
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    @keyframes slideIn {
      from { transform: translateY(-30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 768px) {
      .glass-box {
        padding: 30px 20px;
      }

      .glass-box h1 {
        font-size: 24px;
      }
    }
  </style>
</head>
<body>
  <div class="glass-box">
    <img src="images/shopcart.jpg" alt="Logo" />
    <h1>Welcome to PictusCode Shop</h1>
    <p>Your valuable shopping experience begins here</p>
    <div>
      <a href="user/login.php" class="btn btn-custom btn-login">Login</a>
      <a href="user/register.php" class="btn btn-custom btn-register">Register</a>
      <a href="admin/index.php" class="btn btn-custom btn-admin">Admin</a>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>  

