<?php
require_once __DIR__ . '/../../src/middleware/is_admin.php';
require_once __DIR__ . '/../../config/database.php';

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['user_id'];
    $newRole = $_POST['role'];
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$newRole, $id]);
    header('Location: users.php');
    exit;
}

$users = $conn->query("SELECT id, name, email, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-6">
  <h1 class="text-3xl font-bold mb-6">👥 Gestión de Usuarios</h1>
  <table class="w-full bg-white/10 rounded-xl overflow-hidden">
    <thead>
      <tr class="bg-white/20 text-left">
        <th class="p-3">ID</th>
        <th class="p-3">Nombre</th>
        <th class="p-3">Email</th>
        <th class="p-3">Rol</th>
        <th class="p-3">Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
      <tr class="border-b border-white/10">
        <td class="p-3"><?= $user['id'] ?></td>
        <td class="p-3"><?= htmlspecialchars($user['name']) ?></td>
        <td class="p-3"><?= htmlspecialchars($user['email']) ?></td>
        <td class="p-3"><?= $user['role'] ?></td>
        <td class="p-3">
          <?php if ($_SESSION['user']['id'] !== $user['id']): ?>
          <form method="POST" class="flex gap-2">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <select name="role" class="text-black rounded px-2">
              <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Usuario</option>
              <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
            <button type="submit" class="bg-blue-500 px-3 py-1 rounded hover:bg-blue-600 text-white">Guardar</button>
          </form>
          <?php else: ?>
            <span class="text-sm text-gray-400">No editable</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a href="dashboard.php" class="inline-block mt-6 text-blue-400 underline">⬅ Volver al panel</a>
</body>
</html>
