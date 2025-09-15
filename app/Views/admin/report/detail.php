<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h3 class="mb-3">Laporan Nilai Peserta</h3>

    <!-- Info Kuis -->
    <div class="kuis-info mb-3">
        <p><strong>Kuis:</strong> <?= esc($detail['nama_kuis']) ?></p>
        <p><strong>Sub Soal:</strong> <?= esc($detail['sub_soal']) ?></p>
        <p><strong>Tanggal:</strong> <?= esc($detail['tanggal']) ?> &nbsp; 
           <strong>Waktu:</strong> <?= esc($detail['waktu_mulai']) ?> - <?= esc($detail['waktu_selesai']) ?></p>
    </div>

    <!-- Filter & Download -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <select class="form-select filter-tl">
                <option selected>Filter Berdasarkan Nama TL</option>
                <option value="1">TL A</option>
                <option value="2">TL B</option>
            </select>
        </div>
        <a href="#" class="btn btn-success btn-download">Download</a>
    </div>

    <!-- Tabel Nilai -->
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
<style>
    /* Info kuis */
.kuis-info p {
    margin: 0;
    font-size: 14px;
    color: #333;
}

/* Filter dropdown */
.filter-tl {
    width: 250px;
    border-radius: 6px;
    border: 1px solid #ccc;
    padding: 8px 12px;
    font-size: 14px;
}

/* Tombol download */
.btn-download {
    background-color: #28a745;
    border: none;
    padding: 8px 20px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    color: #fff;
    transition: background 0.3s;
}
.btn-download:hover {
    background-color: #218838;
}

/* Tabel laporan */
.laporan-table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 15px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.laporan-table th,
.laporan-table td {
    padding: 12px 16px;
    text-align: left;
    font-size: 14px;
    border: 1px solid #dee2e6;
}

.laporan-table thead {
    background-color: #007bff;
    color: #fff;
    font-weight: 600;
}

.laporan-table tbody tr:hover {
    background-color: #f9f9f9;
    transition: 0.2s;
}

/* Pesan jika data kosong */
.laporan-table td.text-center {
    color: #777;
    font-style: italic;
}

</style>
<?= $this->endSection() ?>
