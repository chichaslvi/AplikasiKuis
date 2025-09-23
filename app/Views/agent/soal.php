<?= $this->include('layout/agent/navbar'); ?>
<?= $this->section('content') ?>
  <title>Soal Kuis</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; }
    .navbar { background-color: white; border-bottom: 1px solid #ddd; }
    .nav-link { color: #333; transition: all 0.2s ease; }
    .nav-link:hover { color: #0072c6; }
    .nav-link.active { font-weight: 700; color: #0072c6 !important; border-bottom: 2px solid #0072c6; }

    .main-section { background: linear-gradient(180deg, #0072c6, #005a99); padding: 70px 20px; min-height: calc(100vh - 120px); }
    .main-section h3 { font-weight: 600; letter-spacing: 0.5px; margin-bottom: 25px; }

    .quiz-title-box { background: #ff6f00; color: #fff; font-weight: 600; padding: 10px 20px; border-radius: 8px; font-size: 16px; display: inline-block; }

    .quiz-box { background: #ffffff; color: #333; border-radius: 14px; padding: 20px 24px; margin-bottom: 25px; box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
    .quiz-box p { font-size: 16px; font-weight: 600; margin-bottom: 16px; }

    .form-check { background: #f8f9fa; border-radius: 6px; padding: 8px 12px; margin-bottom: 10px; transition: all 0.2s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05); font-size: 14px; }
    .form-check:hover { background: #e9f4ff; transform: translateX(3px); box-shadow: 0 3px 8px rgba(0,0,0,0.12); }

    .question-nav { background: #ffffff; border-radius: 10px; padding: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.12); margin-top: 20px; max-width: 420px; margin-left: auto; margin-right: 0; font-size: 13px; }
    .number-grid { display: grid; grid-template-columns: repeat(10, 1fr); gap: 4px; }
    .question-btn { border-radius: 50%; width: 28px; height: 28px; font-size: 12px; font-weight: 500; border: none; padding: 0; line-height: 28px; text-align: center; }
    .question-btn.unanswered { background-color: #ffb74d; color: #fff; }
    .question-btn.active { background-color: #0072c6; color: #fff; }
    .question-btn.answered { background-color: #4caf50; color: #fff; }

    .btn-finish { background-color: #0072c6; color: white; font-weight: 600; border-radius: 5px; font-size: 12px; padding: 3px 10px; margin-top: 10px; }

    footer { background: #ffffff; color: #555; padding: 10px; text-align: center; font-size: 13px; border-top: 1px solid #eee; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); }

    #resultSection { display: none; background: #ffffff; border-radius: 14px; padding: 20px; box-shadow: 0 6px 15px rgba(0,0,0,0.15); color: #333; }
  </style>
</head>
<body>

  <!-- Main -->
  <div class="main-section text-white">
    <div class="container">
      <div id="quizSection">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3>Daftar Pertanyaan</h3>
          <div class="quiz-title-box">Kuis Seni Rupa</div>
        </div>

        <!-- Soal -->
        <div class="quiz-box mt-4" id="quizBox">
          <p><b id="question-text">1. Which of the following best describes a collage?</b></p>
          <div id="options-container"></div>
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

      <!-- Halaman Hasil -->
      <div id="resultSection" class="mt-4">
        <h4 class="fw-bold text-center mb-3">HASIL KUIS</h4>
        <table class="table table-bordered">
          <tr><th>Nama Kuis</th><td>Kuis Seni Rupa</td></tr>
          <tr><th>Topik</th><td>Seni Rupa</td></tr>
          <tr><th>Jumlah Soal</th><td>50</td></tr>
          <tr><th>Jawaban Benar</th><td id="correctCount">0</td></tr>
          <tr><th>Jawaban Salah</th><td id="wrongCount">0</td></tr>
          <tr><th>Total Skor</th><td id="finalScore">0</td></tr>
        </table>
        <div class="d-flex justify-content-center gap-3">
          <a href="<?= base_url('ulangi-quiz') ?>" class="btn btn-primary">Ulangi Quiz</a>
          <a href="<?= base_url('dashboard'); ?>" class="btn btn-secondary">Selesai</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    © 2025 Melisa. All Rights Reserved.
  </footer>

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

  <!-- Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const totalQuestions = 50;
    const perPage = 10;
    let currentPage = 0;
    let currentQuestion = 1;
    let answers = {};

    const container = document.getElementById("numberContainer");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const questionText = document.getElementById("question-text");
    const optionsContainer = document.getElementById("options-container");

    const questions = {
      1: {q: "Which of the following best describes a collage?", options: {A:"A single photograph", B:"An artwork made by combining materials", C:"A pencil drawing", D:"A clay sculpture", E:"A watercolor painting"}, correct:"B"},
      2: {q: "What material is primarily used in sculpture?", options: {A:"Clay", B:"Paper", C:"Ink", D:"Canvas", E:"Digital"}, correct:"A"},
      3: {q: "Which art uses pigment mixed with water?", options: {A:"Oil painting", B:"Watercolor", C:"Sculpture", D:"Collage", E:"Charcoal"}, correct:"B"},
      // dst sampai 50
    };

    function renderNumbers() {
      container.innerHTML = "";
      let start = currentPage * perPage + 1;
      let end = Math.min(start + perPage - 1, totalQuestions);

      for (let i = start; i <= end; i++) {
        let btn = document.createElement("button");
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

      prevBtn.disabled = currentPage === 0;
      nextBtn.disabled = end === totalQuestions;
    }

    function loadQuestion(num) {
      const qData = questions[num];
      questionText.innerText = num + ". " + (qData ? qData.q : "Soal belum tersedia.");
      optionsContainer.innerHTML = "";

      if (qData) {
        Object.entries(qData.options).forEach(([key, val]) => {
          const div = document.createElement("div");
          div.className = "form-check";
          div.innerHTML = `
            <input class="form-check-input" type="radio" name="q${num}" id="q${num}${key}" value="${key}" ${answers[num]===key?"checked":""}>
            <label class="form-check-label" for="q${num}${key}">${key}. ${val}</label>
          `;
          optionsContainer.appendChild(div);
        });
      }
    }

    document.addEventListener("change", (e) => {
      if (e.target.classList.contains("form-check-input")) {
        answers[currentQuestion] = e.target.value;
        renderNumbers();
      }
    });

    prevBtn.addEventListener("click", () => { if (currentPage > 0) { currentPage--; renderNumbers(); } });
    nextBtn.addEventListener("click", () => { if ((currentPage+1)*perPage < totalQuestions) { currentPage++; renderNumbers(); } });

    renderNumbers();
    loadQuestion(currentQuestion);

    // Tombol selesai
    const finishBtn = document.getElementById("finishBtn");
    const confirmModalEl = document.getElementById("confirmFinishModal");
    const confirmModal = new bootstrap.Modal(confirmModalEl);

    finishBtn.addEventListener("click", () => { confirmModal.show(); });

    document.getElementById("confirmYes").addEventListener("click", () => {
      let benar = 0;
      Object.keys(answers).forEach(no => { if (questions[no] && answers[no]===questions[no].correct) benar++; });
      let salah = totalQuestions - benar;
      let skor = Math.round((benar/totalQuestions)*100);

      document.getElementById("correctCount").innerText = benar;
      document.getElementById("wrongCount").innerText = salah;
      document.getElementById("finalScore").innerText = skor;

      document.getElementById("quizSection").style.display = "none";
      document.getElementById("resultSection").style.display = "block";
      confirmModal.hide();
    });
  </script>
</body>
</html>
