<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// 🔐 Seguridad básica
$data = json_decode(file_get_contents("php://input"), true);

// Validación mínima
if (
    !$data || 
    !isset($data['details']) || 
    !isset($_SESSION['cart']) || 
    empty($_SESSION['cart']) || 
    !isset($_SESSION['user']['id'])
) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos, carrito vacío o usuario no autenticado']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->connect();

    $user_id = $_SESSION['user']['id'];
    $total = $data['details']['purchase_units'][0]['amount']['value'];
    $transaction_id = $data['details']['id'] ?? uniqid('txn_');

    // 🚀 Iniciar transacción
    $conn->beginTransaction();

    // 🧾 Insertar pedido
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, paypal_transaction_id, created_at) VALUES (?, ?, 'paid', ?, NOW())");
    $stmt->execute([$user_id, $total, $transaction_id]);
    $order_id = $conn->lastInsertId();

    // 🛒 Insertar productos del pedido
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product_id, $item['quantity'], $item['price']]);

        // 📉 Actualizar stock
        $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $product_id]);
    }

    // ✅ Confirmar todo
    $conn->commit();

    // 🧹 Limpiar carrito
    unset($_SESSION['cart']);

    // 👌 Enviar éxito
    echo json_encode(['success' => true, 'redirect' => 'success.php']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar el pedido: ' . $e->getMessage()
    ]);
}
