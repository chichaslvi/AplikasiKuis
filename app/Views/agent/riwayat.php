<?= $this->extend('layouts/agent/main') ?>
<?= $this->section('content') ?>

<div class="main-section">
  <div class="container">
    <div class="row g-4">

      <!-- Profil -->
      <div class="col-md-4">
        <h4 class="mb-3 text-white">Profil</h4>
        <div class="card-custom profile-card text-center">
          <img src="https://cdn-icons-png.flaticon.com/512/219/219969.png" 
               class="profile-img mb-3" alt="avatar">
          <div class="profile-info text-start">
            <p><b>Nama</b> <?= esc(session()->get('nama')) ?></p>
            <p><b>NIK</b> <?= esc(session()->get('nik')) ?></p>
            <p><b>Kategori</b> <?= esc(session()->get('kategori')) ?></p>
          </div>
        </div>
      </div>

   <!-- Riwayat -->
<div class="col-md-8">
  <h4 class="mb-3 text-white">Riwayat Kuis</h4>

  <?php if (!empty($riwayat)): ?>
    <?php foreach($riwayat as $item): ?>
      <div class="card-custom quiz-card mb-3">
        <div class="quiz-item d-flex justify-content-between align-items-center">
          <div class="quiz-details">
            <p><b><?= esc($item['nama_kuis']) ?></b></p>
            <p>Jumlah Soal : <?= esc($item['jumlah_soal'] ?? 0) ?></p>
            <p>Benar: <?= esc($item['jawaban_benar'] ?? 0) ?> | Salah: <?= esc($item['jawaban_salah'] ?? 0) ?></p>
            <p>Skor: <?= esc($item['total_skor'] ?? 0) ?></p>
            <small class="text-muted">
              <i class="bi bi-calendar-event me-1"></i> 
              <?= !empty($item['tanggal_pengerjaan']) ? date('d-m-Y', strtotime($item['tanggal_pengerjaan'])) : '-' ?>
              &nbsp;&nbsp;
              <i class="bi bi-clock me-1"></i> 
              <?= !empty($item['tanggal_pengerjaan']) ? date('H:i', strtotime($item['tanggal_pengerjaan'])) : '-' ?>
            </small>
          </div>
          <a href="<?= base_url('agent/hasil/'.$item['id_kuis']) ?>" class="btn btn-start">
            <i class="bi bi-eye me-1"></i> Lihat Hasil
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-white">⚠️ Belum ada riwayat kuis.</p>
  <?php endif; ?>

</div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>