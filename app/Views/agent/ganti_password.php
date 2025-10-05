<?= $this->extend('layouts/agent/main') ?>
<?= $this->section('content') ?>

<div class="container mt-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-4 text-primary fw-bold">Ganti Password</h4>

      <form action="<?= base_url('agent/ganti-password/update') ?>" method="post">
        <?= csrf_field() ?> <!-- biar aman dari CSRF -->

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

        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-save"></i> Simpan Perubahan
        </button>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
