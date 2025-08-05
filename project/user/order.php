<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
  die("Login or add items first.");
}

$uid = $_SESSION['user_id'];
foreach ($_SESSION['cart'] as $pid => $qty) {
  $conn->query("INSERT INTO orders (user_id, product_id, quantity) VALUES ($uid, $pid, $qty)");
}
unset($_SESSION['cart']);
header("Location: orders.php");
exit;
