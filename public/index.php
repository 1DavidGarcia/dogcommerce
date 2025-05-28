<?php
session_start();

require_once __DIR__ . '/../src/helpers/cart_helper.php';


require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/models/Product.php';


$cartItemCount = getCartItemCount();
$productModel = new Product();
$products = $productModel->getAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Inicio</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
  <style>
    header {
    margin-top: 0 !important;
    padding-top: 0 !important;
    }

    html, body {
      font-family: 'Inter', sans-serif;
      height: 100%;
      margin: 0;
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      overflow-x: hidden;
    }

    #particles-js {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: -1;
      background: linear-gradient(135deg, #1e1e3f, #0f0f1f);
    }
    .glass {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
    }
    .cart-badge {
      background: #ff0080;
      color: white;
      border-radius: 9999px;
      font-size: 0.75rem;
      padding: 2px 8px;
      position: absolute;
      top: -6px;
      right: -10px;
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
<body>

<div id="particles-js"></div>

<header class="glass px-8 py-4 rounded-2xl max-w-7xl mx-auto mt-6 flex items-center justify-between fade-in text-white shadow-lg">
  <h1 class="text-3xl font-extrabold tracking-wide flex items-center gap-2">🛍 <span>Catálogo</span></h1>

  <nav class="flex items-center gap-8 text-sm font-medium">
    <a href="faq.php" class="flex items-center gap-1 hover:text-blue-300 transition-all underline underline-offset-4 decoration-blue-400">
      <i class="ph-question-bold text-xl"></i> FAQ
    </a>

<?php if (isset($_SESSION['user'])): ?>
  <div class="flex items-center gap-4">
    <!-- Enlace al dashboard o a pedidos -->
    <a href="<?= $_SESSION['user']['role'] === 'admin' ? 'admin/dashboard.php' : 'orders.php' ?>"
       class="flex items-center gap-1 hover:text-green-400 transition-all underline underline-offset-4 decoration-green-400">
      <i class="ph-user-circle-bold text-xl"></i> <?= htmlspecialchars($_SESSION['user']['name']) ?>
    </a>

    <!-- Botón para cerrar sesión -->
    <a href="logout.php"
       class="flex items-center gap-1 text-red-400 hover:text-red-500 transition-all underline underline-offset-4 decoration-red-400">
      <i class="ph-sign-out-bold text-xl"></i> Cerrar sesión
    </a>
  </div>
<?php else: ?>
  <a href="login.php" class="flex items-center gap-1 hover:text-green-300 transition-all">
    <i class="ph-sign-in-bold text-xl"></i> Iniciar sesión
  </a>
<?php endif; ?>


    <a href="cart.php" class="relative flex items-center gap-1 hover:scale-105 transition-transform">
      <i class="ph-shopping-cart-simple text-2xl"></i>
      <?php if ($cartItemCount > 0): ?>
        <span class="absolute -top-2 -right-3 bg-pink-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow">
          <?= $cartItemCount ?>
        </span>
      <?php endif; ?>
    </a>
  </nav>
</header>

<main class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-10 fade-in text-white">
  <?php foreach ($products as $product): ?>
    <div class="glass p-6 rounded-xl shadow-lg hover:scale-105 transition-transform duration-300">
      <?php if (!empty($product['image'])): ?>
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover mb-4 rounded-lg shadow">
      <?php else: ?>
        <div class="w-full h-48 bg-gray-700 mb-4 rounded-lg flex items-center justify-center text-white/50">
          Sin imagen
        </div>
      <?php endif; ?>

      <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($product['name']) ?></h2>
      <p class="mb-2">💰 $<?= number_format($product['price'], 2) ?></p>
      <a href="cart_action.php?action=add&id=<?= $product['id'] ?>&name=<?= urlencode($product['name']) ?>&price=<?= $product['price'] ?>&image=<?= urlencode($product['image']) ?>" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-white inline-block transition shadow">
        Añadir al carrito
      </a>
    </div>
  <?php endforeach; ?>
</main>

<script>
  particlesJS("particles-js", {
    particles: {
      number: { value: 60, density: { enable: true, value_area: 800 } },
      color: { value: "#ffffff" },
      shape: { type: "circle" },
      opacity: { value: 0.2, random: true },
      size: { value: 4, random: true },
      move: { enable: true, speed: 1 }
    },
    interactivity: {
      detect_on: "canvas",
      events: { onhover: { enable: true, mode: "repulse" } },
      modes: { repulse: { distance: 100, duration: 0.4 } }
    },
    retina_detect: true
  });
</script>

<!-- Chatbot personalizado -->
<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  intent="WELCOME"
  chat-title="Asistente Virtual"
  agent-id="e9cd8eb2-97a8-4fee-8811-d26b411ab1a1"
  language-code="es"
  chat-icon="https://cdn-icons-png.flaticon.com/512/4712/4712035.png"
  user-id="cliente_actual"
  wait-open
  theme="dark">
</df-messenger>

<style>
  df-messenger {
    --df-messenger-button-titlebar-color: #4f46e5;
    --df-messenger-button-titlebar-font-color: #ffffff;
    --df-messenger-chat-background-color: #111827;
    --df-messenger-font-color: #ffffff;
    --df-messenger-send-icon: #60a5fa;
    --df-messenger-user-message: #2563eb;
    --df-messenger-bot-message: #374151;
    --df-messenger-font-family: 'Inter', sans-serif;
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    border-radius: 50px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  df-messenger:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.4);
  }
</style>

<footer class="mt-16 bg-white/5 backdrop-blur border-t border-white/10 text-white">
  <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">
    <div>
      <h2 class="text-xl font-extrabold mb-2">🛒 YEALTECH</h2>
      <p class="text-white/60">Sistema de gestión de productos & ventas.</p>
    </div>

    <div>
      <h3 class="font-bold mb-2">Enlaces útiles</h3>
      <ul class="space-y-1">
        <li><a href="/index.php" class="hover:text-indigo-400 transition underline underline-offset-4 decoration-indigo-400">Inicio</a></li>
        <li><a href="/admin/products.php" class="hover:text-indigo-400 transition underline underline-offset-4 decoration-indigo-400">Panel</a></li>
        <li><a href="/login.php" class="hover:text-indigo-400 transition underline underline-offset-4 decoration-indigo-400">Iniciar sesión</a></li>
      </ul>
    </div>

    <div>
      <h3 class="font-bold mb-2">Síguenos</h3>
      <ul class="space-y-1">
        <li><a href="https://www.facebook.com/people/YEAL-RENT-A-CAR/61553659971559/" target="_blank" class="hover:text-blue-400 transition underline underline-offset-4 decoration-blue-400">Facebook</a></li>
        <li><a href="https://www.instagram.com/" target="_blank" class="hover:text-pink-400 transition underline underline-offset-4 decoration-pink-400">Instagram</a></li>
        <li><a href="https://twitter.com/" target="_blank" class="hover:text-sky-400 transition underline underline-offset-4 decoration-sky-400">Twitter</a></li>
        <li><a href="https://www.youtube.com/" target="_blank" class="hover:text-red-400 transition underline underline-offset-4 decoration-red-400">YouTube</a></li>
      </ul>
    </div>
  </div>

  <div class="text-center text-white/50 text-xs py-4 border-t border-white/10">
    © <?= date('Y') ?> YEAL. Todos los derechos reservados.
  </div>
  <div class="text-center text-white/50 text-xs py-4 border-t border-white/10">
    <span>Número de teléfono: +503 2222-2424 | Atención al cliente: +503 7886-6436</span>
  </div>
</footer>


</body>
</html>