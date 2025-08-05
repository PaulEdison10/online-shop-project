<?php
session_start();
include '../config/db.php';
$uid = $_SESSION['user_id'] ?? 0;
$orders = $conn->query("
  SELECT o.*, p.product_name, p.price 
  FROM orders o 
  JOIN products p ON o.product_id = p.id 
  WHERE o.user_id = $uid 
  ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>🧾 Your Orders – PictusCode</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Google Fonts + Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #212121, #424242);
      color: #fff;
      min-height: 100vh;
      padding: 50px 15px;
      background-image: url('../uploads/LUCES GAMER.jpg');
      background-size: cover;
      background-attachment: fixed;
      background-position: center;
      backdrop-filter: blur(6px);
    }

    .orders-wrapper {
      max-width: 1000px;
      margin: auto;
    }

    .glass-box {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(14px);
      border-radius: 20px;
      box-shadow: 0 0 25px rgba(0, 255, 255, 0.05), 0 4px 30px rgba(0,0,0,0.2);
      padding: 30px;
      margin-bottom: 30px;
      transition: transform 0.4s ease;
      border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .glass-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 40px rgba(0, 255, 255, 0.15);
    }

    .order-header {
      text-align: center;
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 40px;
      color: #00e5ff;
      text-shadow: 0 0 8px rgba(0,255,255,0.6);
      animation: glowFade 2s infinite ease-in-out;
    }

    @keyframes glowFade {
      0%, 100% { text-shadow: 0 0 8px rgba(0,255,255,0.6); }
      50% { text-shadow: 0 0 18px rgba(0,255,255,0.9); }
    }

    .order-details {
      font-size: 1rem;
      line-height: 1.6;
    }

    .order-details strong {
      color: #ffeb3b;
    }

    .order-date {
      font-size: 0.95rem;
      color: #ccc;
      text-align: right;
    }

    .total-amount {
      color: #4caf50;
      font-weight: 600;
    }

    .no-orders {
      text-align: center;
      background: rgba(255, 255, 255, 0.07);
      padding: 30px;
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .no-orders h5 {
      font-size: 1.5rem;
      color: #ffca28;
    }

    .btn-shop {
      margin-top: 20px;
      background-color: #26a69a;
      border: none;
      padding: 10px 20px;
      border-radius: 25px;
      color: white;
      font-weight: bold;
      transition: 0.3s ease-in-out;
    }

    .btn-shop:hover {
      background-color: #1de9b6;
      transform: scale(1.05);
    }
  </style>
</head>
<body>

<div class="orders-wrapper">
  <h2 class="order-header">🧾 Your Orders</h2>

  <?php if ($orders->num_rows > 0): ?>
    <?php while ($o = $orders->fetch_assoc()): ?>
      <div class="glass-box">
        <div class="row">
          <div class="col-md-8 order-details">
            <p><strong>🛍️ Product:</strong> <?= htmlspecialchars($o['product_name']) ?></p>
            <p><strong>🔢 Quantity:</strong> <?= (int)$o['quantity'] ?></p>
            <p><strong>💰 Unit Price:</strong> ₹<?= number_format($o['price'], 2) ?></p>
            <p class="total-amount">Total: ₹<?= number_format($o['price'] * $o['quantity'], 2) ?></p>
          </div>
          <div class="col-md-4 order-date d-flex align-items-center justify-content-md-end justify-content-start mt-3 mt-md-0">
            <span>📅 <?= date("d M Y, h:i A", strtotime($o['created_at'])) ?></span>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="no-orders">
      <h5>No orders found!</h5>
      <p>You haven’t placed any orders yet.</p>
      <a href="view_shops.php" class="btn btn-shop">🛒 Start Shopping</a>
    </div>
  <?php endif; ?>
</div>

</body>
</html>


