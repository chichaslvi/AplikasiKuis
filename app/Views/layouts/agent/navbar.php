<!-- app/Views/layouts/agent/navbar.php -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">
    <img src="<?= base_url('assets/img/Logo.png'); ?>" alt="Melisa Logo" height="32" class="me-2"> 
</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= url_is('agent/dashboard') ? 'active' : '' ?>" 
             href="<?= base_url('agent/dashboard') ?>">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= url_is('agent/riwayat') ? 'active' : '' ?>" 
             href="<?= base_url('agent/riwayat') ?>">Riwayat</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= url_is('agent/ganti-password') ? 'active' : '' ?>" 
             href="<?= base_url('agent/ganti-password') ?>">
            <i class="fas fa-key"></i> Ganti Password
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="<?= base_url('/') ?>">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
