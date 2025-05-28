<?php
require_once __DIR__ . '/../../src/middleware/is_admin.php';
require_once __DIR__ . '/../../config/database.php';

$user = $_SESSION['user'];

$db = new Database();
$conn = $db->connect();

// Estadísticas
$totalProductos = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsuarios = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPedidos = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            z-index: -1;
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }
        .glass-button {
            display: block;
            padding: 1rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }
        .glass-button:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.05);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-8 text-white">

<div id="particles-js"></div>

<!-- Panel -->
<div class="glass p-10 rounded-3xl shadow-lg max-w-4xl w-full text-center space-y-8 border border-white/20 fade-in">
    <h1 class="text-3xl font-extrabold">👨‍💼 Bienvenido, <?= htmlspecialchars($user['name']) ?></h1>
    <p class="text-white/60 text-sm">Panel de administración</p>

    <!-- Resumen -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm font-semibold text-white">
        <div class="glass-button">
            🛒 Productos<br><span class="text-2xl font-bold text-green-400"><?= $totalProductos ?></span>
        </div>
        <div class="glass-button">
            👥 Usuarios<br><span class="text-2xl font-bold text-blue-400"><?= $totalUsuarios ?></span>
        </div>
        <div class="glass-button">
            📦 Pedidos<br><span class="text-2xl font-bold text-yellow-400"><?= $totalPedidos ?></span>
        </div>
    </div>

    <!-- Acciones -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm font-semibold text-white mt-6">
        <a href="../../public/index.php" class="glass-button">🏠 Ir al catálogo</a>
        <a href="create_product.php" class="glass-button">➕ Crear producto</a>
        <a href="products.php" class="glass-button">🛠 Gestionar productos</a>
        <a href="orders.php" class="glass-button">📦 Ver pedidos</a>
            <a href="users.php" class="bg-white/10 hover:bg-white/20 transition p-4 rounded-xl flex items-center justify-center gap-2">
      <i class="ph ph-users-three"></i> Gestionar usuarios
        <a href="../logout.php" class="glass-button hover:bg-red-600/80">🚪 Cerrar sesión</a>
    </div>
</div>

<!-- Partículas -->
<script>
particlesJS("particles-js", {
    particles: {
        number: { value: 60, density: { enable: true, value_area: 800 } },
        color: { value: "#ffffff" },
        shape: { type: "circle" },
        opacity: { value: 0.15 },
        size: { value: 3, random: true },
        move: { enable: true, speed: 1.5 }
    },
    interactivity: {
        detect_on: "canvas",
        events: {
            onhover: { enable: true, mode: "repulse" }
        },
        modes: {
            repulse: { distance: 80, duration: 0.4 }
        }
    },
    retina_detect: true
});
</script>
</body>
</html>
