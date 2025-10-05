<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Dashboard Agent') ?></title>

  <!-- CSRF meta (buat fetch/POST di JS) -->
  <meta name="X-CSRF-HEADER" content="<?= csrf_header() ?>">
  <meta name="X-CSRF-TOKEN"  content="<?= csrf_hash() ?>">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('css/agent/dashboard.css'); ?>">

  <!-- (opsional) section tambahan untuk page-specific CSS -->
  <?= $this->renderSection('styles') ?>
</head>
<body>

  <!-- Navbar -->
  <?= $this->include('layouts/agent/navbar') ?>

  <!-- Main Content -->
  <?= $this->renderSection('content') ?>

  <!-- Footer -->
  <?= $this->include('layouts/agent/footer') ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Popup Notifikasi -->
  <?php if(session()->getFlashdata('success')): ?>
  <script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= session()->getFlashdata('success') ?>',
    showConfirmButton: true,
    confirmButtonText: 'OK',
    confirmButtonColor: '#3085d6',
  });
  </script>
  <?php endif; ?>

  <?php if(session()->getFlashdata('error')): ?>
  <script>
  Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: '<?= session()->getFlashdata('error') ?>',
    confirmButtonText: 'Coba Lagi',
    confirmButtonColor: '#d33',
  });
  </script>
  <?php endif; ?>

  <!-- Section tambahan untuk page-specific JS -->
  <?= $this->renderSection('scripts') ?>
</body>
</html>
