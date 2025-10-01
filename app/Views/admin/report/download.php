<h2 style="text-align:center;">Laporan Nilai Peserta</h2>
<p><strong>Kuis:</strong> <?= esc($detail['nama_kuis']) ?></p>
<p><strong>Topik:</strong> <?= esc($detail['topik']) ?></p>
<p><strong>Tanggal:</strong> <?= esc($detail['tanggal']) ?> &nbsp; 
   <strong>Waktu:</strong> <?= esc($detail['waktu_mulai']) ?> - <?= esc($detail['waktu_selesai']) ?></p>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
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
        <?php if(empty($peserta)): ?>
            <tr><td colspan="6" align="center">Belum ada hasil kuis.</td></tr>
        <?php else: ?>
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
        <?php endif; ?>
    </tbody>
</table>
