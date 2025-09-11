<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>
<div class="content">
  <div class="page-header">
    <h2>Tambah Pengguna</h2>
  </div>

  <div class="card">
    <form action="<?= base_url('admin/users/store_admin') ?>" method="post">
      <?= csrf_field() ?> <!-- CSRF token -->

      <div class="form-group">
        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="nik">NIK</label>
        <input type="text" id="nik" name="nik" class="form-control" required>
      </div>

      <!-- Password dengan toggle -->
      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" class="form-control" required>
          <i class="fa-solid fa-eye-slash toggle-password" id="togglePassword"></i>
        </div>
      </div>

      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" class="form-control" required>
          <option value="">-- Pilih Role --</option>
          <option value="admin">Admin</option>
          <option value="reviewer">Reviewer</option>
        </select>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-green">SIMPAN</button>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-blue">BATAL</a>
      </div>
    </form>
  </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.card { 
background: white; 
border-radius: 8px; 
padding: 20px; 
box-shadow: 0px 3px 6px rgba(0,0,0,0.08); 
margin-top: 20px; 
width: 100%; 
max-width: 600px; }

.page-header h2 { 
  margin: 0 0 15px 0; 
  font-size: 22px; 
  font-weight: 600; 
  color: #333; }

.form-group { 
  margin-bottom: 15px; }
    
.form-group label { 
  display: block; 
  font-weight: 500; 
  margin-bottom: 6px; }

.form-control { 
  width: 100%; 
  padding: 10px; 
  border: 1px solid #ccc; 
  border-radius: 6px; 
  font-size: 14px; }

.password-wrapper { 
  position: relative; }

.password-wrapper 
.form-control { 
  padding-right: 40px; }

.toggle-password { 
  position: absolute; 
  top: 50%; 
  right: 10px; 
  transform: translateY(-50%); 
  cursor: pointer; 
  color: #666; 
  font-size: 16px; }

.form-actions { 
  margin-top: 20px; }

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
  display: inline-block; }

.btn-green { 
  background: #28a745; 
  color: white; }

.btn-blue { 
  background: #0070C0; 
  color: white; }

.btn:hover { 
  opacity: 0.9; 
  transform: scale(1.05); }
</style>

<script>
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');

togglePassword.addEventListener('click', () => {
  const isPassword = passwordInput.getAttribute('type') === 'password';
  passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
  togglePassword.classList.toggle('fa-eye-slash', !isPassword);
  togglePassword.classList.toggle('fa-eye', isPassword);
});
</script>

<?= $this->endSection() ?>
