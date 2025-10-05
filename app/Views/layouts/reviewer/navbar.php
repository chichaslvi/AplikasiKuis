<nav class="navbar">
  <div class="navbar-left">
    <!-- isi kiri navbar kalau ada -->
  </div>

  <div class="navbar-right">
    <span class="navbar-user">Hi, <?= session()->get('nama') ?? 'User' ?></span>
  </div>
</nav>

