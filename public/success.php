<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Guardar el pedido en la base de datos aquí si aún no lo haces

// Limpiar carrito
unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pago Exitoso</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="bg-green-900 text-white flex items-center justify-center min-h-screen font-inter">
  <div class="text-center space-y-6 p-8 bg-white/10 rounded-2xl border border-white/20 backdrop-blur shadow-xl">
    <h1 class="text-4xl font-extrabold">✅ ¡Pago realizado con éxito!</h1>
    <p class="text-lg text-white/70">Gracias por tu compra.</p>
    <a href="orders.php" class="bg-green-500 hover:bg-green-600 px-6 py-3 rounded-xl font-semibold transition shadow-lg">
      📦 Ver pedidos
    </a>
  </div>
</body>
</html>
