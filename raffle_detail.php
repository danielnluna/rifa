<?php
require_once 'config.php';
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }
$stmt = $pdo->prepare('SELECT * FROM raffles WHERE id = ?');
$stmt->execute([$id]);
$r = $stmt->fetch();
if (!$r) { http_response_code(404); echo 'Rifa no encontrada'; exit; }
$rateRow = $pdo->query("SELECT tasa FROM currency_rates ORDER BY updated_at DESC LIMIT 1")->fetch();
$tasa = $rateRow ? (float)$rateRow['tasa'] : 1;
$methods = $pdo->query('SELECT slug,nombre,datos FROM payment_methods WHERE activo=1')->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($r['titulo']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/styles.css" rel="stylesheet"></head><body>
<?php include 'navbar.php'; ?>
<main class="container py-4"><a href="index.php">&larr; Volver</a>
<div class="card shadow-sm mt-3"><div class="card-body">
  <h3><?= e($r['titulo']) ?></h3>
  <p class="small-muted">Sorteo: <?= e($r['fecha_sorteo']) ?></p>
  <p><?= nl2br(e($r['descripcion'])) ?></p>
  <p class="fw-bold">Precio: $<?= number_format($r['precio_usd'],2) ?> USD — ≈ Bs <?= number_format($r['precio_usd'] * $tasa,2) ?></p>
  <hr>
  <h5>Reportar pago</h5>
  <?php foreach($methods as $m): $datos = json_decode($m['datos'], true); ?>
    <div class="border p-2 mb-2">
      <strong><?= e($m['nombre']) ?></strong>
      <div class="small-muted"><?php foreach($datos as $k=>$v) echo e($k).': '.e($v).' '; ?></div>
    </div>
  <?php endforeach; ?>
  <p class="small-muted">Completa el formulario para reportar el pago. El admin validará y asignará el número.</p>
  <a href="report_payment.php?raffle_id=<?= e($r['id']) ?>" class="btn btn-primary">Reportar pago</a>
</div></div></main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>