<?= $this->extend('layouts/reviewer/main') ?>
<?= $this->section('content') ?>
<style>
/* Wrapper konten biar ga ketiban header & sidebar */
.dashboard-wrapper {
  margin-left: 250px;       /* jarak dari sidebar */
  margin-top: 60px;         /* jarak dari navbar */
  padding: 20px 30px;
  min-height: calc(100vh - 60px);
  background: #f9f9f9;      /* biar beda dari sidebar */
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

/* biar label sejajar */
.profile-info .label {
  display: inline-block;
  width: 70px; 
}

/* Stats Section */
.stats h2 { 
  font-size: 18px; 
  margin: 15px 0; 
}

.stats-cards { 
  display: flex; 
  gap: 18px; 
  flex-wrap: wrap;   /* biar responsive */
}

.stat-card { 
  flex: 1 1 120px; 
  background: white; 
  border-radius: 10px; 
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  text-align: center;
  padding: 8px;
  transition: all 0.3s ease;
  cursor: pointer;
  min-width: 120px;
}

.stat-card:hover {
  transform: translateY(-6px) scale(1.05);
  box-shadow: 0 8px 18px rgba(0,0,0,0.25);
}

.stat-card .inner { 
  background: #0070C0; 
  color: white; 
  padding: 12px; 
  border-radius: 6px; 
}

.stat-card .number {
  font-size: 16px;
  font-weight: bold;
  border-bottom: 1px solid white;  
  margin-bottom: 5px;
  padding-bottom: 4px;
}

.stat-card .label {
  font-size: 13px;
  font-weight: 500;
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

  <!-- Statistik Sistem -->
  <div class="stats">
    <h2>Statistik Sistem</h2>
    <div class="stats-cards">
      <div class="stat-card">
        <div class="inner">
          <div class="number"><?= $countAdmin ?></div>
          <div class="label">Admin</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="inner">
          <div class="number"><?= $countReviewer ?></div>
          <div class="label">Reviewer</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="inner">
          <div class="number"><?= $countAgent ?></div>
          <div class="label">Agent</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="inner">
          <div class="number"><?= $countKuis ?></div>
          <div class="label">Kuis</div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
