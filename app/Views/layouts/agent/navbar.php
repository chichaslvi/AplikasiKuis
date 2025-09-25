<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="<?= base_url('agent/dashboard') ?>">
      AgentKuis
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?= url_is('agent/dashboard') ? 'active' : '' ?>" href="<?= base_url('agent/dashboard') ?>">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('agent/riwayat') ?>">Riwayat</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('agent/changePassword') ?>">Ganti Password</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="<?= base_url('logout') ?>">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
