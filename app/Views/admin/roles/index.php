<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>

<style>
/* ---------- Styling khusus untuk halaman Manajemen Role ---------- */
.content {
    padding: 30px 40px;
    font-family: "Helvetica Neue", Arial, sans-serif;
    color: #222;
}

/* Judul & section */
.content h2 { margin-bottom: 26px; font-weight: 600; }
.role-section { margin-bottom: 44px; }
.role-section h4 { margin-bottom: 12px; font-weight: 500; color: #222; }

/* bar input + tombol tambah */
.form-inline {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 18px;
}
.form-inline .txt {
    background: #efefef;
    border: 1px solid #ddd;
    padding: 10px 14px;
    border-radius: 6px;
    min-width: 220px;
    color: #666;
    outline: none;
}
.form-inline .txt::placeholder { color: #9b9b9b; }

/* tombol tambah (biru) */
.btn-add {
    background: #0070C0;
    border: none;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    display: inline-flex;
    gap: 8px;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    transition: background .12s ease, transform .06s ease;
}
.btn-add i { font-size: 13px; }
.btn-add:hover {
    background: #005a99;
    transform: translateY(-1px);
}

/* area berisi baris (mirip tabel) */
.role-table { width: 100%; }
.role-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px 14px;
    margin-bottom: 8px;
    background: #fff;
}

/* teks di kiri */
.role-name { color: #333; font-size: 14px; }

/* tombol hapus (merah) */
.btn-delete {
    background: #ff3b30;
    border: none;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: background .12s ease, transform .06s ease;
}
.btn-delete:hover {
    background: #c62828;
    transform: translateY(-1px);
}

/* responsive sederhana */
@media (max-width: 600px) {
    .form-inline { flex-direction: column; align-items: stretch; }
    .form-inline .txt { width: 100%; }
    .btn-add { width: 100%; justify-content: center; }
}
</style>

<div class="content">
    <h2>Manajemen Role</h2>

    <!-- ===== KATEGORI AGENT ===== -->
    <div class="role-section">
        <h4>Kategori Agent</h4>

        <div class="form-inline">
            <input type="text" class="txt" placeholder="Masukkan Teks...">
            <button class="btn-add"><i class="fa-solid fa-plus"></i> Tambah Kategori</button>
        </div>

        <div class="role-table">
            <?php
            // jika controller mengirim $kategori, pakai itu, kalau tidak gunakan default contoh
            $kategori = isset($kategori) ? $kategori : ['Agent Voice', 'Agent Video Call'];
            foreach ($kategori as $k): ?>
                <div class="role-row">
                    <div class="role-name"><?= esc($k) ?></div>
                    <button class="btn-delete" onclick="return confirm('Hapus <?= esc($k) ?>?')">HAPUS</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ===== TEAM LEADER ===== -->
    <div class="role-section">
        <h4>Team Leader</h4>

        <div class="form-inline">
            <input type="text" class="txt" placeholder="Masukkan Teks...">
            <button class="btn-add"><i class="fa-solid fa-plus"></i> Tambah Team Leader</button>
        </div>

        <div class="role-table">
            <?php
            $team = isset($team) ? $team : ['Ahmad', 'Widya', 'Aura', 'Rafa'];
            foreach ($team as $t): ?>
                <div class="role-row">
                    <div class="role-name"><?= esc($t) ?></div>
                    <button class="btn-delete" onclick="return confirm('Hapus <?= esc($t) ?>?')">HAPUS</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
