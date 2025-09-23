<?= $this->extend('layouts/admin/main') ?>
<?= $this->section('content') ?>

<style>
:root {
    --primary-color: #0070C0;
    --secondary-color: #005a99;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --text-dark: #333;
    --border-color: #ccc;
    --light-gray: #f5f7fa;
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

.card {
    background: var(--light-gray);
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    margin-top: 20px;
    width: 100%;
    max-width: 600px;
}

.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 6px;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: all 0.2s;
}
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(0,112,192,0.15);
}

.password-wrapper {
    position: relative;
}
.password-wrapper .form-control { padding-right: 40px; }
.toggle-password {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    font-size: 16px;
}

.text-danger {
    color: var(--danger-color);
    font-size: 13px;
    margin-top: 4px;
}

.form-actions {
    margin-top: 20px;
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
.btn:hover { transform: translateY(-2px); opacity: 0.9; }
.btn-green { background: var(--success-color); color: var(--text-light); }
.btn-blue { background: var(--primary-color); color: var(--text-light); }
</style>

<div class="content">
  <div class="page-header">
    <h2>Edit Agent</h2>
  </div>

  <div class="card">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert-success" style="margin-bottom:15px; padding:10px 15px; border-radius:6px; background:#d4edda; color:#155724; border:1px solid #c3e6cb;">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/users/update_agent/' . $agent['id']) ?>" method="post">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="nama">Nama</label>
        <input type="text" name="nama" id="nama" value="<?= esc($agent['nama']) ?>" class="form-control" required>
        <?php if(isset($validation) && $validation->hasError('nama')): ?>
          <div class="text-danger"><?= $validation->getError('nama') ?></div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="nik">NIK</label>
        <input type="text" name="nik" id="nik" value="<?= esc($agent['nik']) ?>" class="form-control" required>
        <?php if(isset($validation) && $validation->hasError('nik')): ?>
          <div class="text-danger"><?= $validation->getError('nik') ?></div>
        <?php endif; ?>
      </div>

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

      <div class="form-actions">
        <button type="submit" class="btn btn-green">Update</button>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-blue">Batal</a>
      </div>
    </form>
  </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');

togglePassword.addEventListener('click', () => {
  const isPassword = passwordInput.type === 'password';
  passwordInput.type = isPassword ? 'text' : 'password';
  togglePassword.classList.toggle('fa-eye-slash', !isPassword);
  togglePassword.classList.toggle('fa-eye', isPassword);
});
</script>

<?= $this->endSection() ?>
