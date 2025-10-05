<?= $this->extend('layouts/admin/main') ?>
<?= $this->section('content') ?>

<style>
:root {
    --primary-color: #0070C0;
    --secondary-color: #005a99;
    --danger-color: #e74c3c;
    --border-color: #e0e0e0;
    --light-gray: #f5f7fa;
    --text-dark: #333;
    --text-medium: #555;
    --text-light: #fff;
}

.content {
    padding: 30px 40px;
    font-family: 'Poppins', sans-serif;
    color: var(--text-dark);
}

.content h3 {
    margin-bottom: 20px;
    font-weight: 600;
    color: var(--primary-color);
}

/* Search box */
.search-box {
    margin-bottom: 18px;
}
.search-box input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
}

/* Container card mirip role-section */
.report-container {
    background-color: var(--light-gray);
    border-radius: 8px;
    padding: 20px;
}

/* Item laporan mirip role-row */
.report-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.2s;
}
.report-item:last-child {
    border-bottom: none;
}
.report-item:hover {
    background-color: rgba(0,112,192,0.05);
}

/* Info teks di kiri */
.report-info-left {
    display: flex;
    flex-direction: column;
}
.report-title {
    font-weight: 500;
    font-size: 14px;
    color: var(--primary-color);
}
.report-sub {
    font-size: 13px;
    color: var(--text-dark);
    margin-top: 2px;
}
.report-datetime {
    font-size: 12px;
    color: var(--text-medium);
    margin-top: 4px;
}

/* Tombol lihat nilai mirip btn-add */
.btn-nilai {
    background: var(--primary-color);
    color: var(--text-light);
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s ease, transform .1s ease;
}
.btn-nilai:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}

/* Responsive sederhana */
@media (max-width: 600px) {
    .report-item {
        flex-direction: column;
        align-items: flex-start;
    }
    .btn-nilai {
        margin-top: 8px;
    }
}

/* Search box dengan input + button */
.search-box {
    margin-bottom: 18px;
    display: flex;
    gap: 8px;
}
.search-box input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
}
.search-box button {
    padding: 10px 16px;
    background: var(--primary-color);
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background .2s ease, transform .1s ease;
}
.search-box button:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}
</style>

<div class="content">
    <h3>Laporan Nilai Peserta</h3>

    <!-- Search box -->
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari kuis...">
        <button id="searchBtn">Cari</button>
    </div>

    <div class="report-container" id="reportList">
        <?php if (!empty($kuis) && is_array($kuis)) : ?>
            <?php foreach ($kuis as $item): ?>
                <div class="report-item">
                    <div class="report-info-left">
                        <div class="report-title"><?= esc($item['nama_kuis']) ?></div>
                        <div class="report-topik"><strong>Topik:</strong> <?= esc($item['topik']) ?></div>
                        <div class="report-datetime">
                            📅 <?= esc($item['tanggal']) ?> &nbsp; ⏰ <?= esc($item['waktu_mulai']) ?> - <?= esc($item['waktu_selesai']) ?>
                        </div>
                    </div>
                    <a href="<?= base_url('admin/report/detail/' . $item['id_kuis']) ?>" class="btn-nilai">Lihat Nilai</a>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="alert alert-info">Belum ada data kuis.</div>
        <?php endif; ?>
    </div>
</div>

<script>
    function filterKuis() {
        let filter = document.getElementById('searchInput').value.toLowerCase();
        let items = document.querySelectorAll('.report-item');
        
        items.forEach(function(item) {
            // Ambil seluruh teks dari elemen item (termasuk angka)
            let textContent = item.innerText.toLowerCase();

            if (textContent.includes(filter)) {
                item.style.display = "";
            } else {
                item.style.display = "none";
            }
        });
    }

    // Event listener untuk ketik langsung
    document.getElementById('searchInput').addEventListener('keyup', filterKuis);

    // Event listener untuk klik tombol
    document.getElementById('searchBtn').addEventListener('click', filterKuis);

    // 🔄 Auto-refresh halaman setiap 60 detik
    setInterval(function() {
        window.location.reload();
    }, 60000);

</script>


<?= $this->endSection() ?>
