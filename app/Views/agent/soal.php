<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soal Kuis</title>

  <!-- CSRF untuk AJAX -->
  <meta name="X-CSRF-HEADER" content="X-CSRF-TOKEN">
  <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">

  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .navbar { background-color: white; border-bottom: 1px solid #ddd; }
    .nav-link { color: #333; transition: all 0.2s ease; }
    .nav-link:hover { color: #0072c6; }
    .nav-link.active { font-weight: 700; color: #0072c6 !important; border-bottom: 2px solid #0072c6; }

    /* MAIN SECTION */
    .main-section {
      background: linear-gradient(180deg, #0072c6, #005a99);
      padding: 70px 20px;
      min-height: calc(100vh - 120px);
    }
    .main-section h3 { font-weight: 600; letter-spacing: 0.5px; margin-bottom: 25px; }

    /* Header Kuis */
    .quiz-title-box {
      background: #ff6f00; color: #fff; font-weight: 600;
      padding: 10px 20px; border-radius: 8px; font-size: 16px; display: inline-block;
    }

    /* Soal */
    .quiz-box {
      background: #ffffff; color: #333; border-radius: 14px;
      padding: 20px 24px; margin-bottom: 25px; box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    .quiz-box p { font-size: 16px; font-weight: 600; margin-bottom: 16px; }

    .form-check {
      background: #f8f9fa; border-radius: 6px; padding: 8px 12px; margin-bottom: 10px;
      transition: all 0.2s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05); font-size: 14px;
    }
    .form-check:hover { background: #e9f4ff; transform: translateX(3px); box-shadow: 0 3px 8px rgba(0,0,0,0.12); }

    /* Navigasi Soal */
    .question-nav {
      background: #ffffff; border-radius: 10px; padding: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.12);
      margin-top: 20px; max-width: 420px; margin-left: auto; margin-right: 0; font-size: 13px;
    }
    .number-grid { display: grid; grid-template-columns: repeat(10, 1fr); gap: 4px; }
    .question-btn { border-radius: 50%; width: 28px; height: 28px; font-size: 12px; font-weight: 500; border: none; padding: 0; line-height: 28px; text-align: center; }
    .question-btn.unanswered { background-color: #ffb74d; color: #fff; }
    .question-btn.active { background-color: #0072c6; color: #fff; }
    .question-btn.answered { background-color: #4caf50; color: #fff; }

    .btn-finish {
      background-color: #0072c6; color: white; font-weight: 600;
      border-radius: 5px; font-size: 12px; padding: 3px 10px; margin-top: 10px;
    }

    /* Footer */
    footer {
      background: #ffffff; color: #555; padding: 10px; text-align: center; font-size: 13px;
      border-top: 1px solid #eee; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    }

    /* Hasil */
    #resultSection {
      display: none; background: #ffffff; border-radius: 14px; padding: 20px; box-shadow: 0 6px 15px rgba(0,0,0,0.15); color: #333;
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
          <li class="nav-item"><a class="nav-link" href="<?= base_url('agent/riwayat'); ?>">Riwayat</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('ganti-password'); ?>">Ganti Password</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('auth/logout'); ?>">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- ====== DATA SOAL (backend) ====== -->
  <?php
  /** @var array $soalList */
  $soalList = $soalList ?? [];
  $questionsArr = [];
  $no = 1;
  foreach ($soalList as $item) {
    $optA = $item['pilihan_a'] ?? null;
    $optB = $item['pilihan_b'] ?? null;
    $optC = $item['pilihan_c'] ?? null;
    $optD = $item['pilihan_d'] ?? null;
    $optE = $item['pilihan_e'] ?? null;

    $jawabanRaw = trim((string)($item['jawaban'] ?? ''));
    $correctKey = null;

    if (in_array($jawabanRaw, ['A','B','C','D','E'], true)) {
      $correctKey = $jawabanRaw;
    } else {
      $map = [
        'A' => $optA,
        'B' => $optB,
        'C' => $optC,
        'D' => $optD,
        'E' => $optE,
      ];
      foreach ($map as $key => $val) {
        if ($val !== null && strcasecmp($jawabanRaw, (string)$val) === 0) {
          $correctKey = $key;
          break;
        }
      }
    }

    $questionsArr[$no] = [
      'q' => $item['soal'] ?? '',
      'options' => [
        'A' => $optA,
        'B' => $optB,
        'C' => $optC,
        'D' => $optD,
        'E' => $optE,
      ],
      'correct' => $correctKey,
    ];
    $no++;
  }
  ?>

  <!-- Main -->
  <div class="main-section text-white">
    <div class="container" id="quizContainer">
      <div id="quizSection">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3>Daftar Pertanyaan</h3>
          <div class="quiz-title-box"><?= esc($kuis['nama_kuis']); ?></div>
        </div>

        <!-- Soal -->
        <div class="quiz-box mt-4" id="quizBox">
          <p><b id="question-text"></b></p>
          <div id="options-container"></div>
        </div>

        <!-- Navigasi Soal -->
        <div class="question-nav">
          <h6>Nomor Soal</h6>
          <div class="d-flex align-items-center justify-content-between mb-3">
            <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="prevBtn" disabled>
              <i class="bi bi-chevron-left"></i>
            </button>
            <div class="number-grid flex-grow-1 mx-2" id="numberContainer"></div>
            <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="nextBtn">
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>
          <button type="button" class="btn btn-finish" id="finishBtn">Selesai</button>
        </div>
      </div>

      <!-- Halaman Hasil -->
      <div id="resultSection" class="mt-4">
        <h4 class="fw-bold text-center mb-3">HASIL KUIS</h4>
        <table class="table table-bordered">
          <tr><th>Nama Kuis</th><td><?= esc($kuis['nama_kuis']); ?></td></tr>
          <tr><th>Topik</th><td><?= esc($kuis['topik']); ?></td></tr>
          <tr><th>Jumlah Soal</th><td id="totalSoal"></td></tr>
          <tr><th>Jawaban Benar</th><td id="correctCount">0</td></tr>
          <tr><th>Jawaban Salah</th><td id="wrongCount">0</td></tr>
          <tr><th>Total Skor</th><td id="finalScore">0</td></tr>
        </table>
        <div class="d-flex justify-content-center gap-3">
          <!-- Tombol Ulangi akan muncul jika skor < nilai_minimum -->
          <a id="retryBtn" href="<?= base_url('ulangi-quiz/'.$kuis['id_kuis']); ?>" class="btn btn-primary" style="display:none;">Ulangi Quiz</a>
          <a href="<?= base_url('dashboard'); ?>" class="btn btn-secondary">Selesai</a>
        </div>
      </div>
    </div>
  </div>

  <footer>© 2025 Melisa. All Rights Reserved.</footer>

  <!-- Modal Konfirmasi -->
  <div class="modal fade" id="confirmFinishModal" tabindex="-1" aria-labelledby="confirmFinishLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-3 shadow">
        <div class="modal-body text-center p-4">
          <i class="bi bi-clipboard-check" style="font-size:3rem; color:#0072c6;"></i>
          <h5 class="mb-3">Selesaikan Kuis?</h5>
          <p class="text-muted">Anda akan mengakhiri kuis dan jawaban akan diserahkan.</p>
          <div class="d-flex justify-content-center gap-2 mt-3">
            <button id="confirmYes" type="button" class="btn btn-success">Ya</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Data soal (key 1..N sinkron dengan navigasi)
    const questions = <?= json_encode($questionsArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?>;

    // nilai minimum dari DB
    const passingScore = <?= (int)($kuis['nilai_minimum'] ?? 0) ?>;

    const totalQuestions = Object.keys(questions).length;
    document.getElementById("totalSoal").innerText = totalQuestions;

    const perPage = 10;
    let currentPage = 0;
    let currentQuestion = 1;
    let answers = {};

    const container = document.getElementById("numberContainer");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const questionText = document.getElementById("question-text");
    const optionsContainer = document.getElementById("options-container");

    function renderNumbers() {
      container.innerHTML = "";
      let start = currentPage * perPage + 1;
      let end = Math.min(start + perPage - 1, totalQuestions);

      for (let i = start; i <= end; i++) {
        let btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn question-btn " + (answers[i] ? "answered" : "unanswered");
        if (i === currentQuestion) btn.classList.add("active");
        btn.textContent = i;

        btn.addEventListener("click", () => {
          currentQuestion = i;
          loadQuestion(i);
          renderNumbers();
        });

        container.appendChild(btn);
      }

      prevBtn.disabled = (currentQuestion === 1);
      nextBtn.disabled = (currentQuestion === totalQuestions);
    }

    function loadQuestion(num) {
      const qData = questions[num];
      questionText.innerText = num + ". " + (qData ? qData.q : "Soal belum tersedia.");
      optionsContainer.innerHTML = "";

      if (qData) {
        Object.entries(qData.options).forEach(([key, val]) => {
          if (val) {
            const div = document.createElement("div");
            div.className = "form-check";
            div.innerHTML = `
              <input class="form-check-input" type="radio" name="q${num}" id="q${num}${key}" value="${key}" ${answers[num]===key?"checked":""}>
              <label class="form-check-label" for="q${num}${key}">${key}. ${val}</label>
            `;
            optionsContainer.appendChild(div);
          }
        });
      }
    }

    document.addEventListener("change", (e) => {
      if (e.target.classList.contains("form-check-input")) {
        answers[currentQuestion] = e.target.value;
        renderNumbers();
      }
    });

    prevBtn.addEventListener("click", () => {
      if (currentQuestion > 1) {
        currentQuestion--;
        const firstOnPage = currentPage * perPage + 1;
        if (currentQuestion < firstOnPage) {
          currentPage = Math.max(0, currentPage - 1);
        }
        loadQuestion(currentQuestion);
        renderNumbers();
      }
    });

    nextBtn.addEventListener("click", () => {
      if (currentQuestion < totalQuestions) {
        currentQuestion++;
        const lastOnPage = (currentPage + 1) * perPage;
        if (currentQuestion > lastOnPage) {
          currentPage++;
        }
        loadQuestion(currentQuestion);
        renderNumbers();
      }
    });

    renderNumbers();
    loadQuestion(currentQuestion);

    // Tombol selesai + modal guard
    const finishBtn = document.getElementById("finishBtn");
    const confirmModalEl = document.getElementById("confirmFinishModal");
    let confirmModal = null;
    if (confirmModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      confirmModal = new bootstrap.Modal(confirmModalEl);
    }

    function hitungDanTampilkanHasil() {
      // cegah double submit
      const finishBtnEl = document.getElementById("finishBtn");
      if (finishBtnEl) finishBtnEl.disabled = true;

      // Hitung hasil
      let benar = 0;
      Object.keys(answers).forEach(no => {
        if (questions[no] && answers[no] === questions[no].correct) benar++;
      });
      let total = typeof totalQuestions !== "undefined" ? totalQuestions : Object.keys(questions).length;
      let salah = total - benar;
      let skor  = total > 0 ? Math.round((benar/total)*100) : 0;

      // Tampilkan ringkas di UI
      const correctEl = document.getElementById("correctCount");
      const wrongEl   = document.getElementById("wrongCount");
      const scoreEl   = document.getElementById("finalScore");
      if (correctEl) correctEl.innerText = benar;
      if (wrongEl)   wrongEl.innerText   = salah;
      if (scoreEl)   scoreEl.innerText   = skor;

      // Tampilkan tombol "Ulangi Quiz" hanya jika skor < nilai_minimum
      const retryBtn = document.getElementById("retryBtn");
      if (typeof passingScore !== "undefined" && retryBtn) {
        retryBtn.style.display = (skor < passingScore) ? "inline-block" : "none";
      }

      // Tampilkan section hasil
      const quizSec   = document.getElementById("quizSection");
      const resultSec = document.getElementById("resultSection");
      if (quizSec)   quizSec.style.display   = "none";
      if (resultSec) resultSec.style.display = "block";

      // ===== Kirim ke server untuk disimpan (TANPA redirect) =====
      const headers = {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      };
      const csrfHeaderMeta = document.querySelector('meta[name="X-CSRF-HEADER"]');
      const csrfTokenMeta  = document.querySelector('meta[name="X-CSRF-TOKEN"]');
      if (csrfHeaderMeta && csrfTokenMeta) {
        headers[csrfHeaderMeta.content] = csrfTokenMeta.content;
      }

      const idKuis = <?= (int)($kuis['id_kuis'] ?? 0) ?>;

      fetch('<?= base_url('agent/kuis/submit'); ?>', {
        method: 'POST',
        headers,
        body: JSON.stringify({ id_kuis: idKuis, answers })
      })
      .then(async (res) => {
        if (!res.ok) throw new Error('Submit gagal');
        return true;
      })
      .catch((err) => {
        console.error(err);
        // biarkan user tetap melihat hasil; tidak ada redirect
      });
    }

    // Handler tombol
    if (finishBtn) {
      finishBtn.addEventListener("click", () => {
        if (confirmModal) { confirmModal.show(); }
        else { hitungDanTampilkanHasil(); }
      });
    }

    const confirmYesBtn = document.getElementById("confirmYes");
    if (confirmYesBtn) {
      confirmYesBtn.addEventListener("click", () => {
        hitungDanTampilkanHasil();
        if (confirmModal) confirmModal.hide();
      });
    }
  </script>
</body>
</html>
