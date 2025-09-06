<?php
// dummy data user yang login (biasanya diambil dari session)
$user = [
    'nama' => 'Chicha Silvi Aulia',
    'nik'  => '22576004',
];

// dummy jumlah user (biasanya dari query database)
$jumlah_pengguna = [
    'admin'    => 3,
    'reviewer' => 18,
    'agent'    => 25
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Beranda | Melisa</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }
    body {
      display: flex;
      min-height: 100vh;
      background: #f9f9f9;
    }
    .sidebar {
      width: 230px;
      background: #006FBA;
      color: #fff;
      padding: 20px 0;
    }
    .sidebar .logo {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 30px;
    }
    .sidebar .menu {
      list-style: none;
      padding: 0;
    }
    .sidebar .menu li {
      padding: 12px 20px;
    }
    .sidebar .menu li a {
      text-decoration: none;
      color: #fff;
      display: flex;
      align-items: center;
    }
    .sidebar .menu li a:hover {
      background: #005A96;
      border-radius: 6px;
    }
    .content {
      flex: 1;
      padding: 30px;
    }
    .profile-card {
      display: flex;
      align-items: center;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 20px;
      max-width: 500px;
    }
    .profile-card img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-right: 20px;
    }
    .jumlah-container {
      margin-top: 40px;
    }
    .jumlah-boxes {
      display: flex;
      gap: 20px;
    }
    .box {
      flex: 1;
      background: #006FBA;
      color: #fff;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
    }
    .box h2 {
      margin-bottom: 8px;
      font-size: 28px;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">
      <img src="public/assets/img/Logo.png" alt="Melisa Logo" width="120">
    </div>
    <ul class="menu">
      <li><a href="#"><span>🏠</span>&nbsp; Beranda</a></li>
      <li><a href="#"><span>👥</span>&nbsp; Manajemen User</a></li>
      <li><a href="#"><span>⚙️</span>&nbsp; Manajemen Role</a></li>
      <li><a href="#"><span>📝</span>&nbsp; Manajemen Soal</a></li>
      <li><a href="#"><span>📊</span>&nbsp; Report Nilai</a></li>
      <li><a href="#"><span>🚪</span>&nbsp; Logout</a></li>
    </ul>
  </div>

  <!-- Content -->
  <div class="content">
    <h2>Hi, <?= $user['nama']; ?></h2>

    <!-- Profile Card -->
    <div class="profile-card">
      <img src="https://i.ibb.co/6v7YQ9J/avatar.png" alt="Avatar">
      <div>
        <p><strong>Nama:</strong> <?= $user['nama']; ?></p>
        <p><strong>NIK:</strong> <?= $user['nik']; ?></p>
      </div>
    </div>

    <!-- Jumlah Pengguna -->
    <div class="jumlah-container">
      <h3>Jumlah Pengguna</h3>
      <div class="jumlah-boxes">
        <div class="box">
          <h2><?= $jumlah_pengguna['admin']; ?></h2>
          <p>Admin</p>
        </div>
        <div class="box">
          <h2><?= $jumlah_pengguna['reviewer']; ?></h2>
          <p>Reviewer</p>
        </div>
        <div class="box">
          <h2><?= $jumlah_pengguna['agent']; ?></h2>
          <p>Agent</p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
