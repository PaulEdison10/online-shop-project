<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$name = $_SESSION['user_name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard – PictusCode</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      min-height: 100vh;
      color: white;
      overflow-x: hidden;
    }

    .navbar {
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
      animation: slideDown 1s ease-in-out;
    }

    @keyframes slideDown {
      0% { transform: translateY(-100%); opacity: 0; }
      100% { transform: translateY(0); opacity: 1; }
    }

    h2 {
      animation: fadeIn 1.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(15px);
      border-radius: 16px;
      color: white;
      transition: all 0.4s ease;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }

    .card:hover {
      transform: translateY(-5px) scale(1.03);
      box-shadow: 0 0 25px rgba(255, 255, 255, 0.2);
      background-color: rgba(255, 255, 255, 0.08);
    }

    .btn-outline-light {
      border-color: #fff;
      color: #fff;
      transition: all 0.3s ease-in-out;
    }

    .btn-outline-light:hover {
      background-color: #fff;
      color: #111;
      box-shadow: 0 0 10px #fff;
    }

    .nav-link.active {
      color: #ffc107 !important;
      font-weight: bold;
    }

    .nav-link {
      color: #ccc !important;
      transition: color 0.3s;
    }

    .nav-link:hover {
      color: #fff !important;
    }

    .glow-box {
      animation: glow 2s infinite alternate ease-in-out;
    }

    @keyframes glow {
      from {
        box-shadow: 0 0 10px #00c9ff88, 0 0 20px #92fe9d55;
      }
      to {
        box-shadow: 0 0 25px #00c9ffcc, 0 0 40px #92fe9dcc;
      }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark px-4">
  <a class="navbar-brand fw-bold" href="#">🧑‍💼 User Panel</a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav me-auto">
      <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="view_shops.php">Products</a></li>
      <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
      <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
    </ul>
    <span class="navbar-text me-3">👋 Hello, <?= htmlspecialchars($name) ?></span>
    <a href="logout.php" class="btn btn-danger">Logout</a>

  </div>
</nav>

<div class="container mt-5">
  <h2 class="text-center mb-4">✨ Welcome, <?= htmlspecialchars($name) ?>!</h2>
  <div class="row text-center">
    <div class="col-md-4 mb-4">
      <div class="card p-4 glow-box">
        <h4>🛍️ Shop</h4>
        <p>Browse and view all available products.</p>
        <a href="view_shops.php" class="btn btn-outline-light mt-2">View Products</a>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card p-4 glow-box">
        <h4>🛒 Cart</h4>
        <p>See what you’ve added to your shopping cart.</p>
        <a href="cart.php" class="btn btn-outline-light mt-2">View Cart</a>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card p-4 glow-box">
        <h4>📦 Orders</h4>
        <p>Check your previous order history.</p>
        <a href="orders.php" class="btn btn-outline-light mt-2">View Orders</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
