<?= $this->extend('layouts/admin/main') ?>

<?= $this->section('content') ?>
<style>
/* Profile Card */
/* Profile Card */
/* Profile Card */
/* Profile Card */
/* Profile Card */
.profile-card { 
  background: white; 
  border-radius: 12px; 
  padding: 16px 24px; 
  display: flex; 
  align-items: flex-start;        
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);  /* ✅ shadow lebih halus */
  margin-bottom: 30px; 
  border-top: 6px solid #0070C0; 
  width: fit-content;
  max-width: 460px;   
  min-height: 150px;  
}


.profile-card img { 
  width: 85px;                    /* avatar diperkecil */
  height: 85px; 
  border-radius: 50%; 
  margin-right: 25px; 
}

.profile-info p { 
  margin: 14px 0; 
  font-size: 14px; 
  line-height: 1.7; 
  padding-bottom: 5px; 
  border-bottom: 1px solid #ddd; 
  width: 220px; 
}

.profile-info p:last-child {
  border-bottom: none;            
}

/* Stats Section */
.stats h2 { 
  font-size: 18px; 
  margin: 15px 0; 
}

.stats-cards { 
  display: flex; 
  gap: 18px; 
}

.stat-card { 
  flex: 1; 
  background: white; 
  border-radius: 10px; 
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);   /* ✅ shadow lebih tegas */
  text-align: center;
  width: 120px;    
  padding: 8px;
  transition: all 0.3s ease;                /* ✅ smooth animasi */
  cursor: pointer;                          /* ✅ kursor jadi pointer */
}

.stat-card:hover {
  transform: translateY(-6px) scale(1.05);  /* ✅ naik & sedikit membesar */
  box-shadow: 0 8px 18px rgba(0,0,0,0.25);  /* ✅ shadow makin dalam saat hover */
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
  padding-bottom: px;
  border-bottom: 1px solid white;  
  margin-bottom: 5px;
}

.stat-card .label {
  font-size: 13px;
  font-weight: 500;
}


</style>

<h1></h1>

<!-- Profile Card -->
<div class="profile-card">
  <img src="https://cdn-icons-png.flaticon.com/512/6997/6997662.png" alt="Avatar">
  <div class="profile-info">
    <p><b>Nama</b> &nbsp;&nbsp; Chicha Silvi Aulia</p>
    <p><b>NIK</b> &nbsp;&nbsp; 22576004</p>
  </div>
</div>

<!-- User Stats -->
<div class="stats">
  <h2>Jumlah Pengguna</h2>
  <div class="stats-cards">
    <div class="stat-card">
      <div class="inner">
        <div class="number">3</div>
        <div class="label">Admin</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="inner">
        <div class="number">18</div>
        <div class="label">Reviewer</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="inner">
        <div class="number">25</div>
        <div class="label">Agent</div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
