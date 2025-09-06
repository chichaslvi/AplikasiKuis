<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }
    body {
      display: flex;
      background: #fff;
    }

    /* Sidebar */
    .sidebar {
      width: 230px;
      background: #0070C0;
      color: white;
      min-height: 100vh;
      padding: 20px 0;
      position: fixed;
      left: 0;
      top: 0;
    }
    .logo {
      text-align: center;
      margin-bottom: 40px;
    }
    .logo img {
      width: 60px;
      margin-bottom: 10px;
    }
    .logo h2 {
      font-size: 22px;
      font-weight: 600;
      color: #fff;
    }
    .menu {
      list-style: none;
    }
    .menu li {
      padding: 12px 25px;
      cursor: pointer;
      display: flex;
      align-items: center;
      font-size: 15px;
      transition: background 0.3s;
    }
    .menu li.active, .menu li:hover {
      background: #005c9a;
    }
    .menu li i {
      margin-right: 12px;
      font-size: 16px;
    }

    /* Navbar */
    .navbar {
      background: #0070C0;
      color: white;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding: 0 30px;
      position: fixed;
      top: 0;
      left: 230px; /* agar tidak menutupi sidebar */
      right: 0;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      z-index: 1000;
    }
    .navbar p {
      margin: 0;
      font-weight: 500;
    }

    /* Main */
    .main {
      flex: 1;
      padding: 100px 40px 40px 270px; /* kasih padding atas & kiri biar tidak ketutup navbar/sidebar */
    }
    h1 {
      font-size: 22px;
      margin-bottom: 20px;
    }

    /* Profile Card */
    .profile-card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.15);
      margin-bottom: 40px;
      border-top: 8px solid #0070C0;
      border-top-left-radius: 8px;
    }
    .profile-card img {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      margin-right: 25px;
    }
    .profile-info p {
      margin: 8px 0;
      font-size: 15px;
      border-bottom: 1px solid #ddd;
      padding-bottom: 5px;
      width: 250px;
    }

    /* User Stats */
    .stats h2 {
      font-size: 20px;
      margin-bottom: 20px;
    }
    .stats-cards {
      display: flex;
      gap: 25px;
    }
    .stat-card {
      flex: 1;
      background: white;
      border-radius: 6px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.15);
      overflow: hidden;
    }
    .stat-card .inner {
      background: #0070C0;
      color: white;
      text-align: center;
      padding: 18px;
      font-size: 16px;
      font-weight: 600;
      border-bottom: 3px solid white; /* inner white line */
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">
      <img src="logo.png" alt="Logo">
      <h2>Melisa</h2>
    </div>
    <ul class="menu">
      <li class="active"><i class="fa-solid fa-house"></i> Beranda</li>
      <li><i class="fa-solid fa-users"></i> Manajemen User</li>
      <li><i class="fa-solid fa-user-gear"></i> Manajemen Role</li>
      <li><i class="fa-solid fa-file-lines"></i> Manajemen Soal</li>
      <li><i class="fa-solid fa-chart-column"></i> Report Nilai</li>
      <li><i class="fa-solid fa-right-from-bracket"></i> Logout</li>
    </ul>
  </div>

  <!-- Navbar -->
  <div class="navbar">
    <p>Dashboard Admin</p>
  </div>

  <!-- Main Content -->
  <div class="main">
    <h1>Hi, Chicha Silvi Aulia</h1>
    
    <div class="profile-card">
      <img src="https://cdn-icons-png.flaticon.com/512/6997/6997662.png" alt="Avatar">
      <div class="profile-info">
        <p><b>Nama</b> &nbsp;&nbsp; Chicha Silvi Aulia</p>
        <p><b>NIK</b> &nbsp;&nbsp; 22576004</p>
      </div>
    </div>

    <div class="stats">
      <h2>Jumlah Pengguna</h2>
      <div class="stats-cards">
        <div class="stat-card">
          <div class="inner">3 Admin</div>
        </div>
        <div class="stat-card">
          <div class="inner">18 Reviewer</div>
        </div>
        <div class="stat-card">
          <div class="inner">25 Peserta</div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
