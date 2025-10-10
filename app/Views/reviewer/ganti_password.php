<?= $this->extend('layouts/reviewer/main') ?>
<?= $this->section('content') ?>
<style>
.container {
    margin-top: 30px;
    margin-bottom: 30px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
}

.card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 112, 192, 0.2);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    background: white;
    width: 100%;
    max-width: 800px;
}

.card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 45px rgba(0, 112, 192, 0.3);
}

.card-header {
    background: linear-gradient(135deg, #0070C0 0%, #4a9fe4 50%, #0070C0 100%);
    color: white;
    border-bottom: none;
    padding: 30px 50px;
    position: relative;
    text-align: center;
}

.card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
    background-size: cover;
}

.card-title {
    font-weight: 700;
    font-size: 28px;
    margin: 0;
    text-align: center;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.card-body {
    padding: 40px 60px;
    background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
}

.alert {
    border: none;
    border-radius: 12px;
    padding: 18px 22px;
    margin-bottom: 30px;
    font-weight: 500;
    position: relative;
    overflow: hidden;
    font-size: 15px;
}

.alert::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    background: currentColor;
}

.alert-danger {
    background: linear-gradient(135deg, #fff5f5, #ffe6e6);
    color: #d63031;
    border-left: 4px solid #d63031;
}

.alert-success {
    background: linear-gradient(135deg, #f0fff4, #e6ffe6);
    color: #00b894;
    border-left: 4px solid #00b894;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 12px;
    font-size: 16px;
    display: flex;
    align-items: center;
}

.form-label::before {
    content: '🔒';
    margin-right: 10px;
    font-size: 14px;
}

/* Password Input Container */
.password-input-container {
    position: relative;
    width: 100%;
}

.form-control {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 50px 16px 20px; /* Tambah padding kanan untuk icon */
    font-size: 16px;
    transition: all 0.3s ease;
    background: #ffffff;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    height: 55px;
    width: 100%;
}

.form-control:focus {
    border-color: #0070C0;
    background: white;
    box-shadow: 0 0 0 4px rgba(0, 112, 192, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
}

/* Show/Hide Password Toggle */
.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #718096;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.3s ease;
    z-index: 2;
}

.toggle-password:hover {
    background: #f7fafc;
    color: #0070C0;
    transform: translateY(-50%) scale(1.1);
}

.toggle-password i {
    font-size: 16px;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-text {
    font-size: 13px;
    color: #718096;
    margin-top: 8px;
    font-style: italic;
    padding-left: 25px;
}

/* Button Container - Side by Side */
.button-container {
    display: flex;
    gap: 20px;
    margin-top: 30px;
    justify-content: center;
}

.btn {
    border-radius: 12px;
    padding: 16px 32px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    position: relative;
    overflow: hidden;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    height: 55px;
    flex: 1;
    max-width: 250px;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transition: all 0.6s ease;
    transform: translate(-50%, -50%);
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn-primary {
    background: linear-gradient(135deg, #0070C0, #4a9fe4);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 112, 192, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #005a99, #0070C0);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 112, 192, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #718096, #a0aec0);
    color: white;
    box-shadow: 0 4px 15px rgba(113, 128, 150, 0.3);
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #4a5568, #718096);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(113, 128, 150, 0.4);
}

.mb-3 {
    margin-bottom: 32px !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        margin-top: 20px;
        margin-bottom: 20px;
        padding: 0 15px;
    }
    
    .card-body {
        padding: 35px 30px;
    }
    
    .card-header {
        padding: 25px 30px;
    }
    
    .card-title {
        font-size: 24px;
    }
    
    .card {
        max-width: 100%;
    }
    
    .btn {
        padding: 14px 28px;
        font-size: 15px;
        height: 50px;
        max-width: 200px;
    }
    
    .form-control {
        height: 50px;
        padding: 14px 45px 14px 16px;
    }
    
    .button-container {
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
    
    .toggle-password {
        right: 12px;
        padding: 6px;
    }
}

/* Floating animation */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.card {
    animation: float 6s ease-in-out infinite;
}

/* Form layout */
.form-grid {
    display: grid;
    gap: 25px;
}

.form-group {
    margin-bottom: 30px;
}
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title mb-0">🔐 Ganti Password</h2>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('reviewer/ganti_password/update') ?>" method="post" id="passwordForm">
                        <?= csrf_field() ?>
                        
                        <div class="form-grid">
                            <div class="mb-3 form-group">
                                <label for="current_password" class="form-label">Password Lama</label>
                                <div class="password-input-container">
                                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    <button type="button" class="toggle-password" data-target="current_password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Masukkan password lama Anda</div>
                            </div>
                            
                            <div class="mb-3 form-group">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <div class="password-input-container">
                                    <input type="password" name="new_password" id="new_password" class="form-control" required minlength="8">
                                    <button type="button" class="toggle-password" data-target="new_password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Password minimal 8 karakter</div>
                            </div>
                            
                            <div class="mb-3 form-group">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <div class="password-input-container">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="8">
                                    <button type="button" class="toggle-password" data-target="confirm_password">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Ulangi password baru Anda</div>
                            </div>
                        </div>
                        
                        <div class="button-container">
                            <button type="submit" class="btn btn-primary">
                                <span>🔄</span>
                                <span>Update Password</span>
                            </button>
                            <a href="<?= site_url('reviewer/dashboard') ?>" class="btn btn-secondary">
                                <span>🏠</span>
                                <span>Kembali ke Dashboard</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show/Hide Password Functionality
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const passwordInput = document.getElementById(targetId);
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    });
});

// Form Validation
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword.length < 8) {
        e.preventDefault();
        alert('Password baru harus minimal 8 karakter');
        return false;
    }
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Konfirmasi password tidak sama dengan password baru');
        return false;
    }
});

// Add some interactive effects
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
    });
});
</script>

<?= $this->endSection() ?>