<?php
require_once __DIR__ . '/../config.php';
if (!isAdmin()) { header('Location: ../login.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tasa = (float)($_POST['tasa'] ?? 0);
  if ($tasa > 0) {
    $stmt = $pdo->prepare('INSERT INTO currency_rates (tasa) VALUES (?)');
    $stmt->execute([$tasa]);
    $msg = 'Tasa actualizada.';
  } else $msg = 'Valor inválido.';
}
$current = $pdo->query('SELECT tasa FROM currency_rates ORDER BY updated_at DESC LIMIT 1')->fetchColumn();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Actualizar tasa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="../assets/styles.css" rel="stylesheet"></head><body>
<?php include '../navbar.php'; ?>
<main class="container py-4"><h4>Actualizar tasa USD → Bs</h4>
<?php if($msg) echo '<div class="alert alert-info">'.e($msg).'</div>'; ?>
<form method="post" class="mt-3" style="max-width:360px;">
  <div class="mb-2"><label class="form-label">Tasa (1 USD = ? Bs)</label>
  <input type="number" step="0.01" name="tasa" class="form-control" value="<?= e($current) ?>" required></div>
  <div class="text-end"><button class="btn btn-primary">Actualizar</button></div>
</form></main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>