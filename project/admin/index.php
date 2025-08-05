<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard – PictusCode</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #0f2027; /* fallback for older browsers */
      overflow-x: hidden;
    }

    #particles-js {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
      top: 0;
      left: 0;
    }

    .glass-bg {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
      padding: 30px;
    }

    .navbar {
      background-color: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(6px);
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: 600;
    }

    .card {
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255,255,255,0.1);
      color: white;
      border-radius: 18px;
      transition: 0.4s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.4);
    }

    .card:hover {
      background-color: rgba(255, 255, 255, 0.12);
      transform: translateY(-5px) scale(1.03);
      box-shadow: 0 10px 20px rgba(0,0,0,0.5);
    }

    h2 {
      font-size: 2.2rem;
      font-weight: bold;
      color: #ffffff;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.6);
    }

    .btn-outline-light {
      border-radius: 8px;
      transition: 0.3s;
    }

    .btn-outline-light:hover {
      background-color: #00e5ff;
      color: #000;
      font-weight: bold;
      border-color: #00e5ff;
      transform: scale(1.05);
    }

    .nav-link {
      color: #ccc !important;
      font-weight: 500;
    }

    .nav-link.active,
    .nav-link:hover {
      color: #00f7ff !important;
    }

    .card h4 {
      font-size: 1.4rem;
    }

    .card p {
      font-size: 0.95rem;
      color: #e0e0e0;
    }

    @media(max-width: 768px) {
      .card {
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>

<!-- Particles Background -->
<div id="particles-js"></div>

<nav class="navbar navbar-expand-lg navbar-dark px-4 py-2">
  <a class="navbar-brand" href="#">✨ Admin Panel</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="adminNav">
    <ul class="navbar-nav me-auto">
      <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
      <li class="nav-item"><a class="nav-link" href="manage_orders.php">Orders</a></li>
      <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
    </ul>
    <a class="btn btn-outline-light" href="project/index.php">Logout</a>
  </div>
</nav>

<div class="container mt-5 glass-bg">
  <h2 class="text-center mb-5">Welcome, Admin 🧑‍💼</h2>
  <div class="row justify-content-center text-center g-4">
    <div class="col-md-4">
      <div class="card p-4">
        <h4>🛍️ Manage Products</h4>
        <p>Add or update items in your shop.</p>
        <a href="add_product.php" class="btn btn-outline-light mt-2">Add Products</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-4">
        <h4>🏬 Add Shop</h4>
        <p>Register new shops and their details.</p>
        <a href="add_shop.php" class="btn btn-outline-light mt-2">Add New Shop</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-4">
        <h4>📦 Items Overview</h4>
        <p>Check all shops and their products.</p>
        <a href="view_shops.php" class="btn btn-outline-light mt-2">Shop List</a>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Particles JS -->
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
  particlesJS("particles-js", {
    "particles": {
      "number": {
        "value": 80,
        "density": {
          "enable": true,
          "value_area": 800
        }
      },
      "color": { "value": "#ffffff" },
      "shape": {
        "type": "circle",
        "stroke": { "width": 0, "color": "#000000" }
      },
      "opacity": {
        "value": 0.5,
        "random": false
      },
      "size": {
        "value": 3,
        "random": true
      },
      "line_linked": {
        "enable": true,
        "distance": 150,
        "color": "#ffffff",
        "opacity": 0.4,
        "width": 1
      },
      "move": {
        "enable": true,
        "speed": 3,
        "direction": "none",
        "random": false,
        "straight": false,
        "out_mode": "out"
      }
    },
    "interactivity": {
      "detect_on": "canvas",
      "events": {
        "onhover": { "enable": true, "mode": "repulse" },
        "onclick": { "enable": true, "mode": "push" }
      },
      "modes": {
        "repulse": { "distance": 100 },
        "push": { "particles_nb": 4 }
      }
    },
    "retina_detect": true
  });
</script>

</body>
</html>


