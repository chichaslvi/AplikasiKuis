<?= $this->extend('layouts/admin/main') ?>
<?= $this->section('content') ?>

<style>
:root {
    --primary-color: #0070C0;
    --secondary-color: #005a99;
    --danger-color: #e74c3c;
    --border-color: #e0e0e0;
    --light-gray: #f5f7fa;
    --text-dark: #333;
    --text-medium: #555;
    --text-light: #fff;
}

.content {
    padding: 30px 40px;
    font-family: 'Poppins', sans-serif;
    color: var(--text-dark);
}

.content h3 {
    margin-bottom: 26px;
    font-weight: 600;
    color: var(--primary-color);
}

/* Container card mirip role-section */
.report-container {
    background-color: var(--light-gray);
    border-radius: 8px;
    padding: 20px;
}

/* Item laporan mirip role-row */
.report-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.2s;
}
.report-item:last-child {
    border-bottom: none;
}
.report-item:hover {
    background-color: rgba(0,112,192,0.05);
}

/* Info teks di kiri */
.report-info-left {
    display: flex;
    flex-direction: column;
}
.report-title {
    font-weight: 500;
    font-size: 14px;
    color: var(--primary-color);
}
.report-sub {
    font-size: 13px;
    color: var(--text-dark);
    margin-top: 2px;
}
.report-datetime {
    font-size: 12px;
    color: var(--text-medium);
    margin-top: 4px;
}

/* Tombol lihat nilai mirip btn-add */
.btn-nilai {
    background: var(--primary-color);
    color: var(--text-light);
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s ease, transform .1s ease;
}
.btn-nilai:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}

/* Responsive sederhana */
@media (max-width: 600px) {
    .report-item {
        flex-direction: column;
        align-items: flex-start;
    }
    .btn-nilai {
        margin-top: 8px;
    }
}
</style>

<div class="content">
    <h3>Laporan Nilai Peserta</h3>

    <div class="report-container">
        <?php if (!empty($kuis) && is_array($kuis)) : ?>
            <?php foreach ($kuis as $item): ?>
                <div class="report-item">
                    <div class="report-info-left">
                        <div class="report-title"><?= esc($item['nama_kuis']) ?></div>
                        <div class="report-sub"><strong>Sub Soal:</strong> <?= esc($item['sub_soal']) ?></div>
                        <div class="report-datetime">
                            📅 <?= esc($item['tanggal']) ?> &nbsp; ⏰ <?= esc($item['waktu_mulai']) ?> - <?= esc($item['waktu_selesai']) ?>
                        </div>
                    </div>
                    <a href="<?= base_url('admin/report/detail/' . $item['id']) ?>" class="btn-nilai">Lihat Nilai</a>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="alert alert-info">Belum ada data kuis.</div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
