<?php
require_once 'config.php';
try {
    $stmt = $pdo->query("SELECT * FROM raffles WHERE estado='activa' ORDER BY created_at DESC");
    $raffles = $stmt->fetchAll();
    $rateRow = $pdo->query("SELECT tasa FROM currency_rates ORDER BY updated_at DESC LIMIT 1")->fetch();
    $tasa = $rateRow ? (float)$rateRow['tasa'] : 1;
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Rifas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/styles.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div><h3 class="mb-0">Rifas disponibles</h3><div class="small-muted">Participa y gana</div></div>
  </div>
  <div class="row g-3">
    <?php if(count($raffles)): foreach($raffles as $r): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-bleed shadow-sm h-100">
          <?php if(!empty($r['imagen'])): ?>
            <img src="uploads/<?= htmlspecialchars($r['imagen']) ?>" class="card-img-top" alt="Imagen Rifa" style="max-height:190px; object-fit:cover; background:#fcfcfc;">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h5 class="mb-1"><?= e($r['titulo']) ?></h5>
            <div class="small-muted mb-2">Sorteo: <?= e($r['fecha_sorteo']) ?></div>
            <p class="mt-1 mb-3 flex-grow-1"><?= e(substr($r['descripcion'],0,120)) ?>...</p>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-bold text-success">$<?= number_format($r['precio_usd'],2) ?> USD</div>
                <div class="small-muted">≈ Bs <?= number_format($r['precio_usd'] * $tasa,2) ?></div>
              </div>
              <a href="<?= BASE_URL ?>raffle_detail.php?id=<?= e($r['id']) ?>" class="btn btn-primary">Ver / Pagar</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; else: ?>
      <div class="col-12"><div class="alert alert-info">No hay rifas disponibles.</div></div>
    <?php endif; ?>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
