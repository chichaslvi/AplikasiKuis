<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'Dashboard Admin' ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Modular CSS -->
  <link rel="stylesheet" href="<?= base_url('css/admin/sidebar.css') ?>">
</head>
<body>

  <!-- Sidebar -->
  <?= $this->include('layouts/admin/sidebar') ?>

  <!-- Navbar -->
  <?= $this->include('layouts/admin/navbar') ?>

  <!-- Main Content -->
  <div class="main">
    <?= $this->renderSection('content') ?>
  </div>

</body>
</html>
