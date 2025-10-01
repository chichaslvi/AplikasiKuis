<?= $this->extend('layouts/admin/main') ?>
<?= $this->section('content') ?>

<style>
:root {
    --primary-color: #0070C0;
    --secondary-color: #005a99;
    --danger-color: #dc3545;
    --success-color: #28a745;
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

.page-header h2 {
    margin: 0 0 15px 0;
    font-size: 22px;
    font-weight: 600;
    color: var(--primary-color);
}

/* Tombol Aksi */
.actions {
    margin-bottom: 15px;
}
.btn {
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    margin-right: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
}
.btn:hover { 
    transform: translateY(-2px);
    opacity: 0.9;
}
.btn-blue { 
    background: var(--primary-color);
    color: var(--text-light);
}
.btn-green { 
    background: var(--success-color);
    color: var(--text-light);
}
.btn-red { 
    background: var(--danger-color);
    color: var(--text-light);
}
.btn-sm {
    padding: 4px 10px;
    font-size: 12px;
    margin: 2px;
}

/* Dropdown Filter Role */
.filter-role {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    outline: none;
    font-size: 14px;
}
.filter-role:hover {
    border-color: var(--primary-color);
    box-shadow: 0 0 6px rgba(0,112,192,0.2);
}

/* Card putih */
.card {
    background: var(--light-gray);
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    margin-top: 20px;
    width: 100%;
}

/* Tabel User */
.table-user {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background: #fff;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.table-user th, .table-user td {
    padding: 12px;
    text-align: center;
    font-size: 14px;
    border-bottom: 1px solid var(--border-color);
}
.table-user th {
    background: var(--primary-color);
    color: var(--text-light);
    font-weight: 600;
}
.table-user tbody tr:nth-child(even) {
    background: #f9f9f9;
}
.table-user tbody tr:hover {
    background-color: rgba(0,112,192,0.05);
    transition: 0.2s;
}

/* Kolom Aksi tombol sejajar */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 5px;
}

/* Alert sukses */
.alert-success {
    background: var(--success-color);
    color: var(--text-light);
    border: 1px solid #c3e6cb;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 600px) {
    .actions {
        flex-direction: column;
        gap: 8px;
    }
    .filter-role, .btn {
        width: 100%;
    }
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<div class="content">
  <div class="page-header">
    <h2>Manajemen User</h2>
  </div>

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

  <div class="card">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert-success">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <table class="table-user">
      <thead>
        <tr>
          <th>No</th>
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
          <?php $no = 1; ?>
          <?php foreach ($users as $u) : ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= esc($u['nama']) ?></td>
              <td><?= esc($u['nik']) ?></td>
              <td><?= str_repeat('*', 8) ?></td>
              <td><?= esc($u['role']) ?></td>
              <td><?= esc($u['nama_kategori'] ?? '-') ?></td>
              <td><?= esc($u['nama_tl'] ?? '-') ?></td>
              <td class="action-buttons">
                <?php if ($u['role'] === 'agent'): ?>
                  <a href="<?= base_url('admin/users/edit_agent/' . $u['id']) ?>" class="btn btn-green btn-sm">EDIT</a>
                <?php else: ?>
                  <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-green btn-sm">EDIT</a>
                <?php endif; ?>
                <a href="<?= base_url('admin/users/delete/'.$u['id']) ?>" 
                   class="btn btn-red btn-sm" 
                   onclick="return confirm('Yakin hapus permanen?')">Hapus</a>
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

<script>
function filterRole(role) {
    if (role) {
        window.location.href = "<?= base_url('admin/users?role=') ?>" + role;
    } else {
        window.location.href = "<?= base_url('admin/users') ?>";
    }
}
</script>

<?= $this->endSection() ?>
