<?php include '../config/db.php'; session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>🛍️ Browse Shops – PictusCode</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap & Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      color: #fff;
      min-height: 100vh;
    }

    .header-title {
      font-weight: 700;
      font-size: 2.5rem;
      text-shadow: 1px 2px 4px rgba(0,0,0,0.3);
      animation: fadeInDown 1.2s;
    }

    .shop-card {
      background: rgba(255, 255, 255, 0.1);
      border: none;
      border-radius: 20px;
      padding: 20px;
      backdrop-filter: blur(15px);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .shop-card:hover {
      transform: scale(1.02);
      box-shadow: 0 8px 30px rgba(255, 255, 255, 0.15);
    }

    .shop-name {
      font-size: 1.4rem;
      font-weight: 600;
      color: #fff;
    }

    .shop-location {
      color: #cfd8dc;
      font-size: 0.9rem;
    }

    .product-line {
      background: rgba(255, 255, 255, 0.07);
      border-radius: 12px;
      padding: 10px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      transition: all 0.3s ease-in-out;
    }

    .product-line:hover {
      background-color: rgba(255, 255, 255, 0.12);
    }

    .product-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 12px;
      border: 2px solid #fff;
    }

    .product-line strong {
      font-size: 1.1rem;
    }

    .add-btn {
      border-radius: 8px;
      padding: 6px 14px;
      font-size: 0.9rem;
      background-color: #00e676;
      color: #000;
      border: none;
      transition: background 0.3s ease;
    }

    .add-btn:hover {
      background-color: #1de9b6;
    }

    .card-content {
      margin-top: 10px;
    }

    .section-divider {
      height: 2px;
      width: 60px;
      background: #00e5ff;
      margin: 10px auto 30px;
    }

    @keyframes fadeInDown {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h2 class="text-center header-title">🛍️ Browse Shops & Products</h2>
    <div class="section-divider"></div>
    <div class="row mt-4">
      <?php
        $shops = $conn->query("SELECT * FROM shops");
        while ($shop = $shops->fetch_assoc()) {
          echo "<div class='col-md-6 mb-4'>
                  <div class='shop-card animate__animated animate__fadeInUp'>
                    <h5 class='shop-name'>{$shop['name']}</h5>
                    <p class='shop-location'>📍 {$shop['location']}</p>";

          $products = $conn->query("SELECT * FROM products WHERE shop_id={$shop['id']}");
          if ($products->num_rows > 0) {
            while ($p = $products->fetch_assoc()) {
              $imgPath = "../uploads/" . $p['image'];
              echo "<div class='product-line'>
                      <img src='{$imgPath}' class='product-img' alt='Product'>
                      <div class='flex-grow-1 ms-3'>
                        <strong>{$p['product_name']}</strong><br>
                        <small>₹{$p['price']}</small>
                      </div>
                      <form method='POST' action='cart.php'>
                        <input type='hidden' name='product_id' value='{$p['id']}'>
                        <input type='hidden' name='quantity' value='1'>
                        <button class='add-btn'>Add</button>
                      </form>
                    </div>";
            }
          } else {
            echo "<p class='text-muted'>No products available.</p>";
          }

          echo "   </div>
                </div>";
        }
      ?>
    </div>
  </div>
</body>
</html>

