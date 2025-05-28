<?php
require_once __DIR__ . '/../../helpers/cart_helper.php';
$cartItemCount = getCartItemCount();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Catálogo de Productos</h1>
        <a href="../../public/cart.php" class="text-blue-600 font-semibold">
            🛒 Carrito (<?= $cartItemCount ?>)
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($products as $product): ?>
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold"><?= htmlspecialchars($product['name']) ?></h2>
                <p class="text-gray-600 mb-2">Precio: $<?= number_format($product['price'], 2) ?></p>
                <a href="../../src/controllers/CartController.php?action=add&id=<?= $product['id'] ?>&name=<?= urlencode($product['name']) ?>&price=<?= $product['price'] ?>" 
                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Añadir al carrito
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
