<?php
require_once __DIR__ . '/../../src/middleware/is_admin.php';
require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$conn = $db->connect();

$products = $conn->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(120deg, #0f0c29, #302b63, #24243e);
        }

        .glass {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .fade-in {
            animation: fadeIn 1s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="text-white">

<!-- Partículas de fondo -->
<div id="particles-js"></div>

<!-- Contenido -->
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <h1 class="text-3xl font-extrabold drop-shadow">📦 Panel de Productos</h1>
    <div class="flex gap-4">
        <a href="dashboard.php" 
           class="bg-white/20 hover:bg-white/30 text-white px-6 py-2 rounded-xl font-semibold transition shadow hover:shadow-white/20">
            ⬅️ Volver al Dashboard
        </a>
        <a href="create_product.php" 
           class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-xl font-bold transition shadow-lg hover:shadow-green-500/50">
            ➕ Nuevo Producto
        </a>
    </div>
</div>


    <div class="overflow-x-auto glass rounded-2xl">
        <table class="w-full text-left text-sm">
            <thead class="text-white/80 border-b border-white/20">
                <tr>
                    <th class="p-4">ID</th>
                    <th class="p-4">Nombre</th>
                    <th class="p-4">Precio</th>
                    <th class="p-4">Stock</th>
                    <th class="p-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr class="transition hover:bg-white/5 border-b border-white/10">
                    <td class="p-4"><?= $product['id'] ?></td>
                    <td class="p-4"><?= htmlspecialchars($product['name']) ?></td>
                    <td class="p-4">$<?= number_format($product['price'], 2) ?></td>
                    <td class="p-4"><?= $product['stock'] ?></td>
                    <td class="p-4">
                        <a href="edit_product.php?id=<?= $product['id'] ?>" class="text-blue-400 hover:underline">Editar</a> |
                        <a href="delete_product.php?id=<?= $product['id'] ?>" 
                           class="text-red-400 hover:underline" 
                           onclick="return confirm('¿Eliminar producto?')">
                           Eliminar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Partículas -->
<script>
    particlesJS("particles-js", {
        particles: {
            number: { value: 50, density: { enable: true, value_area: 900 } },
            color: { value: "#ffffff" },
            shape: { type: "circle" },
            opacity: { value: 0.1 },
            size: { value: 3, random: true },
            move: { enable: true, speed: 1 }
        },
        interactivity: {
            events: { onhover: { enable: true, mode: "repulse" } },
            modes: { repulse: { distance: 80, duration: 0.4 } }
        },
        retina_detect: true
    });
</script>

</body>
</html>

