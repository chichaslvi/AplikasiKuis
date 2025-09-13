<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/admin/kuis.css') ?>">
<div class="content-wrapper">
    <div class="header-actions">
        <a href="<?= base_url('admin/kuis/create') ?>" class="btn btn-primary">
            <i class="fa fa-plus"></i> Tambah Kuis
        </a>
    </div>

    <div class="table-container">
        <table class="table-kuis">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kuis</th>
                    <th>Topik</th>
                    <th>Tanggal Pelaksanaan</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Nilai Minimum</th>
                    <th>Batas Pengulangan</th>
                    <th>Kategori Agent</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($kuis)) : ?>
                    <?php foreach ($kuis as $row) : ?>
                        <tr>
                            <td><?= $row['id_kuis'] ?></td>
                            <td><?= esc($row['nama_kuis']) ?></td>
                            <td><?= esc($row['topik']) ?></td>
                            <td><?= esc($row['tanggal']) ?></td>
                            <td><?= esc($row['waktu_mulai']) ?></td>
                            <td><?= esc($row['waktu_selesai']) ?></td>
                            <td><?= esc($row['nilai_minimum']) ?></td>
                            <td><?= esc($row['batas_pengulangan']) ?></td>
                            <td><?= esc($row['id_kategori']) ?></td>
                            <td>
                                <?php if (isset($row['status']) && $row['status'] == 'active') : ?>
                                    <span class="badge active">Active</span>
                                <?php else : ?>
                                    <span class="badge inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <button class="btn btn-upload">Upload</button>
                                <button class="btn btn-edit">Edit</button>
                                <button class="btn btn-delete">Hapus</button>
                                <button class="btn btn-archive">Archive</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="11" style="text-align:center;">Belum ada data kuis</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
