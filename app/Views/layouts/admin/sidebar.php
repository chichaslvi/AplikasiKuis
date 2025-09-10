<div class="sidebar">
  <div class="logo">
    <img src="assets/img/Logo.png" alt="Melisa Logo" class="logo">
    <h3></h3>
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
      <a href="<?= base_url('admin/roles/index') ?>">
        <i class="fa-solid fa-user-gear"></i> Manajemen Kategori
      </a>
    </li>
    <li>
      <a href="<?= base_url('admin/kuis') ?>">
        <i class="fa-solid fa-file-lines"></i> Manajemen kuis
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

<style>
/* Sidebar tetap ukurannya sama persis dengan sebelumnya */
.sidebar {
  width: 250px;
  background: #0070C0; /* warna sidebar */
  min-height: 100vh;
  padding: 20px 0;
}

.sidebar .logo h2 {
  color: white;
}

.sidebar .menu li a {
  color: white;              /* teks putih */
  text-decoration: none;     /* hilangkan underline */
}

.sidebar .menu li a i {
  color: white;              /* ikon putih */
}

.sidebar .menu li a:hover,
.sidebar .menu li.active a {
  background: #005a99;       /* warna hover/active */
  color: white;
}

.sidebar .menu li a:hover i,
.sidebar .menu li.active a i {
  color: white;
}
</style>
