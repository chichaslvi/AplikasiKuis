<?php $uri = service('uri'); ?>

<div class="sidebar">
  <div class="logo">
  <img src="<?= base_url('assets/img/Logo.png') ?>" alt="Melisa Logo" class="logo-img">
  <h3></h3>
</div>

  <ul class="menu">
    <li class="<?= $uri->getSegment(2) === 'dashboard' ? 'active' : '' ?>">
      <a href="<?= base_url('reviewer/dashboard') ?>">
        <i class="fa-solid fa-house"></i> Beranda
      </a>
    </li>
    <li class="<?= $uri->getSegment(2) === 'kuis' ? 'active' : '' ?>">
      <a href="<?= base_url('reviewer/kuis') ?>">
        <i class="fa-solid fa-file-lines"></i> Manajemen kuis
      </a>
    </li>
    <li class="<?= $uri->getSegment(2) === 'reports' ? 'active' : '' ?>">
      <a href="<?= base_url('reviewer/reports') ?>">
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
  .sidebar {
  position: fixed;       /* biar selalu di kiri */
  top: 0;
  left: 0;
  width: 250px;
  background: #0070C0;
  min-height: 100vh;
  padding: 20px 0;
  z-index: 1000;
}
/* Sidebar tetap ukurannya sama persis dengan sebelumnya */
.sidebar {
  width: 250px;
  background: #0070C0; /* warna sidebar */
  min-height: 100vh;
  padding: 20px 0;
}

/* Logo full */
.logo-full {
  width: 100% !important;   /* paksa selebar sidebar */
  height: auto !important;  /* proporsional */
  display: block;
  margin: 0 auto 20px auto;
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
  width: 20px;               /* samakan lebar ikon */
  text-align: center;        /* ikon di tengah kotak */
  margin-right: 10px;        /* jarak ke teks */
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
