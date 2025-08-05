<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body, html {
      height: 100%;
      margin: 0;
      background: linear-gradient(-45deg, #0f2027, #203a43, #2c5364);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
    }

    @keyframes gradientBG {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }

    .card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      color: #fff;
      padding: 2rem;
      transition: all 0.3s ease-in-out;
    }

    .card:hover {
      transform: scale(1.02);
      background: rgba(255, 255, 255, 0.15);
    }

    .form-control {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: #fff;
    }

    .form-control::placeholder {
      color: #ccc;
    }

    .btn-primary {
      background-color: #00c6ff;
      border: none;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background-color: #0072ff;
    }
  </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
  <div class="card w-100" style="max-width: 400px;">
    <h3 class="text-center mb-4">🏪 Add New Shop</h3>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $location = $_POST['location'];

        $stmt = $conn->prepare("INSERT INTO shops (name, location) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $location);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>✅ Shop added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
    ?>

    <form method="POST">
      <div class="mb-3">
        <input type="text" name="name" class="form-control" placeholder="Shop Name" required>
      </div>
      <div class="mb-3">
        <input type="text" name="location" class="form-control" placeholder="Location" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Add Shop</button>
    </form>
  </div>
</div>

</body>
</html>

