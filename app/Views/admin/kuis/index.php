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
                    <th>id</th>
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
                <tr>
                    <td>1</td>
                    <td>Kuis Matematika 1</td>
                    <td>Aljabar</td>
                    <td>10-09-2025</td>
                    <td>08:00</td>
                    <td>09:00</td>
                    <td>70</td>
                    <td>2</td>
                    <td>Junior Agent</td>
                    <td><span class="badge inactive">Inactive</span></td>
                    <td class="actions">
                        <button class="btn btn-upload">Upload</button>
                        <button class="btn btn-edit">Edit</button>
                        <button class="btn btn-delete">Hapus</button>
                        <button class="btn btn-archive">Archive</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Kuis Bahasa</td>
                    <td>Tata Bahasa</td>
                    <td>15-09-2025</td>
                    <td>10:00</td>
                    <td>11:00</td>
                    <td>60</td>
                    <td>1</td>
                    <td>Senior Agent</td>
                    <td><span class="badge active">Active</span></td>
                    <td class="actions">
                        <button class="btn btn-upload">Upload</button>
                        <button class="btn btn-edit">Edit</button>
                        <button class="btn btn-delete">Hapus</button>
                        <button class="btn btn-archive">Archive</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
