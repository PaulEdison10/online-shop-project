<?php
include '../config/db.php';

function showError($message) {
  echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: linear-gradient(-45deg, #ff416c, #ff4b2b, #ff6a00, #ffbb00);
      background-size: 400% 400%;
      animation: gradient 15s ease infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      color: white;
    }

    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .message-box {
      background: rgba(0, 0, 0, 0.3);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      text-align: center;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    .btn {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="message-box">
    <h2>⚠️ $message</h2>
    <a href="view_shops.php" class="btn btn-light">← Back to Shops</a>
  </div>
</body>
</html>
HTML;
  exit;
}

// Safety check for shop ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  showError("❌ Shop ID is missing or invalid.");
}

$id = intval($_GET['id']);

// Delete all related products
$deleteProducts = $conn->prepare("DELETE FROM products WHERE shop_id = ?");
$deleteProducts->bind_param("i", $id);
$deleteProducts->execute();

// Delete the shop
$deleteShop = $conn->prepare("DELETE FROM shops WHERE id = ?");
$deleteShop->bind_param("i", $id);

if ($deleteShop->execute()) {
  header("Location: view_shops.php");
  exit;
} else {
  showError("❌ Failed to delete shop.");
}


