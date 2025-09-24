<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>

<style>
:root {
    --primary-color: #0070C0;     /* biru utama */
    --secondary-color: #005a99;   /* biru lebih gelap untuk hover */
    --success-color: #28a745;     /* hijau untuk sukses */
    --danger-color: #e74c3c;      /* merah untuk hapus */
    --dark-color: #343a40;        /* abu gelap */
    --border-color: #e0e0e0;
    --light-gray: #f5f7fa;
    --text-dark: #333;
    --text-medium: #555;
    --text-light: #fff;
}

/* wrapper */
.content {
    padding: 30px 40px;
    font-family: 'Poppins', sans-serif;
    color: var(--text-dark);
}

/* judul */
.page-header h2 {
    margin-bottom: 26px;
    font-weight: 600;
    color: var(--primary-color);
}

/* card */
.card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0px 3px 6px rgba(0,0,0,0.08);
    margin-top: 20px;
    width: 100%;
    overflow-x: auto;
}

/* tombol */
.actions {
    margin-bottom: 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.btn {
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: background .2s ease, transform .1s ease;
    text-decoration: none;
    display: inline-block;
}
.btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
.btn-blue { background: var(--primary-color); color: var(--text-light); }
.btn-green { background: var(--success-color); color: var(--text-light); }
.btn-red { background: var(--danger-color); color: var(--text-light); }
.btn-dark { background: var(--dark-color); color: var(--text-light); }

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}

/* tabel */
.table-kuis {
    width: 100%;
    border-collapse: collapse;
    min-width: 950px;
}
.table-kuis th, .table-kuis td {
    padding: 12px;
    text-align: center;
    border: 1px solid var(--border-color);
    font-size: 13px;
}
.table-kuis th {
    background: var(--primary-color);
    color: var(--text-light);
    font-weight: 600;
}
.table-kuis tbody tr:nth-child(even) { background: #f9f9f9; }
.table-kuis tbody tr:hover { background: rgba(0,112,192,0.05); }

/* badge */
.badge {
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    color: var(--text-light);
}
.badge.active { background: var(--success-color); }
.badge.inactive { background: #6c757d; }
.badge.upcoming { background: var(--primary-color); }

/* alert sukses */
.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 13px;
}

/* aksi */
.action-buttons {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 5px;
}

/* responsive */
@media screen and (max-width: 768px) {
  .table-kuis th, .table-kuis td { font-size: 12px; padding: 8px; }
  .btn { font-size: 12px; padding: 6px 10px; }
}
</style>

<div class="content">
  <div class="page-header">
    <h2>Manajemen Kuis</h2>
  </div>

  <!-- Tombol Aksi -->
  <div class="actions">
    <a href="<?= base_url('admin/kuis/create') ?>" class="btn btn-blue">+ Tambah Kuis</a>
  </div>

  <div class="card">
    <!-- Alert sukses -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert-success">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <table class="table-kuis">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Kuis</th>
          <th>Topik</th>
          <th>Tanggal</th>
          <th>Waktu Mulai</th>
          <th>Waktu Selesai</th>
          <th>Nilai Min</th>
          <th>Batas Pengulangan</th>
          <th>Kategori Agent</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($kuis)) : ?>
        <?php $no = 1; foreach ($kuis as $row): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= esc($row['nama_kuis']) ?></td>
          <td><?= esc($row['topik']) ?></td>
          <td><?= esc($row['tanggal']) ?></td>
          <td><?= esc($row['waktu_mulai']) ?></td>
          <td><?= esc($row['waktu_selesai']) ?></td>
          <td><?= esc($row['nilai_minimum']) ?></td>
          <td><?= esc($row['batas_pengulangan']) ?></td>
          <td><?= esc($row['kategori']) ?></td>
          <td>
  <?php if ($row['status'] === 'active') : ?>
    <span class="badge active">Active</span>
  <?php elseif ($row['status'] === 'upcoming') : ?>
    <span class="badge upcoming">Upcoming</span>
  <?php else : ?>
    <span class="badge inactive">Inactive</span>
  <?php endif; ?>
</td>
          <td class="action-buttons">
            <a href="<?= base_url('admin/kuis/upload/' . $row['id_kuis']) ?>" class="btn btn-blue btn-sm">UPLOAD</a>
            <a href="<?= base_url('admin/kuis/edit/' . $row['id_kuis']) ?>" class="btn btn-green btn-sm">EDIT</a>
            <a href="<?= base_url('admin/kuis/delete/' . $row['id_kuis']) ?>" class="btn btn-red btn-sm"
               onclick="return confirm('Yakin hapus kuis ini?')">HAPUS</a>
            <a href="<?= base_url('admin/kuis/archive/' . $row['id_kuis']) ?>" class="btn btn-dark btn-sm">ARCHIVE</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="11">Belum ada data kuis.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
