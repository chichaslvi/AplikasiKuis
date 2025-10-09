<?= $this->extend('layouts/agent/main') ?>
<?= $this->section('content') ?>

<style>
.detail-section {
    padding: 60px 15px;
    background: #0070C0;
    min-height: 80vh;
}

.detail-card {
    background-color: rgba(255, 255, 255, 0.95);
    padding: 30px 20px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    max-width: 900px;
    margin: 0 auto;
}

.soal-card {
    text-align: left;
    margin-bottom: 25px;
    padding: 20px;
    border-radius: 12px;
    background-color: #f8f9fa;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    position: relative;
}

.soal-card .status {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 24px;
}

.status-benar {
    color: #28a745;
}

.status-salah {
    color: #dc3545;
}

.pilihan {
    margin-top: 8px;
    padding: 8px 12px;
    border-radius: 8px;
    background-color: #e9ecef;
}

.pilihan.user-benar {
    color: #155724;
    background-color: #d4edda;
    border: 1px solid #28a745;
    font-weight: 600;
}

.pilihan.user-salah {
    color: #721c24;
    background-color: #f8d7da;
    border: 1px solid #dc3545;
    font-weight: 600;
}

.pilihan.jawaban-benar {
    color: #155724;
    background-color: #d4edda;
    border: 1px solid #28a745;
    font-weight: 600;
}

.back-button {
    display: inline-block;
    margin-top: 25px;
    padding: 12px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    background: linear-gradient(135deg, #17a2b8, #117a8b);
    color: #fff;
    transition: all 0.3s ease;
}

.back-button:hover {
    background: linear-gradient(135deg, #117a8b, #17a2b8);
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.25);
}

.main-section h3 {
    font-size: 28px;
    font-weight: 700;
}

.main-section p {
    font-size: 16px;
    color: #ddd;
    margin-bottom: 30px;
}

.info-box {
    background: #e7f3ff;
    border-left: 4px solid #007bff;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.info-item:last-child {
    margin-bottom: 0;
}
</style>

<div class="main-section detail-section text-center">
    <h3 class="text-white mb-3"><?= esc($kuis['nama_kuis']) ?></h3>
    <p class="text-white mb-4"><?= esc($kuis['topik']) ?></p>

    <div class="detail-card">
        <!-- Info Ringkasan -->
        <div class="info-box">
            <div class="info-item">
                <span><strong>Total Soal:</strong></span>
                <span><?= $hasil['jumlah_soal'] ?></span>
            </div>
            <div class="info-item">
                <span><strong>Jawaban Benar:</strong></span>
                <span class="text-success"><?= $hasil['jawaban_benar'] ?></span>
            </div>
            <div class="info-item">
                <span><strong>Jawaban Salah:</strong></span>
                <span class="text-danger"><?= $hasil['jawaban_salah'] ?></span>
            </div>
            <div class="info-item">
                <span><strong>Total Skor:</strong></span>
                <span class="text-primary"><strong><?= $hasil['total_skor'] ?>%</strong></span>
            </div>
        </div>

        <!-- Daftar Soal -->
        <?php foreach ($jawaban as $key => $item): ?>
            <div class="soal-card">
                <div class="status <?= $item['status'] == 'Benar' ? 'status-benar' : 'status-salah' ?>">
                    <?= $item['status'] == 'Benar' ? '✓' : '✗' ?>
                </div>
                
                <div class="pertanyaan mb-3">
                    <strong><?= $key + 1 ?>. <?= esc($item['soal']) ?></strong>
                </div>

                <?php 
                $pilihan = ['a','b','c','d','e'];
                foreach($pilihan as $p):
                    $text = $item['pilihan_'.$p] ?? null;
                    if(!$text) continue;
                    
                    $huruf = strtoupper($p);
                    $class = 'pilihan';
                    
                    // Cek apakah ini jawaban user
                    $isUserAnswer = (strcasecmp($text, $item['jawaban_user']) === 0);
                    
                    // Cek apakah ini jawaban benar
                    $isCorrectAnswer = (strcasecmp($text, $item['jawaban_benar']) === 0);
                    
                    // Tentukan kelas CSS
                    if ($isUserAnswer && $isCorrectAnswer) {
                        $class = 'pilihan user-benar'; // User menjawab benar
                    } elseif ($isUserAnswer && !$isCorrectAnswer) {
                        $class = 'pilihan user-salah'; // User menjawab salah
                    } elseif (!$isUserAnswer && $isCorrectAnswer) {
                        $class = 'pilihan jawaban-benar'; // Jawaban benar (bukan pilihan user)
                    }
                ?>
                    <div class="<?= $class ?>">
                        <?= $huruf ?>. <?= esc($text) ?>
                        <?php if ($isCorrectAnswer): ?>
                            <small class="float-end text-success">✓ Jawaban Benar</small>
                        <?php elseif ($isUserAnswer): ?>
                            <small class="float-end text-danger">✗ Jawaban Anda</small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <!-- Info Jawaban -->
                <div class="mt-3 p-2 rounded" style="background: #f8f9fa; border: 1px dashed #ccc;">
                    <small class="text-muted">
                        <strong>Jawaban Anda:</strong> 
                        <span class="<?= $item['status'] == 'Benar' ? 'text-success' : 'text-danger' ?>">
                            "<?= esc($item['jawaban_user'] ?? '-') ?>"
                        </span>
                        | 
                        <strong>Jawaban Benar:</strong> 
                        <span class="text-success">"<?= esc($item['jawaban_benar'] ?? '-') ?>"</span>
                    </small>
                </div>
            </div>
        <?php endforeach; ?>

        <a href="<?= base_url('agent/riwayat') ?>" class="back-button">Kembali ke Riwayat</a>
    </div>
</div>

<?= $this->endSection() ?>