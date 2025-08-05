<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register – PictusCode</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      height: 100vh;
      background: linear-gradient(-45deg, #3a1c71, #d76d77, #ffaf7b, #43cea2);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .register-card {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 40px 30px;
      width: 100%;
      max-width: 420px;
      color: white;
      box-shadow: 0 0 30px rgba(0,0,0,0.3);
      animation: slideUp 1s ease;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-control {
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: white;
    }

    .form-control::placeholder {
      color: #ddd;
    }

    .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
      background-color: rgba(255, 255, 255, 0.2);
      color: white;
    }

    .nav-tabs .nav-link {
      color: #fff;
      background-color: transparent;
      border: none;
    }

    .nav-tabs .nav-link.active {
      font-weight: bold;
      color: #ffeb3b;
      border-bottom: 2px solid #fff;
    }

    .btn-success {
      background-color: #43a047;
      border: none;
      box-shadow: 0 0 10px #43a047, 0 0 40px #43a047;
    }

    .btn-success:hover {
      background-color: #2e7d32;
      box-shadow: 0 0 20px #43a047, 0 0 60px #43a047;
    }

    .alert {
      margin-top: 20px;
      font-weight: 500;
    }

    a {
      color: #ffeb3b;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="d-flex justify-content-center mb-4">
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="#">Register</a>
        </li>
      </ul>
    </div>

    <div class="mx-auto register-card">
      <h3 class="text-center mb-4">Create Your Account</h3>

      <form method="POST">
        <input class="form-control mb-3" name="name" placeholder="Full Name" required>
        <input class="form-control mb-3" name="email" placeholder="Email" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
        <button class="btn btn-success w-100" name="register">Register</button>
      </form>

      <?php
        if (isset($_POST['register'])) {
          $name = $_POST['name'];
          $email = $_POST['email'];
          $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
          $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')");
          echo "<div class='alert alert-success text-center'>🎉 Registration successful! <a href='login.php'>Login now</a></div>";
        }
      ?>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
