<?php
require_once __DIR__ . '/../../src/middleware/is_admin.php';
require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: products.php");
exit();

