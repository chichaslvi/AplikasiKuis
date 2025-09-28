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
            <p><b>Nama</b> Riska Permata</p>
            <p><b>NIK</b> 22574018</p>
            <p><b>Kategori</b> Agent Voice</p>
          </div>
        </div>
      </div>

      <!-- Riwayat -->
      <div class="col-md-8">
        <h4 class="mb-3 text-white">Riwayat Kuis</h4>

        <?php foreach($riwayatKuis as $item): ?>
          <div class="card-custom quiz-card mb-3">
            <div class="quiz-item d-flex justify-content-between align-items-center">
              <div class="quiz-details">
                <p><b><?= $item['nama_kuis'] ?></b></p>
                <p>Sub Soal : <?= $item['sub_soal'] ?></p>
                <small class="text-muted">
                  <i class="bi bi-calendar-event me-1"></i> <?= $item['tanggal'] ?> &nbsp;&nbsp;
                  <i class="bi bi-clock me-1"></i> <?= $item['waktu'] ?>
                </small>
              </div>
              <a href="<?= base_url('agent/hasil/'.$item['id_kuis']) ?>" class="btn btn-start">
                <i class="bi bi-eye me-1"></i> Lihat Hasil
              </a>
            </div>
          </div>
        <?php endforeach; ?>

      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>