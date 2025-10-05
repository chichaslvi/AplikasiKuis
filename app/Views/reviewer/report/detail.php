<?= $this->extend('layouts/reviewer/main') ?>
<?= $this->section('content') ?>

<style>
:root {
    --primary-color: #0070C0;
    --secondary-color: #005a99;
    --danger-color: #dc3545;
    --success-color: #28a745;
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

.page-header h2 {
    margin: 0 0 15px 0;
    font-size: 22px;
    font-weight: 600;
    color: var(--primary-color);
}

/* Perbesar info kuis */
.kuis-info p {
    margin: 4px 0;
    font-size: 15px;
}
.kuis-info strong {
    font-size: 16px;
    color: var(--primary-color);
}

/* Tombol Aksi & Filter */
.actions {
    margin-bottom: 15px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
}
.btn {
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
}
.btn:hover { 
    transform: translateY(-2px);
    opacity: 0.9;
}
.btn-blue { 
    background: var(--primary-color);
    color: var(--text-light);
}

/* Dropdown Filter TL */
.filter-role {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    outline: none;
    font-size: 14px;
}
.filter-role:hover {
    border-color: var(--primary-color);
    box-shadow: 0 0 6px rgba(0,112,192,0.2);
}

/* Card putih */
.card {
    background: var(--light-gray);
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    margin-top: 20px;
    width: 100%;
}

/* Tabel */
.table-user {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background: #fff;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.table-user th, .table-user td {
    padding: 12px;
    text-align: center;
    font-size: 14px;
    border-bottom: 1px solid var(--border-color);
}
.table-user th {
    background: var(--primary-color);
    color: var(--text-light);
    font-weight: 600;
}
.table-user tbody tr:nth-child(even) {
    background: #f9f9f9;
}
.table-user tbody tr:hover {
    background-color: rgba(0,112,192,0.05);
    transition: 0.2s;
}

/* Responsive */
@media (max-width: 600px) {
    .actions {
        flex-direction: column;
        gap: 8px;
        align-items: stretch;
    }
    .filter-role, .btn {
        width: 100%;
    }
}
</style>

<div class="content">
  <div class="page-header">
    <h2>Laporan Nilai Peserta</h2>
  </div>

  <div class="kuis-info mb-3">
      <p><strong>Kuis:</strong> <?= esc($detail['nama_kuis']) ?></p>
      <p><strong>Topik:</strong> <?= esc($detail['topik']) ?></p>
      <p><strong>Tanggal:</strong> <?= esc($detail['tanggal']) ?> &nbsp; 
         <strong>Waktu:</strong> <?= esc($detail['waktu_mulai']) ?> - <?= esc($detail['waktu_selesai']) ?></p>
  </div>

  <div class="actions">
    <form method="get" action="<?= base_url('reviewer/report/detail/'.$id_kuis) ?>">
        <select name="team_leader_id" class="filter-role" onchange="this.form.submit()">
            <option value="">-- Filter Berdasarkan TL --</option>
            <?php foreach($teamLeaders as $tl): ?>
                <option value="<?= $tl['id'] ?>" 
                    <?= ($selectedTL == $tl['id']) ? 'selected' : '' ?>>
                    <?= $tl['nama'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <a href="<?= base_url('reviewer/report/download/'.$id_kuis.'?team_leader_id='.$selectedTL) ?>" class="btn btn-blue">Download PDF</a>
  </div>

  <div class="card">
    <table class="table-user">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Agent</th>
          <th>Kategori Agent</th>
          <th>Nama TL</th>
          <th>Skor</th>
          <th>Jumlah Pengerjaan</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($peserta) > 0): ?>
            <?php $no=1; foreach($peserta as $p): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $p['nama_agent'] ?></td>
                    <td><?= $p['kategori_agent'] ?? '-' ?></td>
                    <td><?= $p['nama_tl'] ?? '-' ?></td>
                    <td><?= $p['total_skor'] ?></td>
                    <td><?= $p['jumlah_pengerjaan'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Belum ada hasil kuis.</td>
            </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
    // 🔄 Auto-refresh halaman setiap 60 detik
    setInterval(function() {
        window.location.reload();
    }, 60000); // 60000 ms = 60 detik

</script>
<?= $this->endSection() ?>