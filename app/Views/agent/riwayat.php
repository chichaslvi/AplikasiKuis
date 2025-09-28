<?= $this->include('layouts/agent/navbar'); ?>
<?= $this->section('content') ?>
  <title>Riwayat Kuis - Agent</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
    }

    /* Bagian konten utama */
    .content-wrapper {
      background-color: #0d6efd; /* Biru sesuai contoh */
      padding: 40px 20px;
      border-radius: 0 0 20px 20px;
      color: white;
    }

    .card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      background: #fff;
      color: #000;
    }

    .card h5 {
      font-weight: 600;
      margin-bottom: 15px;
      color: #0d6efd;
    }

    /* Profil */
    .profile-card {
      text-align: left;
    }

    .profile-card p {
      margin: 6px 0;
      font-size: 15px;
    }

    /* Riwayat */
    .list-group-item {
      border: none;
      border-radius: 8px;
      margin-bottom: 12px;
      transition: all 0.2s ease;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .list-group-item:hover {
      background: #f1f8ff;
      transform: translateX(4px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .btn-primary.btn-sm {
      font-size: 13px;
      font-weight: 600;
      border-radius: 6px;
      padding: 5px 12px;
    }

    footer {
      background: #fff;
      border-top: 1px solid #e0e0e0;
      padding: 15px;
      font-size: 14px;
      color: #555;
      margin-top: 0;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>
  <div class="content-wrapper">
    <div class="container">
      <div class="row g-4">
        <!-- Profil -->
        <div class="col-md-4">
          <div class="card p-3 profile-card">
            <h5>Profil</h5>
            <p><strong>Nama:</strong> Riska Permata</p>
            <p><strong>NIK:</strong> 22574018</p>
            <p><strong>Kategori:</strong> Agent Voice</p>
          </div>
        </div>

        <!-- Riwayat -->
        <div class="col-md-8">
          <div class="card p-3">
            <h5>Riwayat Kuis</h5>
            <div class="list-group">
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <strong>Kuis A</strong><br>
                  <small class="text-muted">Sub Soal: Kuis Peningkatan Mutu</small><br>
                  <small class="text-secondary">Kamis, 25 Januari 2024 | 11:00 - 12:00</small>
                </div>
                <a href="#" class="btn btn-primary btn-sm">Lihat Hasil</a>
              </div>

              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <strong>Kuis B</strong><br>
                  <small class="text-muted">Sub Soal: Kuis Pengetahuan Produk</small><br>
                  <small class="text-secondary">Senin, 10 Februari 2024 | 09:00 - 10:00</small>
                </div>
                <a href="#" class="btn btn-primary btn-sm">Lihat Hasil</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center">
    <p>&copy; 2025 Melisa. All Rights Reserved.</p>
  </footer>
</body>
</html>
