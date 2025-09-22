<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soal Kuis</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
      background-color: white;
      border-bottom: 1px solid #ddd;
    }
    .nav-link {
      color: #333;
      transition: all 0.2s ease;
    }
    .nav-link:hover {
      color: #0072c6;
    }
    .nav-link.active {
      font-weight: 700;
      color: #0072c6 !important;
      border-bottom: 2px solid #0072c6;
    }

    /* MAIN SECTION */
    .main-section {
      background: linear-gradient(180deg, #0072c6, #005a99);
      padding: 70px 20px;
      min-height: calc(100vh - 120px);
    }
    .main-section h3 {
      font-weight: 600;
      letter-spacing: 0.5px;
      margin-bottom: 25px;
    }

    /* Header Kuis */
    .quiz-title-box {
      background: #ff6f00;
      color: #fff;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 16px;
      display: inline-block;
    }

    /* Soal */
    .quiz-box {
      background: #ffffff;
      color: #333;
      border-radius: 14px;
      padding: 20px 24px;
      margin-bottom: 25px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    .quiz-box p {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 16px;
    }

    .form-check {
      background: #f8f9fa;
      border-radius: 6px;
      padding: 8px 12px;
      margin-bottom: 10px;
      transition: all 0.2s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      font-size: 14px;
    }
    .form-check:hover {
      background: #e9f4ff;
      transform: translateX(3px);
      box-shadow: 0 3px 8px rgba(0,0,0,0.12);
    }

    /* Navigasi Soal */
    .question-nav {
      background: #ffffff;
      border-radius: 10px;
      padding: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.12);
      margin-top: 20px;
      max-width: 420px;     /* makin ramping */
      margin-left: auto;     
      margin-right: 0;       /* rata kanan */
      font-size: 13px;
    }

    .number-grid {
      display: grid;
      grid-template-columns: repeat(10, 1fr);
      gap: 4px;
    }
    .question-btn {
      border-radius: 50%;     /* bulat */
      width: 28px;
      height: 28px;
      font-size: 12px;
      font-weight: 500;
      border: none;
      padding: 0;
      line-height: 28px;
      text-align: center;
    }
    .question-btn.unanswered {
      background-color: #ffb74d;
      color: #fff;
    }
    .question-btn.active {
      background-color: #0072c6;
      color: #fff;
    }
    .question-btn.answered {
      background-color: #4caf50;
      color: #fff;
    }

    .btn-finish {
      background-color: #0072c6;
      color: white;
      font-weight: 600;
      border-radius: 5px;
      font-size: 12px;
      padding: 3px 10px;
      margin-top: 10px;
    }

    /* Footer */
    footer {
      background: #ffffff;
      color: #555;
      padding: 10px;
      text-align: center;
      font-size: 13px;
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
        <img src="<?= base_url('logo.png'); ?>" alt="Melisa Logo" height="32" class="me-2"> Melisa
      </a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="<?= base_url('dashboard'); ?>">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Riwayat</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Ganti Password</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('auth/logout'); ?>">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main -->
  <div class="main-section text-white">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Daftar Pertanyaan</h3>
        <div class="quiz-title-box">Kuis Seni Rupa</div>
      </div>

      <!-- Soal -->
      <div class="quiz-box mt-4">
        <p><b id="question-text">1. Which of the following best describes a collage?</b></p>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="q1" id="q1a">
          <label class="form-check-label" for="q1a">A. A single photograph taken with a camera</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="q1" id="q1b">
          <label class="form-check-label" for="q1b">B. An artwork made by combining different pictures or materials onto a surface</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="q1" id="q1c">
          <label class="form-check-label" for="q1c">C. A drawing done with only pencil lines</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="q1" id="q1d">
          <label class="form-check-label" for="q1d">D. A sculpture made of clay</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="q1" id="q1e">
          <label class="form-check-label" for="q1e">E. A painting created with watercolor</label>
        </div>
      </div>

      <!-- Navigasi Soal -->
      <div class="question-nav">
        <h6>Nomor Soal</h6>
        <div class="d-flex align-items-center justify-content-between mb-3">
          <button class="btn btn-outline-secondary btn-sm me-2" id="prevBtn" disabled>
            <i class="bi bi-chevron-left"></i>
          </button>
          <div class="number-grid flex-grow-1 mx-2" id="numberContainer"></div>
          <button class="btn btn-outline-secondary btn-sm ms-2" id="nextBtn">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
        <button class="btn btn-finish" id="finishBtn">Selesai</button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    © 2025 Melisa. All Rights Reserved.
  </footer>

  <script>
    const totalQuestions = 50;   // misalnya maksimal 50 soal
    const perPage = 10;          // jumlah nomor yang ditampilkan per halaman
    let currentPage = 0;         // mulai dari halaman 0

    const container = document.getElementById("numberContainer");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");

    function renderNumbers() {
      container.innerHTML = "";
      let start = currentPage * perPage + 1;
      let end = Math.min(start + perPage - 1, totalQuestions);

      for (let i = start; i <= end; i++) {
        let btn = document.createElement("button");
        btn.className = "btn question-btn unanswered";
        btn.textContent = i;
        container.appendChild(btn);
      }

      // kontrol tombol prev/next
      prevBtn.disabled = currentPage === 0;
      nextBtn.disabled = end === totalQuestions;
    }

    prevBtn.addEventListener("click", () => {
      if (currentPage > 0) {
        currentPage--;
        renderNumbers();
      }
    });

    nextBtn.addEventListener("click", () => {
      if ((currentPage + 1) * perPage < totalQuestions) {
        currentPage++;
        renderNumbers();
      }
    });

    // render awal
    renderNumbers();
  </script>
</body>
</html>
