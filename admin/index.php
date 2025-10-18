<?php
require_once __DIR__ . '/../config.php';
if (!isAdmin()) { header('Location: ../login.php'); exit; }
$pending = $pdo->query("SELECT p.*, u.nombre AS usuario_nombre, r.titulo AS raffle_title FROM payments p JOIN users u ON u.id=p.user_id JOIN raffles r ON r.id=p.raffle_id WHERE p.estado='pendiente'")->fetchAll();
$tickets = $pdo->query("SELECT t.*, r.titulo AS raffle_title, u.nombre AS usuario_nombre, a.nombre AS admin_nombre FROM raffle_tickets t JOIN raffles r ON r.id=t.raffle_id JOIN users u ON u.id=t.user_id JOIN users a ON a.id=t.asignado_por ORDER BY t.assigned_at DESC LIMIT 100")->fetchAll();
$csrf = csrf_token();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="../assets/styles.css" rel="stylesheet"></head><body>
<?php include '../navbar.php'; ?>
<main class="container py-4">
<h3>Panel Administrador</h3>
<div class="mb-3"><a class="btn btn-sm btn-outline-primary" href="update_rate.php">Actualizar tasa USD→Bs</a> <a class="btn btn-sm btn-outline-secondary" href="payment_methods.php">Métodos de pago</a></div>
<h5>Pagos pendientes</h5>
<div class="table-responsive"><table class="table table-sm">
<thead><tr><th>ID</th><th>Usuario</th><th>Rifa</th><th>Monto</th><th>Ref</th><th>Fecha</th><th>Acción</th></tr></thead>
<tbody><?php foreach($pending as $p): ?>
  <tr>
    <td><?= e($p['id']) ?></td>
    <td><?= e($p['usuario_nombre']) ?></td>
    <td><?= e($p['raffle_title']) ?></td>
    <td>$<?= number_format($p['monto_usd'],2) ?> / <?= number_format($p['monto_bs'],2) ?> Bs</td>
    <td><?= e($p['referencia']) ?></td>
    <td><?= e($p['fecha_pago']) ?></td>
    <td>
      <form method="post" action="verify_payment.php" style="display:inline;">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="payment_id" value="<?= e($p['id']) ?>">
        <button class="btn btn-sm btn-success">Verificar</button>
      </form>
      <form method="post" action="reject_payment.php" style="display:inline;margin-left:6px;">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="payment_id" value="<?= e($p['id']) ?>">
        <button class="btn btn-sm btn-danger">Rechazar</button>
      </form>
    </td>
  </tr>
<?php endforeach; ?></tbody></table></div>

<h5 class="mt-4">Tickets asignados</h5>
<div class="table-responsive"><table class="table table-sm">
<thead><tr><th>ID</th><th>Rifa</th><th>Usuario</th><th>Ticket</th><th>Asignado por</th><th>Fecha</th></tr></thead>
<tbody><?php foreach($tickets as $t): ?>
  <tr>
    <td><?= e($t['id']) ?></td>
    <td><?= e($t['raffle_title']) ?></td>
    <td><?= e($t['usuario_nombre']) ?></td>
    <td><?= e($t['numero_ticket']) ?></td>
    <td><?= e($t['admin_nombre']) ?></td>
    <td><?= e($t['assigned_at']) ?></td>
  </tr>
<?php endforeach; ?></tbody></table></div>
</main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>