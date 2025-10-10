<?= $this->extend('layouts/reviewer/main') ?>

<?= $this->section('content') ?>
<style>
/* Profile Card */
.profile-card { 
  background: white; 
  border-radius: 16px; 
  padding: 20px 25px; 
  display: flex; 
  align-items: center;        
  box-shadow: 0 8px 25px rgba(0,112,192,0.15);
  margin-bottom: 30px; 
  border-top: 6px solid #0070C0;
  border-left: 1px solid #e3f2fd;
  border-right: 1px solid #e3f2fd;
  width: fit-content;
  max-width: 480px;   
  min-height: 160px;  
  margin-left: 0;
  position: relative;
  overflow: hidden;
}

.profile-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, rgba(0,112,192,0.1) 0%, transparent 50%);
  border-radius: 0 16px 0 0;
}

.profile-card img { 
  width: 90px;
  height: 90px; 
  border-radius: 50%; 
  margin-right: 25px;
  border: 3px solid #0070C0;
  padding: 3px;
  background: white;
  box-shadow: 0 4px 12px rgba(0,112,192,0.2);
}

.profile-info p { 
  margin: 16px 0; 
  font-size: 15px; 
  line-height: 1.7; 
  padding-bottom: 8px; 
  border-bottom: 1px solid #e0e0e0; 
  width: 240px; 
  position: relative;
}
.profile-info p:last-child {
  border-bottom: none;            
}

.profile-info p::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 0;
  height: 1px;
  background: #0070C0;
  transition: width 0.3s ease;
}

.profile-info p:hover::after {
  width: 100%;
}

/* biar label sejajar */
.profile-info .label {
  display: inline-block;
  width: 65px;
  color: #0070C0;
  font-weight: 600;
}

/* Main Content Grid */
.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 25px;
  align-items: start;
}

/* Section Styling */
.section {
  margin-top: 0;
}

.section h2 {
  font-size: 18px;
  margin-bottom: 15px;
  color: #2c3e50;
  font-weight: 600;
  padding-left: 10px;
  border-left: 4px solid #0070C0;
}

/* Welcome Card */
.welcome-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  box-shadow: 0 8px 25px rgba(102,126,234,0.3);
  padding: 30px;
  color: white;
  position: relative;
  overflow: hidden;
  height: fit-content;
}

.welcome-card::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 200px;
  height: 200px;
  background: rgba(255,255,255,0.1);
  border-radius: 50%;
}

.welcome-card::after {
  content: '';
  position: absolute;
  bottom: -30%;
  left: -10%;
  width: 150px;
  height: 150px;
  background: rgba(255,255,255,0.05);
  border-radius: 50%;
}

.welcome-title {
  font-size: 22px;
  margin-bottom: 15px;
  font-weight: 700;
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  gap: 10px;
}

.welcome-text {
  line-height: 1.7;
  font-size: 14px;
  opacity: 0.95;
  position: relative;
  z-index: 2;
}

/* Tips Card */
.tips-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  padding: 30px;
  position: relative;
  border: 1px solid #f0f0f0;
  height: fit-content;
}

.tips-title {
  font-size: 18px;
  color: #2c3e50;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
}

.tips-title span {
  font-size: 20px;
}

.tips-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.tips-list li {
  padding: 12px 0;
  border-bottom: 1px solid #f8f9fa;
  display: flex;
  align-items: flex-start;
  gap: 15px;
  transition: background 0.3s ease;
  border-radius: 8px;
  padding-left: 10px;
}

.tips-list li:hover {
  background: #f8f9fa;
}

.tips-list li:last-child {
  border-bottom: none;
}

.tip-icon {
  color: #0070C0;
  font-weight: bold;
  min-width: 24px;
  height: 24px;
  background: #e3f2fd;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  margin-top: 2px;
  flex-shrink: 0;
}

.tip-text {
  flex: 1;
  line-height: 1.5;
}

/* Content Wrapper */
.content-wrapper {
  margin-left: 0;
  padding-left: 0;
}

/* Responsive */
@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .profile-card {
    max-width: 100%;
  }
}
</style>

<div class="content-wrapper">
  <!-- Profile -->
  <div class="profile-card">
    <img src="https://cdn-icons-png.flaticon.com/512/6997/6997662.png" alt="Avatar">
    <div class="profile-info">
      <p><b class="label">Nama</b> <?= session()->get('nama') ?></p>
      <p><b class="label">NIK</b> <?= session()->get('nik') ?></p>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="content-grid">
    <!-- Welcome Message -->
    <div class="section">
      <h2>Selamat Datang</h2>
      <div class="welcome-card">
        <div class="welcome-title">
          </span> Selamat Datang Reviewer!
        </div>
        <div class="welcome-text">
          Anda login sebagai <strong>Reviewer Soal</strong>. Sistem ini mendukung Anda dalam 
          mengelola dan mereview kumpulan soal. Gunakan menu navigasi untuk mengupload soal 
          dan membuat laporan evaluasi.
        </div>
      </div>
    </div>

    <!-- Tips & Panduan Reviewer Soal -->
    <div class="section">
      <h2>Panduan Kerja</h2>
      <div class="tips-card">
        <div class="tips-title">
          </span> Panduan Reviewer Soal
        </div>
        <ul class="tips-list">
          <li>
            <span class="tip-icon">1</span>
            <span class="tip-text">Upload soal dengan format yang telah ditentukan (PDF/DOC)</span>
          </li>
          <li>
            <span class="tip-icon">2</span>
            <span class="tip-text">Pastikan soal sudah melalui proses quality control sebelum diupload</span>
          </li>
          <li>
            <span class="tip-icon">3</span>
            <span class="tip-text">Verifikasi kesesuaian soal dengan kisi-kisi dan kompetensi</span>
          </li>
          <li>
            <span class="tip-icon">4</span>
            <span class="tip-text">Buat laporan review soal secara berkala dan komprehensif</span>
          </li>
          <li>
            <span class="tip-icon">5</span>
            <span class="tip-text">Update status soal (approved/rejected) beserta catatan review</span>
          </li>
          <li>
            <span class="tip-icon">6</span>
            <span class="tip-text">Koordinasi dengan tim pembuat soal untuk perbaikan dan revisi</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>