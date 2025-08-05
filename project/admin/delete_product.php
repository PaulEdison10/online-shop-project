<?php
include '../config/db.php';

if (!isset($_GET['id'])) {
    die("❌ Product ID is missing.");
}

$id = intval($_GET['id']);

// First, delete orders that reference this product
$conn->query("DELETE FROM orders WHERE product_id = $id");

// Then delete the image file (optional)
$product = $conn->query("SELECT image FROM products WHERE id = $id")->fetch_assoc();
if ($product && file_exists("../uploads/" . $product['image'])) {
    unlink("../uploads/" . $product['image']);
}

// Now delete the product
$delete = $conn->query("DELETE FROM products WHERE id = $id");

if ($delete) {
    header("Location: view_shops.php");
    exit;
} else {
    echo "❌ Failed to delete product.";
}
?>
