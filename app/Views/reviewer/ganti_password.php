<?= $this->extend('layouts/reviewer/main') ?>
<?= $this->section('content') ?>

<div class="container">
    <h2 class="mb-4">Ganti Password</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('reviewer/ganti-password/update') ?>" method="post">
        <div class="form-group mb-3">
            <label>Password Lama</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Password Baru</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Ubah Password</button>
    </form>
</div>

<?= $this->endSection() ?>
