<?= $this->extend('layouts/agent/main') ?>
<?= $this->section('content') ?>

<div class="container mt-5">
  <h3 class="mb-4">Ganti Password</h3>

  <form action="<?= base_url('agent/ganti-password/update') ?>" method="post">
    <div class="mb-3">
      <label for="current_password" class="form-label">Password Lama</label>
      <input type="password" name="current_password" id="current_password" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="new_password" class="form-label">Password Baru</label>
      <input type="password" name="new_password" id="new_password" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
      <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
  </form>
</div>

<?= $this->endSection() ?>
