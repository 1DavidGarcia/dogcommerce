<?php
require_once __DIR__ . '/../config/database.php';
session_start();

$error = '';
$success = '';
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = trim($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms            = $_POST['terms'] ?? null;

    if (!$name || !$email || !$password || !$confirm_password) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo electrónico inválido.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (!$terms) {
        $error = "Debes aceptar los términos y condiciones.";
    } else {
        $db = new Database();
        $conn = $db->connect();

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "El correo ya está registrado.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashedPassword])) {
                $success = "Usuario registrado correctamente. <a href='login.php' class='underline'>Inicia sesión</a>.";
                $name = $email = '';
            } else {
                $error = "Error al registrar usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
  <style>
    html, body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
    }

    #particles-js {
      position: fixed;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: radial-gradient(circle at 30% 30%, #1e1e3f, #0f0f1f);
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(16px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    }

    .glow:hover {
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.6), 0 0 60px #7f5af0;
    }

    .fade-in {
      animation: fadeIn 1.2s ease-out forwards;
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

<div id="particles-js"></div>

<div class="flex items-center justify-center h-screen px-4">
  <div class="glass-card max-w-xl w-full p-10 rounded-3xl fade-in text-white">

    <h1 class="text-4xl font-extrabold text-center mb-8 text-white drop-shadow-xl">⚡ Registro</h1>

    <?php if ($error): ?>
        <p class="text-red-400 text-center mb-4 font-semibold"><?= $error ?></p>
    <?php elseif ($success): ?>
        <p class="text-green-400 text-center mb-4 font-semibold"><?= $success ?></p>
    <?php endif; ?>

    <form method="post" class="space-y-6">

      <div class="relative">
        <i class="ph-user-circle text-white absolute left-3 top-3.5 text-2xl"></i>
        <input type="text" name="name" placeholder="Tu nombre" value="<?= htmlspecialchars($name) ?>" required
          class="w-full pl-12 pr-4 py-3 bg-transparent border border-white/30 rounded-xl placeholder-white/50 text-white focus:ring-2 focus:ring-purple-500 transition duration-300">
      </div>

      <div class="relative">
        <i class="ph-envelope-simple text-white absolute left-3 top-3.5 text-2xl"></i>
        <input type="email" name="email" placeholder="Correo electrónico" value="<?= htmlspecialchars($email) ?>" required
          class="w-full pl-12 pr-4 py-3 bg-transparent border border-white/30 rounded-xl placeholder-white/50 text-white focus:ring-2 focus:ring-cyan-400 transition duration-300">
      </div>

      <div class="relative">
        <i class="ph-lock-key text-white absolute left-3 top-3.5 text-2xl"></i>
        <input type="password" name="password" placeholder="Contraseña" required
          class="w-full pl-12 pr-4 py-3 bg-transparent border border-white/30 rounded-xl placeholder-white/50 text-white focus:ring-2 focus:ring-pink-500 transition duration-300">
      </div>

      <div class="relative">
        <i class="ph-lock-key-open text-white absolute left-3 top-3.5 text-2xl"></i>
        <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required
          class="w-full pl-12 pr-4 py-3 bg-transparent border border-white/30 rounded-xl placeholder-white/50 text-white focus:ring-2 focus:ring-pink-400 transition duration-300">
      </div>

      <label class="flex items-center text-sm text-white/70">
        <input type="checkbox" name="terms" required class="mr-2 accent-green-500">
        Acepto los <a href="#" class="underline ml-1 hover:text-blue-300">términos y condiciones</a>
      </label>

      <button type="submit"
        onclick="playClickSound()"
        class="w-full py-3 text-lg font-bold text-white rounded-xl bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 hover:from-yellow-400 hover:to-red-500 glow transition-all duration-500">
        REGISTRARSE
      </button>
    </form>

    <p class="text-center text-white/70 mt-6 text-sm">¿Ya tienes cuenta? <a href="login.php" class="text-white font-semibold underline hover:text-blue-300">Inicia sesión</a></p>
  </div>
</div>

<audio id="clickSound" src="https://cdn.pixabay.com/download/audio/2021/08/04/audio_394f527c97.mp3?filename=ui-click-1-128936.mp3" preload="auto"></audio>
<script>
  function playClickSound() {
    document.getElementById('clickSound').play();
  }
</script>

<script>
  particlesJS("particles-js", {
    particles: {
      number: { value: 80, density: { enable: true, value_area: 800 } },
      color: { value: "#ffffff" },
      shape: { type: "circle" },
      opacity: { value: 0.2, random: true },
      size: { value: 3, random: true },
      move: { enable: true, speed: 1.5 }
    },
    interactivity: {
      detect_on: "canvas",
      events: { onhover: { enable: true, mode: "repulse" }, onclick: { enable: false } },
      modes: { repulse: { distance: 100, duration: 0.4 } }
    },
    retina_detect: true
  });
</script>

</body>
</html>
