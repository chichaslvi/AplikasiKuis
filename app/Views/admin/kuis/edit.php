<?= $this->extend('layouts/admin/main') ?>
<?= $this->section('content') ?>

<div class="content">
  <div class="page-header">
    <h2>Edit Kuis</h2>
  </div>

  <div class="card">
    <form id="formEditKuis" action="<?= base_url('admin/kuis/update/' . $kuis['id_kuis']) ?>" method="post" enctype="multipart/form-data">
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

     <!-- Checkbox Kategori Agent -->
<div class="form-group">
    <label for="id_kategori">Kategori Agent</label>
    <div>
        <label>
            <input type="checkbox" id="checkAll"> <strong>Pilih Semua</strong>
        </label>
    </div>

    <?php 
    // Ambil array id_kategori dari pivot table kuis_kategori
    // $kuisKategori = hasil query pivot table kuis_kategori untuk kuis ini
    $selectedKategori = array_column($kuisKategori, 'id_kategori'); 
    ?>

    <?php foreach ($kategori as $row): ?>
        <div>
            <label>
                <input type="checkbox" class="kategoriCheckbox" name="id_kategori[]" value="<?= $row['id_kategori'] ?>"
                    <?= in_array($row['id_kategori'], $selectedKategori) ? 'checked' : '' ?>>
                <?= esc($row['nama_kategori']) ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Fitur "Pilih Semua"
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.kategoriCheckbox');

    // Set "Pilih Semua" checked jika semua checkbox sudah dicentang
    function updateCheckAll() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkAll.checked = allChecked;
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateCheckAll);
    });

    // Event klik "Pilih Semua"
    checkAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Jalankan sekali saat load untuk set status checkAll
    updateCheckAll();
</script>

      <div class="form-actions">
        <button type="submit" class="btn btn-green">SIMPAN PERUBAHAN</button>
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


</script>


<style>
  .card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0px 3px 6px rgba(0,0,0,0.08); margin-top: 20px; width: 100%; max-width: 600px; }
  .page-header h2 { margin: 0 0 15px 0; font-size: 22px; font-weight: 600; color: #333; }
  .form-group { margin-bottom: 15px; }
  .form-group label { display: block; font-weight: 500; margin-bottom: 6px; }
  .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
  .form-actions { margin-top: 20px; }
  .btn { padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-right: 6px; font-weight: 500; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
  .btn-green { background: #28a745; color: white; }
  .btn-blue { background: #0070C0; color: white; }

  .flatpickr-calendar { border-radius: 6px; box-shadow: 0 2px 12px rgba(0,0,0,0.2); font-family: Arial, sans-serif; width: 320px; padding-bottom: 50px; }
  .flatpickr-months { display: flex; justify-content: space-between; align-items: center; padding: 12px; font-size: 16px; font-weight: 600; }
  .flatpickr-current-month { display: flex; justify-content: center; align-items: center; flex: 1; }
  .numInputWrapper, .flatpickr-monthDropdown-month { display: none !important; }
  .flatpickr-prev-month, .flatpickr-next-month { font-size: 18px; padding: 6px; border-radius: 6px; cursor: pointer; }
  .flatpickr-prev-month:hover, .flatpickr-next-month:hover { background: #f0f0f0; }
  .flatpickr-weekday { font-weight: bold; font-size: 12px; color: #555; }
  .flatpickr-day { border-radius: 6px; height: 38px; width: 38px; margin: 2px; line-height: 38px; }
  .flatpickr-day:hover { background: #e6f0fb; }
  .flatpickr-day.selected { background: #0070C0; color: #fff; }
  .flatpickr-confirm { position: absolute; right: 12px; bottom: 10px; background: #0070C0; color: #fff; border-radius: 6px; padding: 8px 16px; font-weight: 600; cursor: pointer; }
</style>

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



<?= $this->endSection() ?>
