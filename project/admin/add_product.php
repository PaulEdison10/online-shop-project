<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product – Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(-45deg, #3a1c71, #d76d77, #ffaf7b, #43cea2);
      background-size: 400% 400%;
      animation: gradientBG 12s ease infinite;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .card {
      background-color: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
      transition: 0.3s ease;
    }

    .card:hover {
      transform: scale(1.02);
    }

    .form-control, .form-select {
      background-color: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.3);
      color: white;
    }

    .form-control::placeholder {
      color: #ddd;
    }

    .form-control:focus, .form-select:focus {
      border-color: #ffd700;
      box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
    }

    .btn-success {
      background-color: #28a745;
      border: none;
      font-weight: bold;
    }

    .btn-success:hover {
      background-color: #218838;
    }

    .alert-success {
      background-color: rgba(40, 167, 69, 0.2);
      color: #fff;
      border: 1px solid #28a745;
    }

    h3 {
      font-weight: bold;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;">

  <div class="card p-4" style="width: 100%; max-width: 600px;">
    <h3 class="text-center mb-4">📦 Add Product</h3>
    <form method="POST" enctype="multipart/form-data">
      <select class="form-select mb-3" name="shop_id" required>
        <option value="">🛒 Select Shop</option>
        <?php
          $shops = $conn->query("SELECT * FROM shops");
          while ($s = $shops->fetch_assoc()) {
            echo "<option value='{$s['id']}'>{$s['name']}</option>";
          }
        ?>
      </select>

      <input class="form-control mb-3" name="product_name" placeholder="🎁 Product Name" required>
      <input class="form-control mb-3" name="price" placeholder="💰 Price" type="number" required>
      <input class="form-control mb-3" type="file" name="image" accept="image/*" required>

      <button class="btn btn-success w-100 py-2" name="add">➕ Add Product</button>
    </form>

    <?php
      if (isset($_POST['add'])) {
        $shop_id = $_POST['shop_id'];
        $product = $_POST['product_name'];
        $price = $_POST['price'];
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        if ($shop_id && $product && $price && $image) {
          move_uploaded_file($tmp, "../uploads/" . $image);
          $conn->query("INSERT INTO products (shop_id, product_name, price, image) VALUES ('$shop_id', '$product', '$price', '$image')");
          echo "<div class='alert alert-success mt-3'>✅ Product <strong>$product</strong> added successfully!</div>";
        } else {
          echo "<div class='alert alert-danger mt-3'>❌ All fields are required.</div>";
        }
      }
    ?>
  </div>

</body>
</html>
