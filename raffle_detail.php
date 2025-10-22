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
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($r['titulo']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/styles.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<main class="container py-4">
  <a href="index.php" class="btn btn-link">&larr; Volver</a>
  <div class="card shadow-sm mt-3" style="max-width:550px;margin:auto;">
    <?php if (!empty($r['imagen'])): ?>
      <img src="uploads/<?= htmlspecialchars($r['imagen']) ?>"
           class="card-img-top"
           alt="Imagen de rifa"
           style="max-height:260px;object-fit:cover;">
    <?php endif; ?>
    <div class="card-body">
      <h3><?= e($r['titulo']) ?></h3>
      <p class="small-muted">Sorteo: <?= e($r['fecha_sorteo']) ?></p>
      <p><?= nl2br(e($r['descripcion'])) ?></p>
      <p class="fw-bold">
        Precio: $<?= number_format($r['precio_usd'],2) ?> USD
        — ≈ Bs <?= number_format($r['precio_usd'] * $tasa,2) ?>
      </p>
      <hr>
      <h5>Reportar pago</h5>
      <div class="mb-3">
        <?php foreach($methods as $idx => $m): 
          $slug = $m['slug'];
          $nombre = $m['nombre'];
        ?>
        <button type="button"
                class="btn btn-outline-primary me-2 mb-2 method-btn"
                data-method="<?= htmlspecialchars($slug) ?>"
                <?= $idx==0 ? 'id="default-method"' : '' ?>>
          <?= e($nombre) ?>
        </button>
        <?php endforeach; ?>
      </div>
      <!-- Datos de métodos (solo uno visible a la vez) -->
      <?php foreach($methods as $m): 
        $slug = $m['slug'];
        $nombre = $m['nombre'];
        $datos = json_decode($m['datos'], true);
      ?>
      <div class="card mb-3 p-2 method-data"
           id="method-<?= htmlspecialchars($slug) ?>"
           style="display:none;">
           <strong><?= e($nombre) ?></strong>
           <ul class="mb-0">
             <?php foreach($datos as $k=>$v): ?>
               <li><b><?= e($k) ?>:</b> <?= e($v) ?></li>
             <?php endforeach; ?>
           </ul>
      </div>
      <?php endforeach; ?>

      <!-- Formulario de reporte de pago -->
      <form class="mt-3" method="post" action="report_payment.php?raffle_id=<?= e($r['id']) ?>">
        <input type="hidden" name="payment_method" id="selected-method" value="">
        <div class="mb-2">
          <label for="referencia" class="form-label">Referencia/ID de pago</label>
          <input type="text" name="referencia" id="referencia" class="form-control" required>
        </div>
        <div class="mb-2">
          <label for="monto" class="form-label">Monto enviado</label>
          <input type="number" step="0.01" name="monto" id="monto" class="form-control" required>
        </div>
        <!-- Puedes agregar más campos según tus necesidades -->
        <button type="submit" class="btn btn-success">Enviar reporte</button>
      </form>
      <p class="small-muted mt-2">
         El admin validará y asignará el número de ticket tras confirmar el pago.
      </p>
    </div>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Mostrar el primer método por defecto
  var first = document.getElementById("default-method");
  if(first){
    first.click();
  }

  var methodBtns = document.querySelectorAll('.method-btn');
  var methodDataDivs = document.querySelectorAll('.method-data');
  var selectedMethodInput = document.getElementById('selected-method');

  methodBtns.forEach(function(btn){
    btn.addEventListener('click', function(){
      // Oculta todos los bloques de datos
      methodDataDivs.forEach(function(div){ div.style.display = 'none'; });
      // Desactivar todos los botones
      methodBtns.forEach(function(b){ b.classList.remove('active'); });
      // Activa solo el actual
      btn.classList.add('active');
      // Muestra el que hace match
      var slug = btn.getAttribute('data-method');
      var el = document.getElementById('method-' + slug);
      if(el) el.style.display = 'block';
      if(selectedMethodInput) selectedMethodInput.value = slug;
    });
  });

  // Si no ponen método al enviar, elige el primero
  document.querySelector('form').addEventListener('submit', function(e){
    if(!selectedMethodInput.value){
      var defBtn = document.getElementById("default-method");
      if(defBtn){
        selectedMethodInput.value = defBtn.getAttribute('data-method');
      }
    }
  });
});
</script>
</body>
</html>
