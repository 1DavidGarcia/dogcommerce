<?php
session_start();
require_once __DIR__ . '/../src/helpers/cart_helper.php';
require_once __DIR__ . '/../config/database.php';

$cart = $_SESSION['cart'] ?? [];
$cartItemCount = getCartItemCount();
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://www.paypal.com/sdk/js?client-id=AUs3SbL3rGO6cpOL9fULcDuuVJZlwsbvtCBFYVD53IJ_aD42NmvAG4fljtPpZlFm-EmVZjJZtmG80InL&currency=USD"></script>
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
            background: radial-gradient(circle at 20% 20%, #1f1f2e, #121219);
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
        }
        tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .fade-in {
            animation: fadeIn 1s ease-out forwards;
            opacity: 0;
            transform: translateY(40px);
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .qty-btn {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: bold;
        }
        .qty-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="text-white">

<div id="particles-js"></div>
<div class="max-w-6xl mx-auto p-6 fade-in">
    <h1 class="text-4xl font-extrabold mb-8 text-center drop-shadow-lg">🛒 Tu Carrito</h1>

    <div id="carrito-container">
        <?php if (empty($cart)): ?>
            <div class="text-center text-lg text-white/80">
                El carrito está vacío.
                <div class="mt-4">
                    <a href="index.php" class="bg-white/10 hover:bg-white/20 text-white px-5 py-3 rounded-xl font-semibold transition inline-block">
                        ⬅️ Regresar a la tienda
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto glass rounded-2xl p-4">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-white/80 border-b border-white/20">
                            <th class="p-3">Producto</th>
                            <th class="p-3">Precio</th>
                            <th class="p-3">Cantidad</th>
                            <th class="p-3">Subtotal</th>
                            <th class="p-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <?php foreach ($cart as $id => $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr class="transition duration-300 border-b border-white/10 hover:bg-white/5">
                            <td class="p-3 flex items-center gap-4">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-14 h-14 rounded object-cover border border-white/20">
                                <?php else: ?>
                                    <div class="w-14 h-14 bg-gray-700 rounded flex items-center justify-center text-white/50 text-sm">N/A</div>
                                <?php endif; ?>
                                <span><?= htmlspecialchars($item['name']) ?></span>
                            </td>
                            <td class="p-3">$<?= number_format($item['price'], 2) ?></td>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <button onclick="updateQuantity('decrease', <?= $id ?>)" class="qty-btn">−</button>
                                    <span class="px-2" id="qty-<?= $id ?>"><?= $item['quantity'] ?></span>
                                    <button onclick="updateQuantity('increase', <?= $id ?>)" class="qty-btn">+</button>
                                </div>
                            </td>
                            <td class="p-3">$<?= number_format($subtotal, 2) ?></td>
                            <td class="p-3">
                                <a href="cart_action.php?action=remove&id=<?= $id ?>" class="text-red-400 font-semibold hover:underline">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <a href="index.php" class="bg-white/10 hover:bg-white/20 text-white px-5 py-3 rounded-xl font-semibold transition flex items-center gap-2">
                    ⬅️ Seguir comprando
                </a>
                <a href="cart_action.php?action=clear" class="bg-red-500 hover:bg-red-700 text-white px-5 py-3 rounded-xl font-bold transition shadow-lg flex items-center gap-2">
                    🗑 Vaciar carrito
                </a>
            </div>

            <div class="mt-6 text-2xl font-bold text-center">
                🧾 Total a pagar: <span class="text-green-400">$<?= number_format($total, 2) ?></span>
            </div>

            <div id="paypal-button-container" class="mt-10 max-w-sm mx-auto"></div>
            <script>
           paypal.Buttons({
  createOrder: function(data, actions) {
    return actions.order.create({
      purchase_units: [{
        amount: { value: '<?= number_format($total, 2, '.', '') ?>' }
      }]
    });
  },
  onApprove: function(data, actions) {
    return actions.order.capture().then(function(details) {
      // Enviar datos a PaypalController
      return fetch('PaypalController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          orderID: data.orderID,
          payerID: data.payerID,
          details: details
        })
      })
      .then(response => response.json())
      .then(result => {
        if (result.success) {
          // ✅ Redirigir a success
          window.location.href = 'success.php';
        } else {
          alert('Error al registrar el pedido.');
        }
      });
    });
  }
}).render('#paypal-button-container');

            </script>
        <?php endif; ?>
    </div>
</div>

<script>
function updateQuantity(action, id) {
    fetch(`cart_action.php?action=${action}&id=${id}`)
        .then(() => location.reload())
        .then(() => {
            Toastify({
                text: action === 'increase' ? "Cantidad aumentada" : "Cantidad reducida",
                duration: 2000,
                gravity: "bottom",
                style: { background: "linear-gradient(to right, #4ade80, #16a34a)" }
            }).showToast();
        });
}

particlesJS("particles-js", {
    particles: {
        number: { value: 60, density: { enable: true, value_area: 800 } },
        color: { value: "#ffffff" },
        shape: { type: "circle" },
        opacity: { value: 0.1 },
        size: { value: 3, random: true },
        move: { enable: true, speed: 1.2 }
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
