<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>
<div class="content">
  <div class="page-header">
    <h2>Manajemen Kuis</h2>
  </div>

  <!-- Tombol Aksi -->
  <div class="actions">
    <a href="<?= base_url('admin/kuis/create') ?>" class="btn btn-blue">+ Tambah Kuis</a>
  </div>

  <!-- Card berisi tabel -->
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
          <th>ID</th>
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
          <?php foreach ($kuis as $row) : ?>
            <tr>
              <td><?= $row['id_kuis'] ?></td>
              <td><?= esc($row['nama_kuis']) ?></td>
              <td><?= esc($row['topik']) ?></td>
              <td><?= esc($row['tanggal']) ?></td>
              <td><?= esc($row['waktu_mulai']) ?></td>
              <td><?= esc($row['waktu_selesai']) ?></td>
              <td><?= esc($row['nilai_minimum']) ?></td>
              <td><?= esc($row['batas_pengulangan']) ?></td>
              <td><?= esc($row['id_kategori']) ?></td>
              <td>
                <?php if (isset($row['status']) && $row['status'] == 'active') : ?>
                  <span class="badge active">Active</span>
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
        <?php else : ?>
          <tr>
            <td colspan="11">Belum ada data kuis.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<style>
/* Card putih */
.card {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0px 3px 6px rgba(0,0,0,0.08);
  margin-top: 20px;
  width: 100%;
}

/* Header halaman */
.page-header h2 {
  margin: 0 0 15px 0;
  font-size: 22px;
  font-weight: 600;
  color: #333;
}

/* Tombol Aksi */
.actions {
  margin-bottom: 15px;
}
.btn {
  padding: 8px 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  margin-right: 6px;
  font-weight: 500;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-block;
}
.btn-blue { background: #0070C0; color: white; }
.btn-green { background: #28a745; color: white; }
.btn-red { background: #dc3545; color: white; }
.btn-dark { background: #343a40; color: white; }

.btn:hover { opacity: 0.9; transform: scale(1.05); }

/* Tombol kecil untuk tabel */
.btn-sm {
  padding: 4px 10px;
  font-size: 12px;
  margin: 2px;
}

/* Tabel Kuis */
.table-kuis {
  width: 100%;
  border-collapse: collapse;
}
.table-kuis th, .table-kuis td {
  padding: 12px;
  text-align: center;
  border: 1px solid #ddd;
  font-size: 14px;
}
.table-kuis th {
  background: #0070C0;
  color: white;
  font-weight: 600;
}
.table-kuis tbody tr:nth-child(even) {
  background: #f9f9f9;
}
.table-kuis tbody tr:hover {
  background: #f0f8ff;
}

/* Badge status */
.badge {
  padding: 5px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  color: white;
}
.badge.active { background: #28a745; }
.badge.inactive { background: #6c757d; }

/* Kolom Aksi tombol sejajar */
.action-buttons {
  display: flex;
  justify-content: center;
  gap: 5px;
}

/* Alert sukses */
.alert-success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
  padding: 10px 15px;
  border-radius: 6px;
  margin-bottom: 15px;
  font-size: 14px;
}
</style>
<?= $this->endSection() ?>
