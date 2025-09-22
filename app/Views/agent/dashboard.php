<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Agent</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body {
    background-color: #f8f9fa;
    margin: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  }
  .navbar {
    background-color: white;
    border-bottom: 1px solid #ddd;
  }

  /* Navbar link */
  .nav-link {
    color: #333;
    transition: all 0.2s ease;
  }
  .nav-link:hover {
    color: #0072c6;
  }
  /* Biar Beranda lebih nonjol */
  .nav-link.active {
    font-weight: 700;
    color: #0072c6 !important;
    border-bottom: 2px solid #0072c6;
  }

  .main-section {
    background: linear-gradient(180deg, #0072c6, #005a99); /* gradient biru */
    padding: 70px 20px 50px;
    color: white;
    flex: 1;
  }
  .card-custom {
    border-radius: 12px;
    background: white;
    color: #333;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12); /* bayangan 3D */
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .card-custom:hover {
    transform: translateY(-5px); /* efek naik saat hover */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
  }
  /* Profil */
  .profile-info p {
    margin-bottom: 12px;
    padding-bottom: 6px;
    border-bottom: 1px solid #eee;
  }
  .profile-info b {
    display: inline-block;
    width: 80px;
    color: #0d0e0fff;
  }
  .profile-card {
    width: 115%;      
    max-width: 420px;
    margin-left: -15px; /* geser profil lebih kiri */
  }
  .col-md-4 h4 {
    margin-left: -15px; /* judul profil ikut kiri */
  }
  /* Kuis */
  .quiz-card {
    max-width: 600px;   /* lebih pendek */
    margin-left: 60px;  /* tetap geser ke kanan */
  }
  .col-md-8 h4 {
    margin-left: 60px; /* judul daftar kuis ikut kanan */
  }
  .quiz-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #eee;
  }
  .quiz-details p {
    margin: 0 0 5px;
  }
  .btn-start {
    background: linear-gradient(135deg, #20c997, #198754);
    color: white;
    font-weight: 500;
    border-radius: 6px;
    padding: 8px 18px;
    font-size: 14px;
    box-shadow: 0 3px 6px rgba(25, 135, 84, 0.3);
    transition: all 0.2s ease;
  }
  .btn-start:hover {
    background: linear-gradient(135deg, #198754, #157347);
    box-shadow: 0 5px 12px rgba(21, 115, 71, 0.4);
    transform: scale(1.05);
  }
  footer {
    background: #ffffff;
    color: #555;
    padding: 12px;
    text-align: center;
    font-size: 14px;
    border-top: 1px solid #eee;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
  }
</style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">
        <img src="logo.png" alt="Melisa Logo" height="32" class="me-2"> Melisa
      </a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="#">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Riwayat</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Ganti Password</a></li>
          <li class="nav-item"><a class="nav-link" href="/auth/logout">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main -->
  <div class="main-section">
    <div class="container">
      <div class="row g-4">

        <!-- Profil -->
        <div class="col-md-4">
          <h4 class="mb-3">Profil</h4>
          <div class="card-custom d-flex align-items-center profile-card">
            <img src="https://cdn-icons-png.flaticon.com/512/219/219969.png" width="80" class="me-3" alt="avatar">
            <div class="profile-info">
              <p><b>Nama</b> Riska Permata</p>
              <p><b>NIK</b> 22574018</p>
              <p><b>Kategori</b> Agent Voice</p>
            </div>
          </div>
        </div>

        <!-- Daftar Kuis -->
<div class="col-md-8">
  <h4 class="mb-3">Daftar Kuis</h4>
  <div class="card-custom quiz-card">
    <div class="quiz-item">
      <div class="quiz-details">
        <p><b>Kuis A</b></p>
        <p>Sub Soal : Kuis Peningkatan Mutu</p>
        <small class="text-muted">
          <i class="bi bi-calendar-event me-1"></i> Kamis, 25 Januari 2024 &nbsp;&nbsp;
          <i class="bi bi-clock me-1"></i> 11:00 - 12:00
        </small>
      </div>
      <a href="<?= base_url('soal'); ?>" class="btn btn-start">
        <i class="bi bi-play-circle me-1"></i> Mulai
      </a>
    </div>
  </div>
</div>


      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    © 2025 Melisa. All Rights Reserved.
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
