<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'Dashboard Reviewer' ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Modular CSS -->
  <link rel="stylesheet" href="<?= base_url('css/admin/sidebar.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/admin/navbar.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/admin/main.css') ?>">
</head>
<body>

  <!-- Sidebar -->
  <?= $this->include('layouts/reviewer/sidebar') ?>

  <!-- Navbar -->
  <?= $this->include('layouts/reviewer/navbar') ?>

  <!-- Main Content -->
  <div class="main">
    <?= $this->renderSection('content') ?>
  </div>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php if(session()->getFlashdata('success')): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: '<?= session()->getFlashdata('success') ?>',
      showConfirmButton: true,
      confirmButtonText: 'OK',
      allowOutsideClick: false
    })
  </script>
  <?php endif; ?>

  <?php if(session()->getFlashdata('errors')): ?>
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Terjadi Kesalahan',
      html: `
        <?php foreach((array) session()->getFlashdata('errors') as $error): ?>
          <p><?= $error ?></p>
        <?php endforeach; ?>
      `,
      confirmButtonText: 'Coba Lagi'
    })
  </script>
  <?php endif; ?>

</body>
</html>
