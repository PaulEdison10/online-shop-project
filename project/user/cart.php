<?php
session_start();
include '../config/db.php';
$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;
if ($product_id) {
  $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + $quantity;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cart – PictusCode</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <!-- Google Fonts + Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(-45deg, #c33764, #1d2671, #4a00e0, #8e2de2);
      background-size: 400% 400%;
      animation: gradientMove 10s ease infinite;
      font-family: 'Inter', sans-serif;
      color: #fff;
    }

    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .container {
      margin-top: 60px;
      max-width: 600px;
      background: rgba(255, 255, 255, 0.07);
      padding: 30px;
      border-radius: 15px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0,0,0,0.25);
    }

    h3 {
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 25px;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }

    .list-group-item {
      background: rgba(255,255,255,0.1);
      border: none;
      color: #fff;
      backdrop-filter: blur(4px);
      transition: background 0.3s ease;
    }

    .list-group-item:hover {
      background: rgba(255,255,255,0.2);
    }

    .btn-success {
      background-color: #00c897;
      border: none;
      padding: 12px 25px;
      font-weight: 600;
      font-size: 1rem;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      transition: transform 0.2s ease, background 0.3s ease;
    }

    .btn-success:hover {
      background-color: #00a878;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="container">
    <h3>🛒 Your Cart</h3>
    <form method="POST" action="order.php">
      <ul class="list-group mb-4">
        <?php
          $total = 0;
          if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $id => $qty) {
              $p = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
              $subtotal = $p['price'] * $qty;
              $total += $subtotal;
              echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                      <span>{$p['product_name']} × $qty</span>
                      <strong>₹$subtotal</strong>
                    </li>";
            }
          } else {
            echo "<li class='list-group-item'>Your cart is empty.</li>";
          }
        ?>
      </ul>
      <?php if ($total > 0): ?>
        <input type="hidden" name="checkout" value="1">
        <button class="btn btn-success w-100">Place Order (₹<?= $total ?>)</button>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>

