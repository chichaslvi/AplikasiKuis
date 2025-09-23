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

/* Info Kuis */
.kuis-info p {
    margin: 0;
    font-size: 14px;
    color: var(--text-dark);
}

/* Filter dropdown */
.filter-tl {
    width: 250px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    padding: 8px 12px;
    font-size: 14px;
    outline: none;
}

/* Tombol download */
.btn-download {
    background: var(--primary-color);
    color: var(--text-light);
    border: none;
    padding: 8px 20px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    text-decoration: none;
    transition: background 0.2s, transform 0.1s;
}
.btn-download:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}

/* Container tabel */
.laporan-table-container {
    background: var(--light-gray);
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* Tabel laporan */
.laporan-table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 10px;
    background: #fff;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.laporan-table th,
.laporan-table td {
    padding: 12px 16px;
    text-align: left;
    font-size: 14px;
    border-bottom: 1px solid var(--border-color);
}

.laporan-table thead {
    background-color: var(--primary-color);
    color: var(--text-light);
    font-weight: 600;
}

.laporan-table tbody tr:hover {
    background-color: rgba(0,112,192,0.05);
    transition: 0.2s;
}

/* Pesan jika data kosong */
.laporan-table td.text-center {
    color: var(--text-medium);
    font-style: italic;
}

/* Responsive */
@media (max-width: 600px) {
    .d-flex {
        flex-direction: column !important;
        gap: 8px;
    }
    .filter-tl, .btn-download {
        width: 100%;
    }
}
</style>

<div class="content">
    <h3>Laporan Nilai Peserta</h3>

    <div class="kuis-info mb-3">
        <p><strong>Kuis:</strong> <?= esc($detail['nama_kuis']) ?></p>
        <p><strong>Sub Soal:</strong> <?= esc($detail['sub_soal']) ?></p>
        <p><strong>Tanggal:</strong> <?= esc($detail['tanggal']) ?> &nbsp; 
           <strong>Waktu:</strong> <?= esc($detail['waktu_mulai']) ?> - <?= esc($detail['waktu_selesai']) ?></p>
    </div>

    <!-- Filter & Download -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <select class="filter-tl">
            <option selected>Filter Berdasarkan Nama TL</option>
            <option value="1">TL A</option>
            <option value="2">TL B</option>
        </select>
        <a href="#" class="btn-download">Download</a>
    </div>

    <div class="laporan-table-container">
        <table class="table laporan-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Skor</th>
                    <th>Pengulangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($detail['peserta'])): ?>
                    <?php foreach ($detail['peserta'] as $p): ?>
                        <tr>
                            <td><?= esc($p['nama']) ?></td>
                            <td><?= esc($p['username']) ?></td>
                            <td><?= esc($p['nilai']) ?></td>
                            <td><?= esc($p['pengulangan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data peserta.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
