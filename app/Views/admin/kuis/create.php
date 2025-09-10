<?= $this->extend('layouts/admin/main') ?>  

<?= $this->section('title') ?> 
Atur Jadwal Kuis 
<?= $this->endSection() ?>  

<?= $this->section('content') ?>     
<div class="form-container">  
    <h2>Atur Jadwal Kuis</h2>  
    <form method="post" enctype="multipart/form-data">  
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

         <div class="form-group">  
            <label>Kategori Agent</label>  
            <input type="text" placeholder="Masukkan Angka">  
        </div>  
        <!-- Import Excel -->
        <div class="form-group import-excel">  
            <label>Import kuis dari Excel</label>  
            <input type="file" name="file_excel" accept=".xls,.xlsx">  
            <small>Format file: .xls atau .xlsx</small>  
        </div>  

        <button type="submit" class="btn-submit">Simpan</button>  

    </form>  
</div>  

<style>
    .form-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .form-group {
        margin-bottom: 15px;
        display: flex;
        flex-direction: column;
    }
    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
    }
    .form-group input {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }
    .btn-submit {
        padding: 10px 18px;
        border: none;
        border-radius: 6px;
        background: #007bff;
        color: white;
        cursor: pointer;
    }
    .btn-submit:hover {
        background: #0056b3;
    }
    .import-excel {
        margin-top: 20px;
        padding: 15px;
        border: 1px dashed #aaa;
        border-radius: 8px;
        background: #f9f9f9;
    }
    .import-excel small {
        color: #666;
    }
</style>

<?= $this->endSection() ?>  
