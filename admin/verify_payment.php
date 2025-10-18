<?php
require_once __DIR__ . '/../config.php';
if (!isAdmin()) { header('Location: ../login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
if (!verify_csrf($_POST['csrf'] ?? '')) { header('Location: index.php'); exit; }
$payment_id = (int)($_POST['payment_id'] ?? 0);
try {
  $pdo->beginTransaction();
  $stmt = $pdo->prepare('SELECT * FROM payments WHERE id = ? FOR UPDATE');
  $stmt->execute([$payment_id]);
  $p = $stmt->fetch();
  if (!$p) throw new Exception('Pago no encontrado');
  if ($p['estado'] !== 'pendiente') throw new Exception('Pago ya procesado');
  $stmt = $pdo->prepare("UPDATE payments SET estado='verificado' WHERE id = ?");
  $stmt->execute([$payment_id]);
  $raffle_id = (int)$p['raffle_id'];
  $stmt = $pdo->prepare('SELECT numero_ticket FROM raffle_tickets WHERE raffle_id = ? ORDER BY id DESC LIMIT 1');
  $stmt->execute([$raffle_id]);
  $last = $stmt->fetchColumn();
  $next = 1; if ($last !== false) { $num = intval($last); $next = $num + 1; }
  $ticket_str = str_pad($next,3,'0',STR_PAD_LEFT);
  $stmt = $pdo->prepare('INSERT INTO raffle_tickets (raffle_id,user_id,payment_id,numero_ticket,asignado_por) VALUES (?,?,?,?,?)');
  $stmt->execute([$raffle_id, $p['user_id'], $payment_id, $ticket_str, $_SESSION['user']['id']]);
  $pdo->commit();
} catch (Exception $e) {
  $pdo->rollBack();
  error_log($e->getMessage());
}
header('Location: index.php'); exit;
