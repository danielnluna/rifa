<?php
require_once __DIR__ . '/../config.php';
if (!isAdmin()) { header('Location: ../login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
if (!verify_csrf($_POST['csrf'] ?? '')) { header('Location: index.php'); exit; }
$payment_id = (int)($_POST['payment_id'] ?? 0);
$stmt = $pdo->prepare('UPDATE payments SET estado = ? WHERE id = ?');
$stmt->execute(['rechazado', $payment_id]);
header('Location: index.php'); exit;
