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
            <p><b>Nama</b> <?= esc($user['nama']) ?></p>
            <p><b>NIK</b> <?= esc($user['nik']) ?></p>
            <p><b>Kategori</b> <?= esc($user['kategori_nama'] ?? '-') ?></p>
        </div>
    </div>
</div>


      <!-- Daftar Kuis -->
      <div class="col-md-8">
    <h4 class="mb-3 text-white">Daftar Kuis</h4>
    <?php if (!empty($kuis)) : ?>
        <?php foreach ($kuis as $item) : ?>
            <div class="card-custom quiz-card mb-3">
                <div class="quiz-item d-flex justify-content-between align-items-center">
                    <div class="quiz-details">
                        <p><b><?= esc($item['nama_kuis']); ?></b></p>
                        <p>Sub Soal : <?= esc($item['topik'] ?? '-'); ?></p>
                        <small class="text-muted">
                            <i class="bi bi-calendar-event me-1"></i> 
                            <?= date('l, d F Y', strtotime($item['tanggal'])); ?> &nbsp;&nbsp;
                            <i class="bi bi-clock me-1"></i> 
                            <?= date('H:i', strtotime($item['waktu_mulai'])); ?> - <?= date('H:i', strtotime($item['waktu_selesai'])); ?>
                        </small>
                    </div>
                    <a href="<?= base_url('agent/soal/' . $item['id_kuis']); ?>" class="btn btn-start">
                        <i class="bi bi-play-circle me-1"></i> Mulai
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="alert alert-info">Belum ada kuis tersedia.</div>
    <?php endif; ?>
</div>


    </div>
  </div>
</div>

<?= $this->endSection() ?>
