<?php
require_once __DIR__ . '/../config.php';
if (!isAdmin()) { header('Location: ../login.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $slug = trim($_POST['slug'] ?? ''); $nombre = trim($_POST['nombre'] ?? ''); $datos = $_POST['datos'] ?? '';
  if ($slug && $nombre) {
    $stmt = $pdo->prepare('INSERT INTO payment_methods (slug,nombre,datos) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), datos=VALUES(datos)');
    $stmt->execute([$slug,$nombre,json_encode(json_decode($datos, true))]);
    $msg = 'Guardado.';
  }
}
$methods = $pdo->query('SELECT * FROM payment_methods')->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Métodos de pago</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="../assets/styles.css" rel="stylesheet"></head><body>
<?php include '../navbar.php'; ?>
<main class="container py-4"><h4>Métodos de pago</h4><?php if($msg) echo '<div class="alert alert-info">'.e($msg).'</div>'; ?>
<div class="row"><div class="col-md-6"><h5>Agregar / Editar</h5>
<form method="post"><input name="slug" class="form-control mb-2" placeholder="slug (ej: zelle)" required>
<input name="nombre" class="form-control mb-2" placeholder="Nombre (ej: Zelle)" required>
<textarea name="datos" class="form-control mb-2" placeholder='JSON datos (ej: {"identifier":"+123","owner":"Nombre"})'></textarea>
<div class="text-end"><button class="btn btn-primary">Guardar</button></div></form></div>
<div class="col-md-6"><h5>Existentes</h5><ul class="list-group"><?php foreach($methods as $m): ?>
  <li class="list-group-item"><strong><?= e($m['slug']) ?></strong> — <?= e($m['nombre']) ?> <pre class="mb-0 small"><?= e($m['datos']) ?></pre></li>
<?php endforeach; ?></ul></div></div></main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>