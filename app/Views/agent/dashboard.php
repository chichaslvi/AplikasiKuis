<?= $this->extend('layouts/agent/main') ?>
<?= $this->section('content') ?>

<div class="main-section">
  <div class="container">
    <div class="row g-4">

      <!-- Profil -->
      <div class="col-md-4">
        <h4 class="mb-3 text-white">Profil</h4>
        <div class="card-custom profile-card text-center">
          <img src="https://cdn-icons-png.flaticon.com/512/219/219969.png" 
               class="profile-img mb-3" alt="avatar">
          <div class="profile-info text-start">
              <p><b>Nama</b> <?= esc($user['nama']) ?></p>
              <p><b>NIK</b> <?= esc($user['nik']) ?></p>
              <p><b>Kategori</b> <?= esc($user['kategori_nama'] ?? '-') ?></p>
          </div>
        </div>
      </div>

      <!-- Daftar Kuis -->
      <div class="col-md-8">
        <h4 class="mb-3 text-white">Daftar Kuis</h4>

        <!-- wrapper agar bisa inject kartu baru -->
        <div id="kuis-list">
        <?php if (!empty($kuis)) : ?>
          <?php foreach ($kuis as $item) : ?>
            <?php
              $startAt = !empty($item['start_at'])
                  ? $item['start_at']
                  : (trim(($item['tanggal'] ?? '') . ' ' . ($item['waktu_mulai'] ?? '')) ?: null);

              $endAt = !empty($item['end_at'])
                  ? $item['end_at']
                  : (trim(($item['tanggal'] ?? '') . ' ' . ($item['waktu_selesai'] ?? '')) ?: null);

              $now      = date('Y-m-d H:i:s');
              $canStart = ($startAt && $endAt)
                  ? (strtotime($now) >= strtotime($startAt) && strtotime($now) < strtotime($endAt))
                  : false;
            ?>
            <div class="card-custom quiz-card mb-3" data-kuis-id="<?= $item['id_kuis'] ?>">
              <div class="quiz-item d-flex justify-content-between align-items-center">
                <div class="quiz-details">
                  <p><b><?= esc($item['nama_kuis']); ?></b></p>
                  <p>Topik : <?= esc($item['topik'] ?? '-'); ?></p>
                  <small class="text-muted">
                    <i class="bi bi-calendar-event me-1"></i> 
                    <?= date('l, d F Y', strtotime($item['tanggal'])); ?> &nbsp;&nbsp;
                    <i class="bi bi-clock me-1"></i> 
                    <?= date('H:i', strtotime($item['waktu_mulai'])); ?> - <?= date('H:i', strtotime($item['waktu_selesai'])); ?>
                  </small>
                </div>

                <?php if ($canStart): ?>
                  <a href="<?= base_url('agent/soal/'.$item['id_kuis']) ?>" 
                     class="btn btn-start status-btn" 
                     data-id="<?= $item['id_kuis'] ?>">
                    <i class="bi bi-play-circle me-1"></i> Mulai
                  </a>
                <?php else: ?>
                  <button class="btn btn-secondary status-btn" 
                          data-id="<?= $item['id_kuis'] ?>" 
                          disabled>
                    <?php
                      if ($startAt && strtotime($now) < strtotime($startAt)) {
                        echo 'Belum Dibuka';
                      } elseif ($endAt && strtotime($now) >= strtotime($endAt)) {
                        echo 'Sudah Selesai';
                      } else {
                        echo 'Tidak Tersedia';
                      }
                    ?>
                  </button>
                <?php endif; ?>

              </div>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <div class="alert alert-info" id="empty-info">Belum ada kuis tersedia.</div>
        <?php endif; ?>
        </div>
        <!-- /#kuis-list -->

      </div>

    </div>
  </div>
</div>

