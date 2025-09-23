<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('dashboard'); ?>">
      <img src="<?= base_url('logo.png'); ?>" alt="Melisa Logo" height="32" class="me-2"> Melisa
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?= (service('uri')->getSegment(1) == 'dashboard' || service('uri')->getSegment(1) == '') ? 'active' : '' ?>" href="<?= base_url('dashboard'); ?>">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= service('uri')->getSegment(1) == 'riwayat' ? 'active' : '' ?>" href="<?= base_url('riwayat'); ?>">Riwayat</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= service('uri')->getSegment(1) == 'password' ? 'active' : '' ?>" href="<?= base_url('password'); ?>">Ganti Password</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('auth/logout'); ?>">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  .navbar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 8px 15px;
    transition: 0.2s;
  }

  .navbar .nav-link.active {
    font-weight: 600;
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
  }

  .navbar .nav-link:hover {
    color: #0d6efd;
  }
</style>
