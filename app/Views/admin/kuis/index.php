<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>

<style>
/* (CSS kamu tetap, tidak diubah) */
:root {
    --primary-color: #0070C0;     /* biru utama */
    --secondary-color: #005a99;   /* biru lebih gelap untuk hover */
    --success-color: #28a745;     /* hijau untuk sukses */
    --danger-color: #e74c3c;      /* merah untuk hapus */
    --dark-color: #343a40;        /* abu gelap */
    --border-color: #e0e0e0;
    --light-gray: #f5f7fa;
    --text-dark: #333;
    --text-medium: #555;
    --text-light: #fff;
}
html, body {
    overflow-x: hidden;
    max-width: 100%;
}
.content { padding: 3px 1px; font-family: 'Poppins', sans-serif; color: var(--text-dark); }
.page-header h2 { margin-bottom: 26px; font-weight: 600; color: var(--primary-color); }
.card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0px 3px 6px rgba(0,0,0,0.08); margin-top: 20px; width: 100%; overflow-x: hidden; }
.actions { margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 10px; }
.btn { padding: 8px 14px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500; transition: background .2s ease, transform .1s ease; text-decoration: none; display: inline-block; }
.btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
.btn-blue { background: var(--primary-color); color: var(--text-light); }
.btn-green { background: var(--success-color); color: var(--text-light); }
.btn-red { background: var(--danger-color); color: var(--text-light); }
.btn-dark { background: var(--dark-color); color: var(--text-light); }
.btn-sm { padding: 5px 10px; font-size: 12px; }
.table-kuis { width: 100%; border-collapse: collapse; table-layout: auto; }
.table-kuis th, .table-kuis td { padding: 12px; text-align: center; font-size: 13px; white-space: nowrap; }
.table-kuis th { background: var(--primary-color); color: var(--text-light); font-weight: 600; }
.table-kuis tbody tr:nth-child(even) { background: #f9f9f9; }
.table-kuis tbody tr:hover { background: rgba(0,112,192,0.05); }
.badge { padding: 5px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; color: var(--text-light); }
.badge.active { background: var(--success-color); }
.badge.inactive { background: #6c757d; }
.badge.draft { background: var(--primary-color); }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; }
.action-buttons { display: flex; justify-content: center; flex-wrap: nowrap; gap: 5px; }
@media screen and (max-width: 768px) {
  .table-kuis th, .table-kuis td { font-size: 12px; padding: 8px; }
  .btn { font-size: 12px; padding: 6px 10px; }
}
button:disabled,
.btn[disabled] { opacity: 0.5; cursor: not-allowed; box-shadow: none !important; transform: none !important; }
.btn-outline { background: #fff; color: var(--primary-color); border: 1px solid var(--primary-color); }
.btn-outline:hover { background: var(--primary-color); color: #fff; }
/* ✅ agar <a class="btn disabled"> benar2 non-klik */
.btn.disabled { pointer-events: none; opacity: 0.5; cursor: not-allowed; box-shadow: none !important; transform: none !important; }
</style>

<div class="content">
  <div class="page-header">
    <h2>Manajemen Kuis</h2>
  </div>

  <div class="actions">
    <a href="<?= base_url('admin/kuis/create') ?>" class="btn btn-blue">+ Tambah Kuis</a>
  </div>

  <div class="card">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert-success">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>

    <table class="table-kuis">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Kuis</th>
          <th>Topik</th>
          <th>Tanggal</th>
          <th>Waktu Mulai</th>
          <th>Waktu Selesai</th>
          <th>Nilai Min</th>
          <th>Batas Pengulangan</th>
          <th>Kategori Agent</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>

      <tbody>
      <?php if (!empty($kuis)) : ?>
        <?php $no = 1; foreach ($kuis as $row): ?>
        <tr data-kuis-id="<?= esc($row['id_kuis']) ?>">
          <td><?= $no++ ?></td>
          <td class="col-nama"><?= esc($row['nama_kuis']) ?></td>
          <td class="col-topik"><?= esc($row['topik']) ?></td>
          <td class="col-tanggal"><?= esc($row['tanggal']) ?></td>
          <td class="col-mulai"><?= esc($row['waktu_mulai']) ?></td>
          <td class="col-selesai"><?= esc($row['waktu_selesai']) ?></td>
          <td class="col-nilai"><?= esc($row['nilai_minimum']) ?></td>
          <td class="col-batas"><?= esc($row['batas_pengulangan']) ?></td>
          <td class="col-kategori"><?= esc($row['kategori']) ?></td>

          <!-- STATUS -->
          <td>
            <?php
                $statusDb = strtolower($row['status'] ?? 'draft');
                $tz = new \DateTimeZone('Asia/Jakarta');
                $now = new \DateTime('now', $tz);
                $start = !empty($row['tanggal']) && !empty($row['waktu_mulai']) 
                         ? new \DateTime($row['tanggal'].' '.$row['waktu_mulai'], $tz) : null;
                $end = !empty($row['tanggal']) && !empty($row['waktu_selesai']) 
                       ? new \DateTime($row['tanggal'].' '.$row['waktu_selesai'], $tz) : null;
                if ($start && $end && $end <= $start) $end->modify('+1 day');
                $statusView = ($statusDb === 'active' && $end && $now >= $end) ? 'inactive' : $statusDb;
                $badgeClass = $statusView;
            ?>
            <span class="badge badge-status <?= $badgeClass ?>"
                  data-id="<?= $row['id_kuis'] ?>"
                  data-val="<?= $statusView ?>">
              <?= ucfirst($statusView) ?>
            </span>
          </td>
          <!-- END STATUS -->

          <td class="action-buttons">
            <?php if ($row['status'] === 'draft'): ?>
              <a href="<?= base_url('admin/kuis/upload/' . $row['id_kuis']) ?>"
                 class="btn btn-blue btn-sm btn-upload"
                 onclick="return confirm('Yakin upload kuis ini?')">Upload</a>
            <?php else: ?>
              <button class="btn btn-sm btn-upload" disabled>Upload</button>
            <?php endif; ?>

            <?php if ($statusView === 'inactive'): ?>
              <button class="btn btn-green btn-sm btn-edit" disabled>EDIT</button>
            <?php else: ?>
              <a href="<?= base_url('admin/kuis/edit/' . $row['id_kuis']) ?>" class="btn btn-green btn-sm btn-edit">EDIT</a>
            <?php endif; ?>

            <a href="<?= base_url('admin/kuis/delete/' . $row['id_kuis']) ?>" class="btn btn-red btn-sm"
               onclick="return confirm('Yakin hapus kuis ini?')">HAPUS</a>
            <a href="<?= base_url('admin/kuis/archive/' . $row['id_kuis']) ?>" class="btn btn-dark btn-sm">ARCHIVE</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="11">Belum ada data kuis.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const ENDPOINT     = "<?= base_url('admin/kuis/pollStatus') ?>";
  const UPLOAD_BASE  = "<?= base_url('admin/kuis/upload') ?>";
  const POLL_MS      = 3000; // 3 detik

  function setText(row, selector, value) {
    const el = row.querySelector(selector);
    if (!el) return;
    const v = (value ?? '').toString();
    if (el.textContent !== v) el.textContent = v;
  }

  function updateStatusBadge(badge, status) {
    if (!badge) return;
    const s = (status || '').toLowerCase();
    badge.classList.remove('active','inactive','draft');
    if (s === 'active')      badge.classList.add('active');
    else if (s === 'inactive') badge.classList.add('inactive');
    else                     badge.classList.add('draft');
    badge.textContent = s.charAt(0).toUpperCase() + s.slice(1);
    badge.dataset.val = s;
  }

  // Toggle tombol EDIT: inactive -> disable, lainnya -> enable
  function toggleEdit(row, status) {
    const s = (status || '').toLowerCase();
    const btnEdit = row.querySelector('.btn-edit');
    if (!btnEdit) return;

    const disable = (s === 'inactive');
    if (btnEdit.tagName === 'A') {
      if (disable) {
        if (!btnEdit.dataset.href) btnEdit.dataset.href = btnEdit.getAttribute('href') || '';
        btnEdit.removeAttribute('href');
        btnEdit.setAttribute('aria-disabled', 'true');
        btnEdit.classList.add('disabled');
      } else {
        if (btnEdit.dataset.href) btnEdit.setAttribute('href', btnEdit.dataset.href);
        btnEdit.removeAttribute('aria-disabled');
        btnEdit.classList.remove('disabled');
      }
    } else {
      btnEdit.disabled = disable;
      btnEdit.classList.toggle('disabled', disable);
    }
  }

  // Toggle tombol UPLOAD:
  // - Jika status 'draft' => harus jadi <a href=".../upload/{id}">Upload</a> aktif
  // - Selain draft => harus disabled (boleh <button disabled> atau <a disabled tanpa href>)
  function toggleUpload(row, status, idKuis) {
    const s = (status || '').toLowerCase();
    let el = row.querySelector('.btn-upload');
    if (!el) return;

    if (s === 'draft') {
      // jika masih <button disabled>, ganti ke <a>
      if (el.tagName !== 'A') {
        const a = document.createElement('a');
        a.className = 'btn btn-blue btn-sm btn-upload';
        a.href = `${UPLOAD_BASE}/${idKuis}`;
        a.textContent = 'Upload';
        a.onclick = function(){ return confirm('Yakin upload kuis ini?'); };
        el.replaceWith(a);
        el = a;
      }
      // pastikan aktif
      el.classList.remove('disabled');
      el.setAttribute('href', `${UPLOAD_BASE}/${idKuis}`);
      el.removeAttribute('aria-disabled');
    } else {
      // jika <a>, nonaktifkan. Pilih: ubah jadi <button disabled> biar konsisten dengan tampilan awal
      if (el.tagName === 'A') {
        const btn = document.createElement('button');
        btn.className = 'btn btn-sm btn-upload';
        btn.textContent = 'Upload';
        btn.disabled = true;
        el.replaceWith(btn);
        el = btn;
      } else {
        el.disabled = true;
        el.classList.add('disabled');
      }
    }
  }

  async function tick() {
    try {
      const res  = await fetch(ENDPOINT, { cache:'no-store', headers:{'X-Requested-With':'XMLHttpRequest'} });
      if (!res.ok) return;
      const json = await res.json();
      if (!json.ok) return;

      (json.data || []).forEach(it => {
        const row = document.querySelector(`tr[data-kuis-id="${it.id_kuis}"]`);
        if (!row) return;

        // Patch kolom-kolom
        setText(row, '.col-nama',     it.nama_kuis);
        setText(row, '.col-topik',    it.topik);
        setText(row, '.col-tanggal',  it.tanggal);
        setText(row, '.col-mulai',    it.waktu_mulai);
        setText(row, '.col-selesai',  it.waktu_selesai);
        setText(row, '.col-nilai',    it.nilai_minimum);
        setText(row, '.col-batas',    it.batas_pengulangan);
        setText(row, '.col-kategori', it.kategori);

        // Status badge
        updateStatusBadge(row.querySelector('.badge-status'), it.status);

        // Toggle tombol
        toggleUpload(row, it.status, it.id_kuis);
        toggleEdit(row, it.status);
      });
    } catch (e) {
      // silent
    }
  }

  tick();
  setInterval(tick, POLL_MS);
});
</script>

<?= $this->endSection() ?>
