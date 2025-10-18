<?php
require_once 'config.php';
if (!isLoggedIn()) { header('Location: login.php'); exit; }
$user = currentUser();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $raffle_id = (int)($_GET['raffle_id'] ?? 0);
  if (!$raffle_id) header('Location: index.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $raffle_id = (int)($_POST['raffle_id'] ?? 0);
  $method = $_POST['method'] ?? '';
  $nombre = $_POST['nombre'] ?? '';
  $cedula = $_POST['cedula'] ?? '';
  $email = $_POST['email'] ?? '';
  $telefono = $_POST['telefono'] ?? '';
  $monto_usd = (float)($_POST['monto_usd'] ?? 0);
  $monto_bs = (float)($_POST['monto_bs'] ?? 0);
  $referencia = $_POST['referencia'] ?? '';
  $fecha_pago = $_POST['fecha_pago'] ?? '';
  $comprobante = $_POST['comprobante'] ?? '';
  if (!$raffle_id || !$method || !$nombre || !$cedula || !$email || !$referencia || !$fecha_pago) {
    $error = 'Faltan datos obligatorios';
  } else {
    $stmt = $pdo->prepare('INSERT INTO payments (user_id, raffle_id, metodo_pago_slug, monto_usd, monto_bs, referencia, fecha_pago, datos_cliente, comprobante_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $datos_client = json_encode(['nombre'=>$nombre,'cedula'=>$cedula,'email'=>$email,'telefono'=>$telefono]);
    $stmt->execute([$_SESSION['user']['id'],$raffle_id,$method,$monto_usd,$monto_bs,$referencia,$fecha_pago,$datos_client,$comprobante]);
    header('Location: my_payments.php'); exit;
  }
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Reportar pago</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/styles.css" rel="stylesheet"></head><body>
<?php include 'navbar.php'; ?>
<main class="container py-4" style="max-width:720px;">
<a href="raffle_detail.php?id=<?= e($raffle_id) ?>">&larr; Volver</a>
<div class="card shadow-sm mt-2"><div class="card-body">
<h4>Reportar pago</h4><?php if(!empty($error)) echo '<div class="alert alert-danger">'.e($error).'</div>'; ?>
<form method="post">
  <input type="hidden" name="raffle_id" value="<?= e($raffle_id) ?>">
  <div class="mb-2"><label class="form-label">Método</label>
    <select name="method" class="form-select" required>
      <?php foreach($pdo->query('SELECT slug,nombre FROM payment_methods WHERE activo=1') as $m): ?>
        <option value="<?= e($m['slug']) ?>"><?= e($m['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="row g-2"><div class="col-md-6"><input name="nombre" class="form-control" placeholder="Nombre" required></div>
  <div class="col-md-6"><input name="cedula" class="form-control" placeholder="Cédula" required></div></div>
  <div class="row g-2 mt-2"><div class="col-md-6"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
  <div class="col-md-6"><input name="telefono" class="form-control" placeholder="Teléfono"></div></div>
  <div class="row g-2 mt-2"><div class="col-md-4"><input name="monto_usd" class="form-control" placeholder="Monto USD" required></div>
  <div class="col-md-4"><input name="monto_bs" class="form-control" placeholder="Monto Bs" required></div>
  <div class="col-md-4"><input name="referencia" class="form-control" placeholder="Referencia (6 dígitos)" required></div></div>
  <div class="row g-2 mt-2"><div class="col-md-4"><input name="fecha_pago" type="date" class="form-control" required></div>
  <div class="col-md-8"><input name="comprobante" class="form-control" placeholder="URL comprobante (opcional)"></div></div>
  <div class="mt-3 text-end"><button class="btn btn-success">Enviar reporte</button></div>
</form>
</div></div></main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>