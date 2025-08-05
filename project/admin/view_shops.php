<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shops & Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(-45deg, #1d2b64, #f8cdda, #1fa2ff, #12d8fa);
      background-size: 400% 400%;
      animation: bgGradient 15s ease infinite;
      font-family: 'Inter', sans-serif;
      color: #fff;
    }

    @keyframes bgGradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .container {
      padding-top: 50px;
    }

    .shop-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 16px;
      border: 1px solid rgba(255,255,255,0.15);
      padding: 20px;
      margin-bottom: 30px;
      backdrop-filter: blur(8px);
      transition: transform 0.3s ease, background 0.3s ease;
    }

    .shop-card:hover {
      transform: translateY(-5px);
      background: rgba(255, 255, 255, 0.08);
    }

    .shop-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: #fff;
    }

    .product-list {
      list-style: none;
      padding-left: 0;
      margin-top: 15px;
    }

    .product-item {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      padding: 10px 20px;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #f8f9fa;
      transition: background 0.2s ease;
    }

    .product-item:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .product-name {
      font-weight: 500;
      font-size: 1rem;
    }

    .product-price {
      font-weight: bold;
      font-size: 1rem;
    }

    .product-img {
      width: 45px;
      height: 45px;
      object-fit: cover;
      border-radius: 6px;
      margin-left: 15px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    h3 {
      font-size: 2rem;
      font-weight: bold;
      text-align: center;
      margin-bottom: 40px;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
    }

    .btn-edit, .btn-delete {
      font-size: 0.8rem;
      padding: 2px 8px;
      margin-left: 5px;
    }

    .shop-actions {
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <h3>📋 Shop List with Products</h3>
  <?php
    $shops = $conn->query("SELECT * FROM shops");
    while ($shop = $shops->fetch_assoc()) {
      echo "<div class='shop-card'>
              <div class='shop-title'>
                {$shop['name']} <small class='text-muted' style='color:#ccc'>({$shop['location']})</small>
              </div>
              <div class='shop-actions'>
                <a href='edit_shop.php?id={$shop['id']}' class='btn btn-sm btn-primary btn-edit'>Edit Shop</a>
                <a href='delete_shop.php?id={$shop['id']}' class='btn btn-sm btn-danger btn-delete' onclick=\"return confirm('Delete this shop? All related products will also be removed.')\">Delete Shop</a>
              </div>
              <ul class='product-list'>";
      $products = $conn->query("SELECT * FROM products WHERE shop_id={$shop['id']}");
      while ($p = $products->fetch_assoc()) {
        echo "<li class='product-item'>
                <span class='product-name'>{$p['product_name']}</span>
                <span class='product-price'>₹{$p['price']}</span>
                <img src='../uploads/{$p['image']}' class='product-img' alt=''>
                <div>
                  <a href='edit_product.php?id={$p['id']}' class='btn btn-sm btn-warning btn-edit'>Edit</a>
                  <a href='delete_product.php?id={$p['id']}' class='btn btn-sm btn-danger btn-delete' onclick=\"return confirm('Delete this product?')\">Delete</a>
                </div>
              </li>";
      }
      echo "</ul></div>";
    }
  ?>
</div>

</body>
</html>
