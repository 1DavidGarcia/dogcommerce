<?php
require_once __DIR__ . '/../../src/middleware/is_admin.php';
require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'] ?? null;
if (!$id) die("ID inválido");

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) die("Producto no encontrado");

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $description = $_POST['description'] ?? '';
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    $imagePath = $product['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $filename;
        $webPath = 'uploads/' . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $webPath;
        } else {
            $error = "Error al subir la nueva imagen.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE products SET name=?, slug=?, price=?, stock=?, image=?, description=? WHERE id=?");
        if ($stmt->execute([$name, $slug, $price, $stock, $imagePath, $description, $id])) {
            $success = "✅ Producto actualizado.";
            $product['name'] = $name;
            $product['price'] = $price;
            $product['stock'] = $stock;
            $product['image'] = $imagePath;
            $product['description'] = $description;
        } else {
            $error = "❌ Error al actualizar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
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

<div id="particles-js"></div>

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="glass max-w-xl w-full p-10 rounded-3xl fade-in">

        <h2 class="text-3xl font-extrabold mb-6 text-center drop-shadow">✏️ Editar Producto</h2>

        <?php if ($error): ?>
            <p class="text-red-400 text-center mb-4 font-semibold"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="text-green-400 text-center mb-4 font-semibold"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="space-y-5">

            <!-- Nombre -->
            <div class="relative">
                <i class="ph-package text-white absolute left-3 top-3.5 text-xl"></i>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required
                       class="w-full pl-12 pr-4 py-3 bg-transparent border border-white/30 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-purple-500 transition duration-300">
            </div>

            <!-- Precio -->
            <div class="relative">
                <i class="ph-currency-dollar-simple text-white absolute left-3 top-3.5 text-xl"></i>
                <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required
                       class="w-full pl-12 pr-4 py-3 bg-transparent border border-white/30 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-green-400 transition duration-300">
            </div>

            <!-- Stock -->
            <div class="relative">
                <i class="ph-archive text-white absolute left-3 top-3.5 text-xl"></i>
                <input type="number" name="stock" value="<?= $product['stock'] ?>"
                       class="w-full pl-12 pr-4 py-3 bg-transparent border border-white/30 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-yellow-400 transition duration-300">
            </div>

            <!-- Descripción -->
            <div class="relative">
                <label class="block mb-1 font-semibold text-white/80">Descripción</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-3 bg-transparent border border-white/30 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-indigo-400 transition duration-300 resize-none"
                          placeholder="Escribe una breve descripción..."><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <!-- Imagen -->
            <div class="relative">
                <label class="block mb-1 font-semibold text-white/80">Imagen actual</label>
                <img src="../../public/<?= htmlspecialchars($product['image']) ?>" alt="Imagen actual"
                     class="rounded-xl shadow w-full h-48 object-cover mb-2 border border-white/20">
                <input type="file" name="image"
                       class="w-full px-4 py-3 bg-transparent border border-white/30 rounded-xl text-white placeholder-white/60 file:bg-white file:text-black file:rounded file:px-3 file:py-1"
                       accept="image/*">
            </div>

            <!-- Botones -->
            <div class="flex justify-between gap-4 mt-6">
                <a href="products.php"
                   class="flex-1 text-center bg-white/20 hover:bg-white/40 text-white font-semibold py-3 rounded-xl transition-all">
                    ⬅️ Volver
                </a>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-pink-500 hover:to-yellow-400 text-white font-bold py-3 rounded-xl transition-all duration-500 transform hover:scale-105 hover:shadow-lg">
                    💾 Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    particlesJS("particles-js", {
        particles: {
            number: { value: 50, density: { enable: true, value_area: 800 } },
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
