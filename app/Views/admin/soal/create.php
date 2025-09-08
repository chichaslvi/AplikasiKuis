<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('title') ?>
Atur Jadwal Kuis
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="form-container">
        <h2>Atur Jadwal Kuis</h2>
        <form>
            <div class="form-group">
                <label>Nama Kuis</label>
                <input type="text" placeholder="Masukkan Teks">
            </div>

            <div class="form-group">
                <label>Topik</label>
                <input type="text" placeholder="Masukkan Teks">
            </div>

            <div class="form-group">
                <label>Tanggal Pelaksanaan Kuis</label>
                <input type="text" placeholder="DD/MM/YYYY">
            </div>

            <div class="form-group">
                <label>Waktu Mulai</label>
                <input type="text" placeholder="HH:MM">
            </div>

            <div class="form-group">
                <label>Waktu Selesai</label>
                <input type="text" placeholder="HH:MM">
            </div>

            <div class="form-group">
                <label>Nilai Minimum</label>
                <input type="text" placeholder="Masukkan Nilai">
            </div>

            <div class="form-group">
                <label>Batas Pengulangan</label>
                <input type="text" placeholder="Masukkan Angka">
            </div>

            <button type="submit" class="btn-submit">Simpan</button>
        </form>
    </div>

    <style>
        
    </style>
<?= $this->endSection() ?>
