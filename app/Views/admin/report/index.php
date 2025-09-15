<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>

<style>
    .report-container {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .report-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #ddd;
    }
    .report-item:last-child {
        border-bottom: none;
    }
    .report-title {
        font-weight: bold;
        margin-bottom: 4px;
        font-size: 16px;
    }
    .report-sub {
        font-size: 14px;
        color: #444;
    }
    .report-info {
        font-size: 13px;
        color: #666;
        margin-top: 4px;
    }
    .btn-nilai {
        background: #1a73e8;
        color: #fff;
        border: none;
        border-radius: 20px;
        padding: 6px 18px;
        font-size: 14px;
        transition: background 0.2s;
        text-decoration: none;
    }
    .btn-nilai:hover {
        background: #0f5bcc;
        color: #fff;
    }
    .report-icon {
        margin-right: 6px;
    }
</style>

<div class="content-wrapper">
    <h3 class="mb-4">Laporan Nilai Peserta</h3>

  <div class="report-container">
    <?php if (!empty($kuis) && is_array($kuis)) : ?>
        <?php foreach ($kuis as $item): ?>
            <div class="report-item">
                <div>
                    <div class="report-title"><?= esc($item['nama_kuis']) ?></div>
                    <div class="report-sub"><strong>Sub Soal :</strong> <?= esc($item['sub_soal']) ?></div>
                    <div class="report-info">
                        <span class="report-icon">📅</span><?= esc($item['tanggal']) ?>
                        &nbsp;&nbsp;
                        <span class="report-icon">⏰</span><?= esc($item['waktu_mulai']) ?> - <?= esc($item['waktu_selesai']) ?>
                    </div>
                </div>
                <a href="<?= base_url('admin/report/detail/' . $item['id']) ?>" class="btn-nilai">Lihat Nilai</a>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="alert alert-info">Belum ada data kuis.</div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

