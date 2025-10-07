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
            <p><b>Kategori</b> <?= esc(session()->get('kategori_nama') ?? '-') ?></p>
          </div>
        </div>
      </div>

      <!-- Riwayat -->
      <div class="col-md-8">
        <h4 class="mb-3 text-white">Riwayat Kuis</h4>

        <?php if (!empty($riwayat)) : ?>
          <?php foreach ($riwayat as $item) : ?>
            <div class="card-custom quiz-card mb-3 p-3 d-flex justify-content-between align-items-center">
              <div class="quiz-info">
                <p class="mb-1 fw-bold"><?= esc($item['nama_kuis']) ?></p>
                <p class="mb-1 text-secondary">Sub Soal : <?= esc($item['sub_soal'] ?? 'Kuis Peningkatan Mutu') ?></p>
                <small class="text-muted">
                  <i class="bi bi-calendar-event me-1"></i>
                  <?= !empty($item['tanggal_pengerjaan']) ? date('l, d F Y', strtotime($item['tanggal_pengerjaan'])) : '-' ?>
                  &nbsp;&nbsp;
                  <i class="bi bi-clock me-1"></i>
                  <?= !empty($item['tanggal_pengerjaan']) ? date('H:i', strtotime($item['tanggal_pengerjaan'])) : '-' ?>
                </small>
              </div>
              <a href="<?= base_url('agent/hasil/'.$item['id_kuis']) ?>" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-eye me-1"></i> Lihat Hasil
              </a>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <div class="alert alert-info" id="empty-info">
             Belum ada riwayat kuis.
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
<style>
  .card-custom {
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}
.card-custom:hover {
  transform: scale(1.02);
}

.profile-card {
  padding: 20px;
}
.profile-img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
}
.profile-info p {
  margin: 5px 0;
}

.quiz-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.quiz-info p {
  margin-bottom: 2px;
}
.btn-primary {
  background-color: #007bff;
  border: none;
}

</style> 