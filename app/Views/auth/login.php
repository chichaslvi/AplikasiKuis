<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login | Melisa</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #fff;
    }

    .container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      width: 100%;
      max-width: 1100px;
      background: #fff;
    }

    .left {
      padding: 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-start;
      text-align: left;
    }

    .header-box {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      width: 160%;
      max-width: 700px;
      margin-bottom: 40px;
    }

    .left img.logo { 
      width: 180px; 
      margin-bottom: 50px; 
    }

    .left h3 { 
      font-weight: 400; 
      margin-bottom: 25px; 
      color: #333; 
      font-size: 15px; 
      text-align: center;  
    }

    form {
      display: flex;
      flex-direction: column;
      align-items: flex-end; 
      gap: 0;
      width: 100%;
      max-width: 320px;
      margin-left: 150px;    
    }

    .form-wrapper {
      width: 100%;
      overflow: visible;
    }

    .input-group {
      position: relative;
      width: 579px;     
      height: 62px;     
    }

    .input-group label {
      position: absolute;
      top: 8px;         
      left: 15px;
      font-size: 10.45px;
      color: #666;
      pointer-events: none;
      letter-spacing: 0.5px; 
    }

    .input-group input {
      width: 100%;
      height: 100%;
      padding: 28px 10px 10px 15px; 
      border: 1px solid #ccc;
      font-size: 10.45px;
      outline: none;
      transition: all 0.3s;
    }

    .input-group input:focus {
      border-left: 3px solid #007bff;
    }

    /* Placeholder style */
    .input-group input::placeholder {
      color: #aaa;
      font-size: 10.45px;
    }
 
    .input-group:first-child input {
      border-radius: 4px 4px 0 0;
    }
    .input-group:last-of-type input {
      border-radius: 0 0 4px 4px;
      border-top: none;
    }

    button {
      width: 70%;       
      padding: 12px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
      display: block;
      margin-left: auto; 
      margin-top: 20px;
    }

    button:hover {
      background: #0056b3;
    }

    .right {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }

    .right img {
      width: 100%;
      max-width: 900px;
    }

    .flash {
      margin-bottom: 10px;
      font-size: 14px;
    }
    .error { color: red; }
    .success { color: green; }

    @media (max-width: 768px) {
      .input-group {
        width: 100%; 
      }
      button {
        width: 100%;  
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Kiri -->
    <div class="left">
      <div class="header-box">
        <img src="assets/img/Logo.png" alt="Melisa Logo" class="logo">
        <h3>Welcome back!<br>Please login to your account.</h3>
      </div>

      <?php if(session()->getFlashdata('error')): ?>
        <p class="flash error"><?= session()->getFlashdata('error') ?></p>
      <?php endif; ?>
      <?php if(session()->getFlashdata('success')): ?>
        <p class="flash success"><?= session()->getFlashdata('success') ?></p>
      <?php endif; ?>

      <form action="/auth/doLogin" method="post">
        <div class="input-group">
          <label for="username">Username</label>
          <input type="text" name="username" id="username" placeholder="Masukkan username" required>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" placeholder="Masukkan password" required>
        </div>
        <button type="submit">LOGIN</button>
      </form>
    </div>

    <!-- Kanan -->
    <div class="right">
      <img src="assets/img/ilustrasi.png" alt="Ilustrasi Login">
    </div>
  </div>
</body>
</html>