<script>
(function(){
  const ENDPOINT_LP   = '<?= base_url('agent/statusKuisLP') ?>'; // long-poll
  const ENDPOINT_SNAP = '<?= base_url('agent/statusKuis') ?>';   // snapshot (fallback)
  const listEl   = document.getElementById('kuis-list');
  const emptyEl  = document.getElementById('empty-info');

  // ========= Helper =========
  function esc(s){ return (s||'').toString()
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
    .replace(/'/g,'&#039;'); }
  function timeHHMM(t){ return (t||'').slice(0,5); }

  // ➕ cek limit habis (batas_pengulangan & attempt_count dari server)
  function limitExceeded(k){
    const limit = parseInt(k && k.batas_pengulangan != null ? k.batas_pengulangan : 0, 10);
    const count = parseInt(k && k.attempt_count != null ? k.attempt_count : 0, 10);
    const hasIP = !!(k && k.has_in_progress);
    return limit > 0 && count >= limit && !hasIP;
  }

  // ✅ Banner otomatis: hide jika ada .quiz-card, show kalau kosong
  function updateBannerVisibility(){
    if (!emptyEl) return;
    const hasCard = !!listEl.querySelector('.quiz-card');
    emptyEl.style.display = hasCard ? 'none' : '';
  }
  if (window.MutationObserver) {
    const mo = new MutationObserver(() => updateBannerVisibility());
    mo.observe(listEl, { childList: true });
  }
  updateBannerVisibility();

  // ========= View helpers =========
  function statusBtnHtml(k){
    // 🧠 hormati limit di UI, walau server sudah set can_start/ui_status
    if (limitExceeded(k)) {
      return `<button class="btn btn-secondary status-btn" data-id="${k.id_kuis}" disabled>Batas Habis</button>`;
    }
    if (k.can_start) {
      return `<a href="<?= base_url('agent/soal/') ?>${k.id_kuis}" class="btn btn-start status-btn" data-id="${k.id_kuis}">
                <i class="bi bi-play-circle me-1"></i> Mulai
              </a>`;
    }
    return `<button class="btn btn-secondary status-btn" data-id="${k.id_kuis}" disabled>${esc(k.ui_status)}</button>`;
  }

  function cardHtml(k){
    return `
      <div class="card-custom quiz-card mb-3" data-kuis-id="${k.id_kuis}">
        <div class="quiz-item d-flex justify-content-between align-items-center">
          <div class="quiz-details">
            <p><b>${esc(k.nama_kuis)}</b></p>
            <p>Topik : ${esc(k.topik || '-')}</p>
            <small class="text-muted">
              <i class="bi bi-calendar-event me-1"></i> ${esc(k.tanggal || '')}
              &nbsp;&nbsp;<i class="bi bi-clock me-1"></i> ${timeHHMM(k.waktu_mulai)} - ${timeHHMM(k.waktu_selesai)}
            </small>
          </div>
          ${statusBtnHtml(k)}
        </div>
      </div>`;
  }

  function upsertCard(k){
    const card = listEl.querySelector(`[data-kuis-id="${k.id_kuis}"]`);
    if (!card) {
      listEl.insertAdjacentHTML('afterbegin', cardHtml(k));
      updateBannerVisibility();
      return;
    }

    // Patch teks agar realtime
    const details = card.querySelector('.quiz-details');
    if (details) {
      const pTitle = details.querySelector('p b');
      if (pTitle) pTitle.textContent = k.nama_kuis || '';
      const ps = details.querySelectorAll('p');
      if (ps && ps[1]) ps[1].innerHTML = 'Topik : ' + esc(k.topik || '-');
      const small = details.querySelector('small');
      if (small) {
        small.innerHTML =
          `<i class="bi bi-calendar-event me-1"></i> ${esc(k.tanggal || '')}
           &nbsp;&nbsp;<i class="bi bi-clock me-1"></i> ${timeHHMM(k.waktu_mulai)} - ${timeHHMM(k.waktu_selesai)}`;
      }
    }

    // Toggle tombol (hormati limit)
    const btn = card.querySelector('.status-btn');
    const mustDisableByLimit = limitExceeded(k);

    if (btn) {
      if (mustDisableByLimit) {
        // paksa jadi disabled "Batas Habis"
        if (btn.tagName === 'A') {
          const b = document.createElement('button');
          b.className = 'btn btn-secondary status-btn';
          b.dataset.id = k.id_kuis;
          b.disabled = true;
          b.textContent = 'Batas Habis';
          btn.replaceWith(b);
        } else {
          btn.textContent = 'Batas Habis';
          btn.disabled = true;
        }
      } else if (k.can_start) {
        if (btn.tagName === 'BUTTON') {
          const a = document.createElement('a');
          a.href = '<?= base_url('agent/soal/') ?>' + k.id_kuis;
          a.className = 'btn btn-start status-btn';
          a.dataset.id = k.id_kuis;
          a.innerHTML = '<i class="bi bi-play-circle me-1"></i> Mulai';
          btn.replaceWith(a);
        } else {
          btn.setAttribute('href', '<?= base_url('agent/soal/') ?>' + k.id_kuis);
          btn.innerHTML = '<i class="bi bi-play-circle me-1"></i> Mulai';
          btn.classList.remove('disabled');
          btn.removeAttribute('aria-disabled');
        }
      } else {
        // non-start case: tampilkan status dari server
        const label = esc(k.ui_status);
        if (btn.tagName === 'A') {
          const b = document.createElement('button');
          b.className = 'btn btn-secondary status-btn';
          b.dataset.id = k.id_kuis;
          b.disabled = true;
          b.textContent = label;
          btn.replaceWith(b);
        } else {
          btn.textContent = label;
          btn.disabled = true;
        }
      }
    }

    updateBannerVisibility();
  }

  function applySnapshot(arr){
    if (!Array.isArray(arr)) return;
    arr.forEach(upsertCard);

    // Hapus kartu yang tidak ada lagi
    const validIds = new Set(arr.map(x => String(x.id_kuis)));
    listEl.querySelectorAll('[data-kuis-id]').forEach(el => {
      const id = el.getAttribute('data-kuis-id');
      if (!validIds.has(String(id))) el.remove();
    });

    updateBannerVisibility();
  }

  // ========= Long-poll =========
  let currentSig = '';       // akan diisi dari server
  let retryDelay = 1000;     // backoff awal

  async function longPoll() {
    const ctl = new AbortController();
    const killer = setTimeout(() => ctl.abort(), 28000);

    try {
      const res = await fetch(`${ENDPOINT_LP}?sig=${encodeURIComponent(currentSig)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-store',
        signal: ctl.signal
      });
      clearTimeout(killer);
      if (!res.ok) throw new Error('LP HTTP '+res.status);
      const json = await res.json();

      if (json && json.ok) {
        if (json.data) {
          applySnapshot(json.data);
        }
        if (json.sig) currentSig = json.sig;

        retryDelay = 1000;
        longPoll();
        return;
      }
      throw new Error('LP payload not ok');
    } catch (e) {
      clearTimeout(killer);
      try {
        const snap = await fetch(ENDPOINT_SNAP, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          cache: 'no-store'
        }).then(r => r.json()).catch(()=>null);
        if (snap && snap.ok && Array.isArray(snap.data)) {
          applySnapshot(snap.data);
        }
      } catch (_) { /* ignore */ }

      setTimeout(longPoll, retryDelay);
      retryDelay = Math.min(retryDelay * 2, 10000);
    }
  }

  // Start: biarkan currentSig kosong agar server langsung kirim snapshot pertama
  longPoll();
})();
</script>

<?= $this->endSection() ?>
