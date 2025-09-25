<?= $this->extend('layouts/reviewer/main') ?>
<?= $this->section('content') ?>
<style>
/* Wrapper konten biar ga ketiban header & sidebar */
.dashboard-wrapper {
  margin-left: 250px;
  margin-top: 60px;
  padding: 20px 30px;
  min-height: calc(100vh - 60px);
  background: #f9f9f9;
}

/* Profile Card */
.profile-card { 
  background: white; 
  border-radius: 12px; 
  padding: 16px 24px; 
  display: flex; 
  align-items: center;        
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  margin-bottom: 30px; 
  border-top: 6px solid #0070C0; 
  width: 100%;
  max-width: 500px;   
}

.profile-card img { 
  width: 85px;
  height: 85px; 
  border-radius: 50%; 
  margin-right: 25px; 
}

.profile-info p { 
  margin: 10px 0; 
  font-size: 14px; 
  line-height: 1.5; 
  padding-bottom: 5px; 
  border-bottom: 1px solid #ddd; 
}
.profile-info p:last-child {
  border-bottom: none;            
}

.profile-info .label {
  display: inline-block;
  width: 70px; 
}

/* Notifikasi Section */
.notif-box {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  max-width: 700px;
}

.notif-box h2 {
  font-size: 18px;
  margin-bottom: 15px;
}

.notif-item {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  padding: 12px;
  border-bottom: 1px solid #eee;
  transition: background 0.2s;
}

.notif-item:last-child {
  border-bottom: none;
}

.notif-item:hover {
  background: #f5f9ff;
}

.notif-icon {
  font-size: 22px;
  color: #0070C0;
}

.notif-content {
  flex: 1;
}

.notif-title {
  font-weight: 600;
  margin: 0;
  font-size: 14px;
}

.notif-time {
  font-size: 12px;
  color: #777;
  margin-top: 4px;
}
</style>

<div class="dashboard-wrapper">
  <!-- Profile Card -->
  <div class="profile-card">
    <img src="https://cdn-icons-png.flaticon.com/512/6997/6997662.png" alt="Avatar">
    <div class="profile-info">
      <p><b class="label">Nama</b> <?= session()->get('nama') ?></p>
      <p><b class="label">NIK</b> <?= session()->get('nik') ?></p>
    </div>
  </div>

  <!-- Notifikasi -->
  <div class="notif-box">
    <h2>Notifikasi Terbaru</h2>

    <?php if (!empty($notifikasi)): ?>
      <?php foreach ($notifikasi as $n): ?>
        <div class="notif-item">
          <div class="notif-icon">🔔</div>
          <div class="notif-content">
            <p class="notif-title"><?= esc($n['judul']) ?></p>
            <p class="notif-time"><?= date('d M Y H:i', strtotime($n['created_at'])) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Tidak ada notifikasi terbaru.</p>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>
