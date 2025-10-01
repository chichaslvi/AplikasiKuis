<?= $this->extend('layouts/reviewer/main') ?>
<?= $this->section('content') ?>

<style>
:root {
    --primary-color: #0070C0;
    --secondary-color: #005a99;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --border-color: #e0e0e0;
    --light-gray: #f5f7fa;
    --text-dark: #333;
    --text-medium: #555;
    --text-light: #fff;
    --radius: 8px;
    --transition: all 0.3s ease;
}

.content {
    padding: 30px 40px;
    font-family: 'Poppins', sans-serif;
    color: var(--text-dark);
}

.page-header h2 {
    margin-bottom: 20px;
    font-weight: 600;
    color: var(--primary-color);
}

.card {
    background: #fff;
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: 0px 3px 6px rgba(0,0,0,0.08);
    max-width: 700px;
}

/* Form */
.form-group {
    margin-bottom: 16px;
}
.form-group label {
    font-weight: 500;
    margin-bottom: 6px;
    display: block;
}
.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    font-size: 14px;
    color: var(--text-dark);
    transition: var(--transition);
}
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(0,112,192,0.15);
    outline: none;
}

/* Dropdown Checkbox */
.dropdown {
    position: relative;
    user-select: none;
}
.dropdown-toggle {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.dropdown-toggle:after {
    content: "▼";
    font-size: 12px;
    margin-left: 8px;
}
.dropdown-list {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    max-height: 180px;
    overflow-y: auto;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    background: #fff;
    margin-top: 4px;
    display: none;
    z-index: 100;
    padding: 8px;
}
.dropdown-list label {
    display: block;
    margin-bottom: 4px;
    cursor: pointer;
}

/* Import Excel Box */
.import-box {
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    background: var(--light-gray);
    padding: 15px;
    margin-top: 10px;
}
.import-box label {
    font-weight: 500;
    margin-bottom: 8px;
    display: block;
}
.import-box input[type="file"] {
    width: 100%;
    cursor: pointer;
}

/* Button */
.btn {
    padding: 10px 16px;
    border-radius: var(--radius);
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: var(--transition);
    text-decoration: none;
    display: inline-block;
}
.btn-green {
    background: var(--success-color);
    color: var(--text-light);
}
.btn-green:hover {
    background: #218838;
    transform: translateY(-2px);
}
.btn-blue {
    background: var(--primary-color);
    color: var(--text-light);
}
.btn-blue:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}
.form-actions {
    margin-top: 20px;
    display: flex;
    gap: 12px;
}

/* Flatpickr override */
.flatpickr-calendar {
    border-radius: var(--radius);
    box-shadow: 0 2px 12px rgba(0,0,0,0.2);
    font-family: Poppins, sans-serif;
}
.flatpickr-day.selected {
    background: var(--primary-color);
}
.flatpickr-confirm {
    background: var(--primary-color);
    color: #fff;
    border-radius: var(--radius);
    padding: 6px 12px;
    font-weight: 500;
}
</style>

<div class="content">
  <div class="page-header">
    <h2>Edit Kuis</h2>
  </div>

  <div class="card">
    <form id="formEditKuis" action="<?= base_url('reviewer/kuis/update/' . $kuis['id_kuis']) ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="nama_kuis">Nama Kuis</label>
        <input type="text" id="nama_kuis" name="nama_kuis" class="form-control" 
               value="<?= esc($kuis['nama_kuis']) ?>" required>
      </div>

      <div class="form-group">
        <label for="topik">Topik</label>
        <input type="text" id="topik" name="topik" class="form-control" 
               value="<?= esc($kuis['topik']) ?>" required>
      </div>

      <div class="form-group">
        <label for="tanggal_pelaksanaan">Tanggal Pelaksanaan</label>
        <input type="text" id="tanggal_pelaksanaan" name="tanggal" 
               class="form-control" value="<?= esc($kuis['tanggal']) ?>" 
               required readonly>
      </div>

      <div class="form-group">
        <label for="waktu_mulai">Waktu Mulai</label>
        <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control"
               value="<?= esc($kuis['waktu_mulai']) ?>" required>
      </div>

      <div class="form-group">
        <label for="waktu_selesai">Waktu Selesai</label>
        <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control"
               value="<?= esc($kuis['waktu_selesai']) ?>" required>
      </div>

      <div class="form-group">
        <label for="nilai_minimum">Nilai Minimum</label>
        <input type="number" id="nilai_minimum" name="nilai_minimum" class="form-control"
               min="1" max="99" value="<?= esc($kuis['nilai_minimum']) ?>" required>
      </div>

      <div class="form-group">
        <label for="batas_pengulangan">Batas Pengulangan</label>
        <input type="number" id="batas_pengulangan" name="batas_pengulangan" 
               class="form-control" min="1" max="9" 
               value="<?= esc($kuis['batas_pengulangan']) ?>" required
               oninput="this.value=this.value.slice(0,1)">
      </div>

      <!-- Import Excel -->
      <div class="form-group import-excel">  
          <div class="import-box">
              <label>Import kuis dari Excel</label>  
              <input type="file" id="file_excel" name="file_excel" accept=".xls,.xlsx">  
              <small>Format file: .xls atau .xlsx</small>
          </div>
      </div>

      <!-- Dropdown Kategori Agent -->
      <div class="form-group">
        <label>Kategori Agent</label>
        <div class="dropdown" id="dropdownKategori">
            <div class="dropdown-toggle">Pilih Kategori</div>
            <div class="dropdown-list">
                <label>
                    <input type="checkbox" id="checkAll"> <strong>Pilih Semua</strong>
                </label>
                <?php $selectedKategori = array_column($kuisKategori, 'id_kategori'); ?>
                <?php foreach ($kategori as $row): ?>
                    <label>
                        <input type="checkbox" class="kategoriCheckbox" name="id_kategori[]" value="<?= $row['id_kategori'] ?>"
                            <?= in_array($row['id_kategori'], $selectedKategori) ? 'checked' : '' ?>>
                        <?= esc($row['nama_kategori']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-green">SIMPAN PERUBAHAN</button>
        <a href="<?= base_url('reviewer/kuis') ?>" class="btn btn-blue">BATAL</a>
      </div>
    </form>
  </div>
</div>

<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.min.js"></script>
<script>
flatpickr("#tanggal_pelaksanaan", {
  dateFormat: "Y-m-d",
  allowInput: false,
  monthSelectorType: "static",
  plugins: [new confirmDatePlugin({ confirmText: "Done" })]
});

// Dropdown multi-select
const dropdown = document.getElementById('dropdownKategori');
const toggle = dropdown.querySelector('.dropdown-toggle');
const list = dropdown.querySelector('.dropdown-list');
toggle.addEventListener('click', () => list.style.display = list.style.display === 'block' ? 'none' : 'block');

// Pilih semua kategori
const checkAll = document.getElementById('checkAll');
const checkboxes = document.querySelectorAll('.kategoriCheckbox');
function updateCheckAll() {
    checkAll.checked = Array.from(checkboxes).every(cb => cb.checked);
}
checkboxes.forEach(cb => cb.addEventListener('change', updateCheckAll));
checkAll.addEventListener('change', () => checkboxes.forEach(cb => cb.checked = checkAll.checked));
updateCheckAll();

// Tutup dropdown kalau klik di luar
document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target)) list.style.display = 'none';
});
</script>

<?= $this->endSection() ?>
