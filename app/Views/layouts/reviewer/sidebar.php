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
    <?php $uri = service('uri'); ?>
<li class="<?= $uri->getSegment(2) === 'password' ? 'active' : '' ?>">
  <a href="<?= base_url('reviewer/password') ?>">
    <i class="fa fa-key"></i> Ganti Password
  </a>
</li>
    <li>
      <a href="<?= base_url('/auth/logout') ?>">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </li>
  </ul>
</div>

