<?php
session_start();
include '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login – PictusCode</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      background: url('../images/shutter-speed-BQ9usyzHx_w-unsplash.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 40px;
      max-width: 400px;
      width: 100%;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
      animation: fadeIn 1.2s ease;
    }

    .glass-card h4 {
      color: #fff;
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
      text-shadow: 0 2px 5px rgba(0,0,0,0.5);
    }

    .glass-card input {
      background: rgba(255, 255, 255, 0.8);
      border: none;
      border-radius: 10px;
      padding: 12px;
      margin-bottom: 20px;
    }

    .glass-card .btn {
      background-color: #007bff;
      color: white;
      border-radius: 25px;
      padding: 10px;
      font-weight: bold;
      box-shadow: 0 0 10px #007bff;
      transition: 0.3s;
    }

    .glass-card .btn:hover {
      background-color: #0056b3;
      box-shadow: 0 0 20px #007bff, 0 0 40px #007bff;
    }

    .glass-card .nav-tabs .nav-link {
      color: #fff;
    }

    .glass-card .nav-tabs .nav-link.active {
      background-color: rgba(255,255,255,0.2);
      border-radius: 10px;
      font-weight: bold;
    }

    .alert {
      font-size: 14px;
      margin-top: 15px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="glass-card">
    <ul class="nav nav-tabs justify-content-center mb-3">
      <li class="nav-item"><a class="nav-link active" href="#">Login</a></li>
      <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
    </ul>

    <h4>🔐 Login to your account</h4>

    <?php
      if (isset($_POST['login'])) {
        $email = $conn->real_escape_string($_POST['email']);
        $pw = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $u = $result->fetch_assoc();

        if ($u && password_verify($pw, $u['password'])) {
          $_SESSION['user_id'] = $u['id'];
          $_SESSION['user_name'] = $u['name'];
          header("Location: index.php");
          exit;
        } else {
          echo "<div class='alert alert-danger'>❌ Invalid email or password</div>";
        }

        $stmt->close();
      }
    ?>

    <form method="POST">
      <input type="email" class="form-control" name="email" placeholder="Email" required />
      <input type="password" class="form-control" name="password" placeholder="Password" required />
      <button type="submit" name="login" class="btn w-100">Login</button>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


