<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$user_id = $_SESSION['user']['id'];

// Obtener pedidos del usuario
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Pedidos</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #1f1f2e, #121219);
      color: white;
    }
    .glass {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
    }
  </style>
</head>
<body class="min-h-screen p-6">
  <div class="max-w-5xl mx-auto">
    <h1 class="text-4xl font-extrabold mb-8 text-center">📦 Mis Pedidos</h1>

    <?php if (empty($orders)): ?>
      <div class="text-center text-white/80">No has realizado ningún pedido.</div>
    <?php else: ?>
      <?php foreach ($orders as $order): ?>
        <div class="glass p-6 rounded-xl mb-6">
          <h2 class="text-2xl font-bold mb-2">Pedido #<?= $order['id'] ?></h2>
          <p class="mb-2">🕒 Fecha: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
          <p class="mb-2">💳 Transacción: <?= $order['paypal_transaction_id'] ?: 'N/A' ?></p>
          <p class="mb-2">💰 Total: <span class="text-green-400 font-semibold">$<?= number_format($order['total'], 2) ?></span></p>
          <p class="mb-2">📌 Estado: <span class="font-bold text-green-400"><?= ucfirst($order['status']) ?></span></p>

          <!-- Productos del pedido -->
          <div class="mt-4">
            <h3 class="text-lg font-bold mb-2">🧾 Productos:</h3>
            <ul class="list-disc list-inside text-white/80">
              <?php
                $stmtItems = $conn->prepare("
                  SELECT oi.quantity, oi.price, p.name 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = ?
                ");
                $stmtItems->execute([$order['id']]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                foreach ($items as $item):
              ?>
                <li><?= $item['quantity'] ?> × <?= htmlspecialchars($item['name']) ?> — $<?= number_format($item['price'], 2) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="text-center mt-10">
      <a href="index.php" class="inline-block bg-white/10 hover:bg-white/20 px-5 py-3 rounded-xl text-white font-semibold transition">
        ⬅️ Volver al catálogo
      </a>
    </div>
  </div>
</body>
</html>
