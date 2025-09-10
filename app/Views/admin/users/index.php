<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>
<div class="content">
  <div class="page-header">
    <h2>Manajemen User</h2>
  </div>

  <!-- Tombol Aksi -->
  <div class="actions">
    <button class="btn btn-blue">+ Tambah Akun Admin & Reviewer</button>
    <button class="btn btn-blue">+ Tambah Akun Agent</button>
    <select class="filter-role">
      <option value="">-- Filter Role --</option>
      <option value="admin">Admin</option>
      <option value="reviewer">Reviewer</option>
      <option value="agent">Agent</option>
    </select>
  </div>

  <!-- Card berisi tabel -->
  <div class="card">
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
        <tr>
          <td>1</td>
          <td>Rido</td>
          <td>26372</td>
          <td>@infomedia</td>
          <td>Admin</td>
          <td>-</td>
          <td>-</td>
          <td>
            <button class="btn btn-green btn-sm">EDIT</button>
            <button class="btn btn-red btn-sm">HAPUS</button>
          </td>
        </tr>
        <tr>
          <td>2</td>
          <td>Rina</td>
          <td>26483</td>
          <td>@infomedia</td>
          <td>Agent</td>
          <td>Agent Voice</td>
          <td>Hasan</td>
          <td>
            <button class="btn btn-green btn-sm">EDIT</button>
            <button class="btn btn-red btn-sm">HAPUS</button>
          </td>
        </tr>
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
</style>
<?= $this->endSection() ?>
