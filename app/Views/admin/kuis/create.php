<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>
<div class="content">
  <div class="page-header">
    <h2>Tambah Kuis</h2>
  </div>

  <div class="card">
    <form id="formKuis" action="<?= base_url('admin/kuis/store_kuis') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="nama_kuis">Nama Kuis</label>
        <input type="text" id="nama_kuis" name="nama_kuis" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="topik">Topik</label>
        <input type="text" id="topik" name="topik" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="tanggal_pelaksanaan">Tanggal Pelaksanaan</label>
        <input type="text" id="tanggal_pelaksanaan" name="tanggal_pelaksanaan" class="form-control" required readonly>
      </div>

      <div class="form-group">
        <label for="waktu_mulai">Waktu Mulai</label>
        <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="waktu_selesai">Waktu Selesai</label>
        <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="nilai_minimum">Nilai Minimum</label>
        <input type="number" id="nilai_minimum" name="nilai_minimum" class="form-control" min="1" max="99" required>
      </div>

      <div class="form-group">
        <label for="batas_pengulangan">Batas Pengulangan</label>
        <input type="number" id="batas_pengulangan" name="batas_pengulangan" 
               class="form-control" min="1" max="9" required
               oninput="this.value=this.value.slice(0,1)">
      </div>

       <div class="form-group import-excel">  
        <div class="import-box">
          <label>Import kuis dari Excel</label>  
          <input type="file" id="file_excel" name="file_excel" accept=".xls,.xlsx" required>  
          <small>Format file: .xls atau .xlsx</small>
        </div>
      </div>

      <div class="form-group">
        <label for="id_kategori">Kategori</label>
        <div>
          <label>
            <input type="checkbox" id="checkAll"> <strong>Pilih Semua</strong>
          </label>
        </div>
        <?php foreach ($kategori as $row): ?>
          <div>
            <label>
              <input type="checkbox" name="id_kategori[]" value="<?= $row['id_kategori'] ?>">
              <?= $row['nama_kategori'] ?>
            </label>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-green">SIMPAN</button>
        <a href="<?= base_url('admin/kuis') ?>" class="btn btn-blue">BATAL</a>
      </div>
    </form>
  </div>
</div>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.min.js"></script>

<script>
flatpickr("#tanggal_pelaksanaan", {
  dateFormat: "Y-m-d",
  allowInput: false,
  monthSelectorType: "static",
  plugins: [
    new confirmDatePlugin({ confirmText: "Done" })
  ]
});

// Pilih Semua checkbox
document.getElementById('checkAll').addEventListener('change', function() {
  const checkboxes = document.querySelectorAll('input[name="id_kategori[]"]');
  checkboxes.forEach(cb => cb.checked = this.checked);
});

// Validasi form sebelum submit
document.getElementById("formKuis").addEventListener("submit", function(e) {
  const checked = document.querySelectorAll('input[name="id_kategori[]"]:checked');
  const fileExcel = document.getElementById("file_excel").value;

  if (checked.length === 0) {
    e.preventDefault();
    alert("Minimal pilih 1 kategori!");
    return;
  }

  if (!fileExcel) {
    e.preventDefault();
    alert("File Excel wajib diunggah!");
    return;
  }
});
</script>




<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.css">

<style>
  /* Import Excel Box */
  .import-box {
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
    padding: 15px;
    margin-top: 10px;
  }
  .import-box label {
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
  }

  /* Card + form styles (tetap) */
  .card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0px 3px 6px rgba(0,0,0,0.08); margin-top: 20px; width: 100%; max-width: 600px; }
  .page-header h2 { margin: 0 0 15px 0; font-size: 22px; font-weight: 600; color: #333; }
  .form-group { margin-bottom: 15px; }
  .form-group label { display: block; font-weight: 500; margin-bottom: 6px; }
  .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
  .form-actions { margin-top: 20px; }
  .btn { padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 6px; font-weight: 500; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
  .btn-green { background: #28a745; color: white; }
  .btn-blue { background: #0070C0; color: white; }

  /* === Kalender sesuai gambar === */
  .flatpickr-calendar {
    border-radius: 6px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.2);
    font-family: Arial, sans-serif;
    width: 320px;
    padding-bottom: 50px; /* ruang buat tombol Done */
  }

  /* Header bulan */
  .flatpickr-months {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    font-size: 16px;
    font-weight: 600;
    border-bottom: none;
  }
  .flatpickr-current-month {
    display: flex;
    justify-content: center;
    align-items: center;
    flex: 1;
  }
  .numInputWrapper, .flatpickr-monthDropdown-month {
    display: none !important; /* sembunyikan input dropdown */
  }

  /* Panah kiri kanan */
  .flatpickr-prev-month, .flatpickr-next-month {
    font-size: 18px;
    padding: 6px;
    border-radius: 6px;
    cursor: pointer;
  }
  .flatpickr-prev-month:hover, .flatpickr-next-month:hover {
    background: #f0f0f0;
  }

  /* Nama hari */
  .flatpickr-weekdays {
    margin-top: 4px;
  }
  .flatpickr-weekday {
    font-weight: bold;
    font-size: 12px;
    color: #555;
  }

  /* Tanggal */
  .flatpickr-day {
    border-radius: 6px;
    height: 38px;
    width: 38px;
    margin: 2px;
    line-height: 38px;
  }
  .flatpickr-day:hover {
    background: #e6f0fb;
  }
  .flatpickr-day.selected {
    background: #0070C0;
    color: #fff;
  }

  /* Tombol Done */
  .flatpickr-confirm {
    position: absolute;
    right: 12px;
    bottom: 10px;
    background: #0070C0;
    color: #fff;
    border-radius: 6px;
    padding: 8px 16px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  }
</style>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.min.js"></script>

<script>
flatpickr("#tanggal_pelaksanaan", {
  dateFormat: "Y-m-d",
  allowInput: false,
  monthSelectorType: "static",
  plugins: [
    new confirmDatePlugin({ confirmText: "Done" })
  ]
});

// Pilih Semua checkbox
document.getElementById('checkAll').addEventListener('change', function() {
  const checkboxes = document.querySelectorAll('input[name="id_kategori[]"]');
  checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

<?= $this->endSection() ?>
