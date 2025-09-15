<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>
<div class="content">
  <div class="page-header">
    <h2>Edit Agent</h2>
  </div>

  <div class="card">
    <!-- Alert sukses -->
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert-success">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <!-- Form Edit Agent -->
    <form action="<?= base_url('admin/users/update_agent/' . $agent['id']) ?>" method="post">
      <?= csrf_field() ?>

      <!-- Nama -->
      <div class="form-group">
        <label for="nama">Nama</label>
        <input type="text" name="nama" id="nama" value="<?= esc($agent['nama']) ?>" class="form-control" required>
        <?php if(isset($validation) && $validation->hasError('nama')): ?>
          <div class="text-danger"><?= $validation->getError('nama') ?></div>
        <?php endif; ?>
      </div>

      <!-- NIK -->
      <div class="form-group">
        <label for="nik">NIK</label>
        <input type="text" name="nik" id="nik" value="<?= esc($agent['nik']) ?>" class="form-control" required>
        <?php if(isset($validation) && $validation->hasError('nik')): ?>
          <div class="text-danger"><?= $validation->getError('nik') ?></div>
        <?php endif; ?>
      </div>

      <!-- Password -->
      <div class="form-group">
        <label for="password">Password <small>(biarkan kosong jika tidak ingin diubah)</small></label>
        <div class="password-wrapper">
          <input type="password" name="password" id="password" class="form-control">
          <i class="fa-solid fa-eye-slash toggle-password" id="togglePassword"></i>
        </div>
        <?php if(isset($validation) && $validation->hasError('password')): ?>
          <div class="text-danger"><?= $validation->getError('password') ?></div>
        <?php endif; ?>
      </div>

      <!-- Dropdown Kategori Agent -->
      <div class="form-group">
        <label for="kategori_agent_id">Kategori Agent</label>
        <select name="kategori_agent_id" id="kategori_agent_id" class="form-control" required>
          <option value="">-- Pilih Kategori Agent --</option>
          <?php foreach($kategoris as $k): ?>
            <option value="<?= $k['id_kategori'] ?>" <?= $agent['kategori_agent_id'] == $k['id_kategori'] ? 'selected' : '' ?>>
              <?= esc($k['nama_kategori']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if(isset($validation) && $validation->hasError('kategori_agent_id')): ?>
          <div class="text-danger"><?= $validation->getError('kategori_agent_id') ?></div>
        <?php endif; ?>
      </div>

      <!-- Dropdown Team Leader -->
      <div class="form-group">
        <label for="team_leader_id">Team Leader</label>
        <select name="team_leader_id" id="team_leader_id" class="form-control">
          <option value="">-- Pilih Team Leader --</option>
          <?php foreach($teamLeaders as $tl): ?>
            <option value="<?= $tl['id'] ?>" <?= $agent['team_leader_id'] == $tl['id'] ? 'selected' : '' ?>>
              <?= esc($tl['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if(isset($validation) && $validation->hasError('team_leader_id')): ?>
          <div class="text-danger"><?= $validation->getError('team_leader_id') ?></div>
        <?php endif; ?>
      </div>

      <!-- Tombol -->
      <div class="form-actions">
        <button type="submit" class="btn btn-green">Update</button>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-blue">Batal</a>
      </div>
    </form>
  </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0px 3px 6px rgba(0,0,0,0.08); margin-top: 20px; width: 100%; max-width: 600px; }
.page-header h2 { margin: 0 0 15px 0; font-size: 22px; font-weight: 600; color: #333; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; font-weight: 500; margin-bottom: 6px; }
.form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
.password-wrapper { position: relative; }
.password-wrapper .form-control { padding-right: 40px; }
.toggle-password { position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer; color: #666; font-size: 16px; }
.form-actions { margin-top: 20px; }
.btn { padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 6px; font-weight: 500; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
.btn-green { background: #28a745; color: white; }
.btn-blue { background: #0070C0; color: white; }
.btn:hover { opacity: 0.9; transform: scale(1.05); }
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
