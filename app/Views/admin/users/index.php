<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>
<div class="content">
  <div class="page-header">
    <h2>Manajemen User</h2>
  </div>

  <!-- Tombol Aksi -->
  <div class="actions">
    <a href="<?= base_url('admin/users/create_admin') ?>" class="btn btn-blue">+ Tambah Akun Admin & Reviewer</a>
    <a href="<?= base_url('admin/users/create_agent') ?>" class="btn btn-blue">+ Tambah Akun Agent</a>
    <select class="filter-role" onchange="filterRole(this.value)">
      <option value="">-- Filter Role --</option>
      <option value="admin" <?= ($selectedRole === 'admin') ? 'selected' : '' ?>>Admin</option>
      <option value="reviewer" <?= ($selectedRole === 'reviewer') ? 'selected' : '' ?>>Reviewer</option>
      <option value="agent" <?= ($selectedRole === 'agent') ? 'selected' : '' ?>>Agent</option>
    </select>
  </div>

  <!-- Card berisi tabel -->
  <div class="card">

    <!-- Alert sukses -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert-success">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <table class="table-user">
      <thead>
        <tr>
          <th>Id</th>
          <th>Nama</th>
          <th>NIK</th>
          <th>Password</th>
          <th>Role</th>
          <th>Kategori Agent</th>
          <th>Nama TL</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($users)) : ?>
          <?php $no = 1; // Counter untuk id berurutan ?>
          <?php foreach ($users as $u) : ?>
            <tr>
              <td><?= $no++ ?></td> <!-- ID berurutan -->
              <td><?= esc($u['nama']) ?></td>
              <td><?= esc($u['nik']) ?></td>
              <td><?= str_repeat('*', 8) ?></td>
              <td><?= esc($u['role']) ?></td>
              <td><?= esc($u['nama_kategori'] ?? '-') ?></td>
              <td><?= esc($u['nama'] ?? '-') ?></td>
              <td class="action-buttons">
                <?php if ($u['role'] === 'agent'): ?>
                    <a href="<?= base_url('admin/users/edit_agent/' . $u['id']) ?>" class="btn btn-green btn-sm">EDIT</a>
                <?php else: ?>
                    <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-green btn-sm">EDIT</a>
                <?php endif; ?>
                <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" class="btn btn-red btn-sm" onclick="return confirm('Yakin hapus user ini?')">HAPUS</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="8">Belum ada data user.</td>
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
.btn:hover { opacity: 0.9; transform: scale(1.05); }

/* Tombol kecil untuk tabel */
.btn-sm {
  padding: 4px 10px;
  font-size: 12px;
  margin: 2px;
}

/* Dropdown Filter Role */
.filter-role {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ccc;
  outline: none;
}
.filter-role:hover {
  border-color: #0070C0;
  box-shadow: 0 0 6px rgba(0, 112, 192, 0.5);
}

/* Tabel User */
.table-user {
  width: 100%;
  border-collapse: collapse;
}
.table-user th, .table-user td {
  padding: 12px;
  text-align: center;
  border: 1px solid #ddd;
  font-size: 14px;
}
.table-user th {
  background: #0070C0;
  color: white;
  font-weight: 600;
}
.table-user tbody tr:nth-child(even) {
  background: #f9f9f9;
}
.table-user tbody tr:hover {
  background: #f0f8ff;
}

/* Kolom Aksi tombol sejajar */
.action-buttons {
  display: flex;
  justify-content: center;
  gap: 5px; /* Jarak antar tombol */
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

<script>
  // Filter role sederhana
  function filterRole(role) {
    if (role) {
      window.location.href = "<?= base_url('admin/users?role=') ?>" + role;
    } else {
      window.location.href = "<?= base_url('admin/users') ?>";
    }
  }
</script>
<?= $this->endSection() ?>
