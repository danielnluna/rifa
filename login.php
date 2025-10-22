<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $stmt = $pdo->prepare("SELECT id, email, password_hash, estado FROM users WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && $user['estado']==1 && password_verify($password, $user['password_hash'])) {
            // Login correcto
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: index.php'); exit;
        } else {
            $error = "Correo o contraseña incorrectos, o cuenta inactiva.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Iniciar sesión — Rifas</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
          <h4 class="mb-0">Iniciar sesión</h4>
        </div>
        <div class="card-body">
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          <form method="post" action="login.php" autocomplete="on">
            <div class="mb-3">
              <label for="email" class="form-label">Correo</label>
              <input type="email" name="email" id="email" class="form-control" required autofocus autocomplete="username" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-success w-100">Ingresar</button>
          </form>
          <div class="d-grid gap-2 mt-4">
            <a href="register.php" class="btn btn-outline-primary">Crear cuenta</a>
            <a href="recover.php" class="btn btn-link text-decoration-underline">¿Olvidaste tu contraseña?</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
