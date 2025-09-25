<nav class="navbar">
  <div class="navbar-left">
    <!-- isi kiri navbar kalau ada -->
  </div>

  <div class="navbar-right">
    <span class="navbar-user">Hi, <?= session()->get('nama') ?? 'User' ?></span>
  </div>
</nav>

<style>
/* Navbar sama dengan admin */
.navbar {
  position: fixed;
  top: 0;
  left: 250px;                 /* mulai setelah sidebar */
  height: 60px;
  width: calc(100% - 250px);   /* sisa layar selain sidebar */
  background: #0070C0;
  color: white;
  display: flex;
  align-items: center;
  padding: 0 20px;             /* ini yang bikin teks ga mepet kanan */
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  z-index: 999;
}

.navbar-left {
  flex: 1;                     /* isi kiri ambil space */
  display: flex;
  align-items: center;
}

.navbar-right {
  display: flex;
  align-items: center;
}

.navbar-user {
  font-size: 16px;
  font-weight: 500;
}
</style>
