<?php
include '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  die("❌ Shop ID not found.");
}

// Fetch existing shop details
$stmt = $conn->prepare("SELECT * FROM shops WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$shop = $result->fetch_assoc();

if (!$shop) {
  die("❌ Shop not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $location = $_POST['location'];

  $update = $conn->prepare("UPDATE shops SET name = ?, location = ? WHERE id = ?");
  $update->bind_param("ssi", $name, $location, $id);
  if ($update->execute()) {
    header("Location: view_shops.php");
    exit;
  } else {
    echo "<div class='alert alert-danger text-center mt-3'>❌ Update failed. Try again.</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>✏️ Edit Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(-45deg, #667eea, #764ba2, #6ee7b7, #facc15);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .form-container {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
      max-width: 500px;
      width: 100%;
      color: #fff;
      animation: slideIn 1s ease;
    }

    @keyframes slideIn {
      from { transform: translateY(50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .form-label {
      font-weight: 600;
    }

    .form-control {
      background: rgba(255,255,255,0.8);
      border: none;
      border-radius: 8px;
    }

    .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.3);
    }

    .btn-primary {
      background-color: #00c3ff;
      border: none;
      padding: 10px;
      font-weight: bold;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #00a0cc;
      transform: scale(1.05);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      font-weight: 700;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>✏️ Edit Shop</h2>
  <form method="post">
    <div class="mb-3">
      <label for="name" class="form-label">Shop Name</label>
      <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($shop['name']) ?>">
    </div>
    <div class="mb-3">
      <label for="location" class="form-label">Location</label>
      <input type="text" class="form-control" id="location" name="location" required value="<?= htmlspecialchars($shop['location']) ?>">
    </div>
    <button type="submit" class="btn btn-primary w-100">Update Shop</button>
  </form>
</div>

</body>
</html>

