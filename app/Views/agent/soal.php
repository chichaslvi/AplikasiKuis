<?= $this->include('layouts/agent/navbar'); ?>
<?= $this->section('content') ?>
  <title>Soal Kuis</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    /* ... CSS kamu biarin sama persis ... */
  </style>
</head>
<body>

  <!-- Main -->
  <div class="main-section text-white">
    <div class="container">
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
          <tr><th>Nama Kuis</th><td><?= esc($kuis['nama_kuis']); ?></td></tr>
          <tr><th>Topik</th><td><?= esc($kuis['topik']); ?></td></tr>
          <tr><th>Jumlah Soal</th><td id="totalSoal"></td></tr>
          <tr><th>Jawaban Benar</th><td id="correctCount">0</td></tr>
          <tr><th>Jawaban Salah</th><td id="wrongCount">0</td></tr>
          <tr><th>Total Skor</th><td id="finalScore">0</td></tr>
        </table>
        <div class="d-flex justify-content-center gap-3">
          <a href="<?= base_url('ulangi-quiz/'.$kuis['id_kuis']); ?>" class="btn btn-primary">Ulangi Quiz</a>
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
  <!-- (modal biarin sama persis) -->

  <!-- Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Data soal dari PHP
  const questions = <?= json_encode(
    array_reduce($soalList, function($carry, $item) {
      $carry[$item['id']] = [
        "q" => $item['soal'],
        "options" => [
          "A" => $item['pilihan_a'],
          "B" => $item['pilihan_b'],
          "C" => $item['pilihan_c'],
          "D" => $item['pilihan_d'],
          "E" => $item['pilihan_e']
        ],
        "correct" => $item['jawaban']
      ];
      return $carry;
    }, []),
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
  ) ?>;

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
        if (val) { // biar opsi kosong gak ikut tampil
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
