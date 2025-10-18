<?php
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  if ($email && $password) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($password, $u['password_hash'])) {
      $_SESSION['user'] = ['id'=>$u['id'],'nombre'=>$u['nombre'],'email'=>$u['email'],'rol'=>$u['rol']];
      header('Location: index.php'); exit;
    } else $error = 'Email o contraseña inválidos';
  } else $error = 'Completa email y contraseña';
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/styles.css" rel="stylesheet"></head><body>
<?php include 'navbar.php'; ?>
<main class="container py-4" style="max-width:420px;"><div class="card shadow-sm"><div class="card-body">
<h4 class="mb-3">Iniciar sesión</h4><?php if($error) echo '<div class="alert alert-danger">'.e($error).'</div>'; ?>
<form method="post"><input name="email" type="email" class="form-control mb-2" placeholder="Email" required>
<input name="password" type="password" class="form-control mb-3" placeholder="Contraseña" required>
<div class="text-end"><button class="btn btn-primary">Entrar</button></div></form></div></div></main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>