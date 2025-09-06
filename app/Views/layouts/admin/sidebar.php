<div class="sidebar">
  <div class="logo">
    <img src="/logo.png" alt="Logo">
    <h2>Melisa</h2>
  </div>
  <ul class="menu">
    <li class="active">
      <a href="<?= base_url('admin/dashboard') ?>">
        <i class="fa-solid fa-house"></i> Beranda
      </a>
    </li>
    <li>
      <a href="<?= base_url('admin/users') ?>">
        <i class="fa-solid fa-users"></i> Manajemen User
      </a>
    </li>
    <li>
      <a href="<?= base_url('admin/roles') ?>">
        <i class="fa-solid fa-user-gear"></i> Manajemen Role
      </a>
    </li>
    <li>
      <a href="<?= base_url('admin/soal') ?>">
        <i class="fa-solid fa-file-lines"></i> Manajemen Soal
      </a>
    </li>
    <li>
      <a href="<?= base_url('admin/reports') ?>">
        <i class="fa-solid fa-chart-column"></i> Report Nilai
      </a>
    </li>
    <li>
      <a href="<?= base_url('/auth/logout') ?>">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </li>
  </ul>
</div>
