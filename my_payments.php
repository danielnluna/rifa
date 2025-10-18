<?php
require_once 'config.php';
if (!isLoggedIn()) { header('Location: login.php'); exit; }
$user = currentUser();
$stmt = $pdo->prepare('SELECT p.*, r.titulo AS raffle_title FROM payments p JOIN raffles r ON r.id = p.raffle_id WHERE p.user_id = ? ORDER BY p.created_at DESC');
$stmt->execute([$user['id']]);
$payments = $stmt->fetchAll();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Mis pagos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/styles.css" rel="stylesheet"></head><body>
<?php include 'navbar.php'; ?>
<main class="container py-4">
<h3>Mis reportes de pago</h3>
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>Rifa</th><th>Método</th><th>Monto USD</th><th>Monto Bs</th><th>Ref</th><th>Fecha</th><th>Estado</th><th>Ticket</th></tr></thead>
<tbody>
<?php foreach($payments as $p):
  $stmt2 = $pdo->prepare('SELECT numero_ticket FROM raffle_tickets WHERE payment_id = ?');
  $stmt2->execute([$p['id']]);
  $ticket = $stmt2->fetchColumn();
?>
  <tr>
    <td><?= e($p['raffle_title']) ?></td>
    <td><?= e($p['metodo_pago_slug']) ?></td>
    <td>$<?= number_format($p['monto_usd'],2) ?></td>
    <td><?= number_format($p['monto_bs'],2) ?></td>
    <td><?= e($p['referencia']) ?></td>
    <td><?= e($p['fecha_pago']) ?></td>
    <td><?= e($p['estado']) ?></td>
    <td><?= $ticket ? e($ticket) : '—' ?></td>
  </tr>
<?php endforeach; ?>
</tbody></table></div></main><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>