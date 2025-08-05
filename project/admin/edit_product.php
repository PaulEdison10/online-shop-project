<?php
include '../config/db.php';

if (!isset($_GET['id'])) {
  die("❌ Product ID is missing.");
}

$id = intval($_GET['id']);
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();

if (!$product) {
  die("❌ Product not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['product_name'];
  $price = $_POST['price'];

  if ($_FILES['image']['name']) {
    $imageName = time() . '_' . basename($_FILES['image']['name']);
    $targetPath = "../uploads/" . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
  } else {
    $imageName = $product['image'];
  }

  $stmt = $conn->prepare("UPDATE products SET product_name = ?, price = ?, image = ? WHERE id = ?");
  $stmt->bind_param("sssi", $name, $price, $imageName, $id);

  if ($stmt->execute()) {
    header("Location: view_shops.php");
    exit;
  } else {
    echo "❌ Failed to update product.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🛠️ Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea, #764ba2, #ff6a00);
      background-size: 400% 400%;
      animation: gradientFlow 12s ease infinite;
      color: #fff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @keyframes gradientFlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .edit-form {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      width: 100%;
      max-width: 600px;
      animation: fadeIn 1.2s ease-in;
    }

    @keyframes fadeIn {
      0% { opacity: 0; transform: scale(0.95); }
      100% { opacity: 1; transform: scale(1); }
    }

    .edit-form label {
      color: #fff;
      font-weight: 500;
    }

    .edit-form input[type="text"],
    .edit-form input[type="number"],
    .edit-form input[type="file"] {
      background-color: rgba(255, 255, 255, 0.2);
      border: none;
      color: #fff;
    }

    .edit-form input:focus {
      background-color: rgba(255, 255, 255, 0.3);
      box-shadow: none;
      color: #fff;
    }

    .btn-primary {
      background-color: #ff6a00;
      border: none;
    }

    .btn-primary:hover {
      background-color: #ff8c42;
    }

    .btn-secondary {
      background-color: rgba(255, 255, 255, 0.3);
      border: none;
    }

    img.preview {
      width: 100px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    h3 {
      text-align: center;
      margin-bottom: 25px;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="edit-form">
  <h3>✏️ Edit Product</h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Product Name</label>
      <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']) ?>" required>
    </div>

    <div class="mb-3">
      <label>Price (₹)</label>
      <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
    </div>

    <div class="mb-3">
      <label>Current Image</label><br>
      <img src="../uploads/<?= $product['image'] ?>" class="preview mb-2" alt="Product Image">
    </div>

    <div class="mb-3">
      <label>Change Image</label>
      <input type="file" name="image" class="form-control">
    </div>

    <div class="d-flex justify-content-between">
      <button type="submit" class="btn btn-primary">Update Product</button>
      <a href="view_shops.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

</body>
</html>
