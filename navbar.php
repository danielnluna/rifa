<?php require_once 'config.php'; $user = currentUser(); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>index.php">RifaPlus</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php">Inicio</a></li>
      </ul>
      <div class="d-flex">
        <?php if($user): ?>
          <span class="navbar-text text-white me-3">Hola, <?= e($user['nombre']) ?></span>
          <?php if($user['rol'] === 'admin'): ?>
            <a class="btn btn-outline-light btn-sm me-2" href="<?= BASE_URL ?>admin/index.php">Panel Admin</a>
          <?php else: ?>
            <a class="btn btn-outline-light btn-sm me-2" href="<?= BASE_URL ?>my_payments.php">Mis pagos</a>
          <?php endif; ?>
          <a class="btn btn-light btn-sm" href="<?= BASE_URL ?>logout.php">Salir</a>
        <?php else: ?>
          <a class="btn btn-outline-light btn-sm me-2" href="<?= BASE_URL ?>login.php">Entrar</a>
          <a class="btn btn-light btn-sm" href="<?= BASE_URL ?>register.php">Registrarse</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>