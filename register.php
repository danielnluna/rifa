<?php
require_once 'config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $cedula = trim($_POST['cedula'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $telefono = trim($_POST['telefono'] ?? '');
  $password = $_POST['password'] ?? '';
  if (!$nombre || !$email || strlen($password) < 6) $errors[] = 'Completa los campos obligatorios y contraseña mínimo 6';
  if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = 'El email ya está registrado';
    else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare('INSERT INTO users (nombre, cedula, email, telefono, password_hash) VALUES (?, ?, ?, ?, ?)');
      $stmt->execute([$nombre,$cedula,$email,$telefono,$hash]);
      $_SESSION['user'] = ['id'=>$pdo->lastInsertId(),'nombre'=>$nombre,'email'=>$email,'rol'=>'cliente'];
      header('Location: index.php'); exit;
    }
  }
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Registro</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/styles.css" rel="stylesheet"></head><body>
<?php include 'navbar.php'; ?>
<main class="container py-4" style="max-width:720px;"><div class="card shadow-sm"><div class="card-body">
<h4>Crear cuenta</h4><?php if($errors) foreach($errors as $err) echo '<div class="alert alert-danger">'.e($err).'</div>'; ?>
<form method="post"><div class="row g-2"><div class="col-md-6"><input name="nombre" class="form-control" placeholder="Nombre" required></div>
<div class="col-md-6"><input name="cedula" class="form-control" placeholder="Cédula"></div></div>
<div class="mt-2"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
<div class="mt-2"><input name="telefono" class="form-control" placeholder="Teléfono"></div>
<div class="mt-2"><input name="password" type="password" class="form-control" placeholder="Contraseña" required></div>
<div class="mt-3 text-end"><button class="btn btn-success">Registrarse</button></div></form>
</div></div></main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>