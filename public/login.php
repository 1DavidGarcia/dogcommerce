<?php
require_once __DIR__ . '/../config/database.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];

        // Redirección según el rol
        if ($user['role'] === 'admin') {
            header('Location: ../public/admin/dashboard.php');
        } else {
            header('Location: index.php');
        }

        exit();
    } else {
        $error = "Credenciales incorrectas.";
    }
}

?>

<?php if (!isset($error)) $error = null; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
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
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.7);
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
    </style>
</head>
<body>

<!-- Fondo animado -->
<div id="particles-js"></div>

<!-- Contenedor del formulario -->
<div class="min-h-screen flex items-center justify-center px-4 fade-in">
    <div class="glass max-w-md w-full p-10 rounded-3xl text-white">
        <h1 class="text-3xl font-extrabold text-center mb-8 drop-shadow">🔐 Iniciar Sesión</h1>

        <?php if ($error): ?>
            <p class="text-red-400 text-center mb-4 font-semibold"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" class="space-y-6">
            <div class="relative">
                <i class="ph-envelope-simple text-white absolute left-3 top-3.5 text-xl"></i>
                <input type="email" name="email" placeholder="Correo electrónico" required
                    class="w-full pl-12 pr-4 py-3 bg-transparent border border-white/30 rounded-xl placeholder-white/50 text-white focus:ring-2 focus:ring-blue-400 focus:outline-none transition duration-300">
            </div>

            <div class="relative">
                <i class="ph-lock-key text-white absolute left-3 top-3.5 text-xl"></i>
                <input type="password" name="password" placeholder="Contraseña" required
                    class="w-full pl-12 pr-4 py-3 bg-transparent border border-white/30 rounded-xl placeholder-white/50 text-white focus:ring-2 focus:ring-purple-500 focus:outline-none transition duration-300">
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 hover:from-pink-500 hover:to-yellow-500 text-white font-bold py-3 rounded-xl transition-all duration-500 transform hover:scale-105 hover:shadow-lg">
                Iniciar Sesión
            </button>
        </form>

        <p class="text-center text-white/60 mt-6 text-sm">¿No tienes cuenta? <a href="register.php" class="text-blue-300 font-semibold underline hover:text-yellow-300">Regístrate aquí</a></p>
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
            events: { onhover: { enable: true, mode: "repulse" } },
            modes: { repulse: { distance: 100, duration: 0.4 } }
        },
        retina_detect: true
    });
</script>

</body>
</html>
