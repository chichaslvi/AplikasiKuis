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
        <i class="fa-solid fa-file-lines"></i> Manajemen Kuis
      </a>
    </li>
    <li class="<?= $uri->getSegment(2) === 'reports' ? 'active' : '' ?>">
      <a href="<?= base_url('reviewer/reports') ?>">
        <i class="fa-solid fa-chart-column"></i> Report Nilai
      </a>
    </li>
    <li class="<?= $uri->getSegment(2) === 'password' ? 'active' : '' ?>">
      <a href="<?= base_url('reviewer/password') ?>">
        <i class="fa-solid fa-key"></i> Ganti Password
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
/* Sidebar — sama dengan admin */
.sidebar {
  width: 250px;
  background: #0070C0; /* warna sidebar */
  min-height: 100vh;
  padding: 20px 0;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
}

/* Logo */
.logo-full {
  width: 100% !important;
  height: auto !important;
  display: block;
  margin: 0 auto 20px auto;
}

.sidebar .logo h2 {
  color: white;
}

/* Menu */
.sidebar .menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar .menu li a {
  color: white;
  text-decoration: none;
  display: flex;
  align-items: center;
  padding: 10px 20px;
}

.sidebar .menu li a i {
  color: white;
  width: 20px;
  text-align: center;
  margin-right: 10px;
}

/* Hover & Active */
.sidebar .menu li a:hover,
.sidebar .menu li.active a {
  background: #005a99;
  color: white;
  border-radius: 5px;
}

.sidebar .menu li a:hover i,
.sidebar .menu li.active a i {
  color: white;
}
</style>
