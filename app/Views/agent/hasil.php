<?= $this->extend('layouts/agent/main') ?>
<?= $this->section('content') ?>

<style>
  .hasil-section {
      padding: 40px 0;
  }

  .hasil-card {
      background-color: #ffffff20; /* transparan gelap */
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
      display: inline-block; /* supaya width otomatis */
  }

  .hasil-card table {
      background-color: #fff; /* putih polos */
      border-radius: 8px;
      overflow: hidden;
      width: 500px; /* lebih lebar */
      margin: 0 auto; /* center */
  }

  .hasil-card th, .hasil-card td {
      padding: 12px 15px;
      text-align: left;
      background-color: #fff; /* putih polos */
      color: #333;
      border: 1px solid #dee2e6; /* garis tabel */
  }

  .hasil-card th {
      font-weight: 600;
      width: 160px; /* kolom th sedikit lebih lebar */
  }

  .hasil-buttons {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      gap: 15px;
  }

  .hasil-buttons a {
      padding: 12px 25px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      color: #fff;
      transition: all 0.3s ease;
  }

  .btn-detail {
      background: linear-gradient(135deg, #28a745, #1e7e34);
  }

  .btn-detail:hover {
      background: linear-gradient(135deg, #1e7e34, #28a745);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }

  .btn-back {
      background: linear-gradient(135deg, #17a2b8, #117a8b);
  }

  .btn-back:hover {
      background: linear-gradient(135deg, #117a8b, #17a2b8);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }

</style>

<div class="main-section hasil-section text-center">
  <h3 class="text-white mb-3">Terima Kasih</h3>
  <p class="text-white mb-4">Tes Anda telah selesai, berikut hasil dari tes anda</p>

  <div class="hasil-card">
    <table class="table table-bordered mb-0">
      <tr>
        <th>Nama Kuis</th>
        <td><?= esc($hasil['nama_kuis']) ?></td>
      </tr>
      <tr>
        <th>Topik Kuis</th>
        <td><?= esc($hasil['topik']) ?></td>
      </tr>
      <tr>
        <th>Jumlah Soal</th>
        <td><?= esc($hasil['jumlah_soal']) ?></td>
      </tr>
      <tr>
        <th>Jawaban Benar</th>
        <td><?= esc(data: $hasil['jawaban_benar']) ?></td>
      </tr>
      <tr>
        <th>Jawaban Salah</th>
        <td><?= esc($hasil['jawaban_salah']) ?></td>
      </tr>
      <tr class="fw-bold">
        <th>Total Skor</th>
        <td><?= esc($hasil['total_skor']) ?></td>
      </tr>
    </table>

    <div class="hasil-buttons">
      <a href="<?= base_url('agent/hasil/detail/'.$hasil['id_kuis']) ?>" class="btn-detail">Lihat Detail</a>
      <a href="<?= base_url('agent/riwayat') ?>" class="btn-back">Kembali ke Riwayat</a>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
