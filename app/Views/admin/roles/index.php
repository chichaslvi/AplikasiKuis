<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>

<style>
:root {
    --primary-color: #0070C0;     /* biru utama */
    --secondary-color: #005a99;   /* biru lebih gelap untuk hover */
    --danger-color: #e74c3c;      /* merah untuk hapus */
    --border-color: #e0e0e0;
    --light-gray: #f5f7fa;
    --text-dark: #333;
    --text-medium: #555;
    --text-light: #fff;
}

/* ---------- Styling khusus untuk halaman Manajemen Role ---------- */
.content {
    padding: 30px 40px;
    font-family: 'Poppins', sans-serif;
    color: var(--text-dark);
}

/* Judul & section */
.content h2 {
    margin-bottom: 26px;
    font-weight: 600;
    color: var(--primary-color);
}
.role-section {
    margin-bottom: 30px;
    background-color: var(--light-gray);
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s;
}
.role-section h4 {
    margin-bottom: 12px;
    font-weight: 500;
    color: var(--primary-color);  /* ubah jadi biru */
}

/* bar input + tombol tambah */
.form-inline {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 18px;
}
.form-inline .txt {
    background: #fff;
    border: 1px solid var(--border-color);
    padding: 10px 14px;
    border-radius: 6px;
    flex-grow: 1;
    max-width: 300px;
    color: var(--text-dark);
    outline: none;
    transition: all 0.3s;
}
.form-inline .txt:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(0, 112, 192, 0.2);
}
.form-inline .txt::placeholder {
    color: var(--text-medium);
}

/* tombol tambah (biru) */
.btn-add {
    background: var(--primary-color);
    border: none;
    color: var(--text-light);
    padding: 6px 12px;   /* 👉 lebih kecil dari sebelumnya */
    border-radius: 6px;
    display: inline-flex;
    gap: 6px;
    align-items: center;
    cursor: pointer;
    font-size: 12px;     /* 👉 perkecil font */
    font-weight: 500;
    transition: background .2s ease, transform .1s ease;
}
.btn-add i { font-size: 12px; }
.btn-add:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,112,192,0.3);
}
/* area berisi daftar role */
.role-table {
    list-style: none;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    overflow: hidden;
    background-color: #fff;
}
.role-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.2s;
}
.role-row:last-child {
    border-bottom: none;
}
.role-row:hover {
    background-color: rgba(0,112,192,0.05);
}

/* teks di kiri */
.role-name {
    color: var(--text-dark);
    font-size: 14px;
}

/* tombol hapus (merah) */
.btn-delete {
    background: var(--danger-color);
    border: none;
    color: var(--text-light);
    padding: 6px 12px;   /* 👉 lebih kecil */
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;     /* 👉 perkecil font */
    font-weight: 500;
    transition: background .2s ease, transform .1s ease;
}
.btn-delete:hover {
    background: #c0392b;
    transform: translateY(-2px);
}

/* responsive sederhana */
@media (max-width: 600px) {
    .form-inline {
        flex-direction: column;
        align-items: stretch;
    }
    .form-inline .txt {
        width: 100%;
    }
    .btn-add {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="content">
    <h2>Manajemen Role</h2>

    <!-- ===== KATEGORI AGENT ===== -->
    <div class="role-section">
        <h4>Kategori Agent</h4>

        <form class="form-inline" method="post" action="<?= site_url('admin/roles/storeKategori') ?>">
            <input type="text" name="nama" id="inputKategori" class="txt" placeholder="Masukkan Teks...">
            <button type="submit" id="btnAddKategori" class="btn-add"><i class="fa-solid fa-plus"></i> Tambah Kategori</button>
        </form>

        <h5>Aktif</h5>
        <div class="role-table" id="kategoriListActive">
            <?php foreach ($kategori_active as $k): ?>
                <div class="role-row">
                    <div class="role-name"><?= esc($k['nama_kategori']) ?></div>
                    <a href="<?= site_url('admin/roles/deleteKategori/'.$k['id_kategori']) ?>"
                       class="btn-delete"
                       onclick="return confirm('Nonaktifkan <?= esc($k['nama_kategori']) ?>?')">NONAKTIFKAN</a>
                </div>
            <?php endforeach; ?>
        </div>

        <h5>Nonaktif</h5>
        <div class="role-table" id="kategoriListInactive">
            <?php foreach ($kategori_inactive as $k): ?>
                <div class="role-row">
                    <div class="role-name"><?= esc($k['nama_kategori']) ?></div>
                    <div style="display:flex; gap:6px;">
                        <a href="<?= site_url('admin/roles/activateKategori/'.$k['id_kategori']) ?>"
                           class="btn-add"
                           onclick="return confirm('Aktifkan <?= esc($k['nama_kategori']) ?>?')">AKTIFKAN</a>

                        <a href="<?= site_url('admin/roles/destroyKategori/'.$k['id_kategori']) ?>"
                           class="btn-delete"
                           onclick="return confirm('Hapus permanen <?= esc($k['nama_kategori']) ?>?')">HAPUS</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ===== TEAM LEADER ===== -->
    <div class="role-section">
        <h4>Team Leader</h4>

        <form class="form-inline" method="post" action="<?= site_url('admin/roles/storeTeam') ?>">
            <input type="text" name="nama" id="inputTeam" class="txt" placeholder="Masukkan Teks...">
            <button type="submit" id="btnAddTeam" class="btn-add"><i class="fa-solid fa-plus"></i> Tambah Team Leader</button>
        </form>

        <h5>Aktif</h5>
        <div class="role-table" id="teamListActive">
            <?php foreach ($team_active as $t): ?>
                <div class="role-row">
                    <div class="role-name"><?= esc($t['nama']) ?></div>
                    <a href="<?= site_url('admin/roles/deleteTeam/'.$t['id']) ?>"
                       class="btn-delete"
                       onclick="return confirm('Nonaktifkan <?= esc($t['nama']) ?>?')">NONAKTIFKAN</a>
                </div>
            <?php endforeach; ?>
        </div>

        <h5>Nonaktif</h5>
        <div class="role-table" id="teamListInactive">
            <?php foreach ($team_inactive as $t): ?>
                <div class="role-row">
                    <div class="role-name"><?= esc($t['nama']) ?></div>
                    <div style="display:flex; gap:6px;">
                        <a href="<?= site_url('admin/roles/activateTeam/'.$t['id']) ?>"
                           class="btn-add"
                           onclick="return confirm('Aktifkan <?= esc($t['nama']) ?>?')">AKTIFKAN</a>

                        <a href="<?= site_url('admin/roles/destroyTeam/'.$t['id']) ?>"
                           class="btn-delete"
                           onclick="return confirm('Hapus permanen <?= esc($t['nama']) ?>?')">HAPUS</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.getElementById("btnAddKategori")?.addEventListener("click", function(e) {
    if(!document.getElementById("inputKategori").value.trim()){
        e.preventDefault();
        alert("Teks tidak boleh kosong!");
    }
});

document.getElementById("btnAddTeam")?.addEventListener("click", function(e) {
    if(!document.getElementById("inputTeam").value.trim()){
        e.preventDefault();
        alert("Teks tidak boleh kosong!");
    }
});
</script>

<?= $this->endSection() ?>
