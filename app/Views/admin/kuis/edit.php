<?= $this->extend('layouts/admin/main') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <h2>Edit Kuis</h2>

    <form action="<?= base_url('admin/kuis/update/' . $kuis['id_kuis']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group">
            <label>Nama Kuis</label>
            <input type="text" name="nama_kuis" value="<?= esc($kuis['nama_kuis']) ?>" required>
        </div>

        <div class="form-group">
            <label>Topik</label>
            <input type="text" name="topik" value="<?= esc($kuis['topik']) ?>" required>
        </div>

        <div class="form-group">
            <label>Tanggal Pelaksanaan</label>
            <input type="date" name="tanggal" value="<?= esc($kuis['tanggal']) ?>" required>
        </div>

        <div class="form-group">
            <label>Waktu Mulai</label>
            <input type="time" name="waktu_mulai" value="<?= esc($kuis['waktu_mulai']) ?>" required>
        </div>

        <div class="form-group">
            <label>Waktu Selesai</label>
            <input type="time" name="waktu_selesai" value="<?= esc($kuis['waktu_selesai']) ?>" required>
        </div>

        <div class="form-group">
            <label>Nilai Minimum</label>
            <input type="number" name="nilai_minimum" value="<?= esc($kuis['nilai_minimum']) ?>" required>
        </div>

        <div class="form-group">
            <label>Batas Pengulangan</label>
            <input type="number" name="batas_pengulangan" value="<?= esc($kuis['batas_pengulangan']) ?>" required>
        </div>

        <div class="form-group">
            <label>Kategori Agent</label>
            <select name="id_kategori" required>
                <?php foreach ($kategori as $kat): ?>
                    <option value="<?= $kat['id_kategori'] ?>" 
                        <?= $kuis['id_kategori'] == $kat['id_kategori'] ? 'selected' : '' ?>>
                        <?= esc($kat['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="<?= base_url('admin/kuis') ?>" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?= $this->endSection() ?>
