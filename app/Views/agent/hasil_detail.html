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

.pilihan.user-benarr {
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

.pilihan.benar {
    color: #155724;
    background-color: #d4edda;
    border: 1px solid #28a745;
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
</style>

<div class="main-section detail-section text-center">
    <h3 class="text-white mb-3"><?= esc($kuis['nama_kuis']) ?></h3>
    <p class="text-white mb-4"><?= esc($kuis['topik']) ?></p>

    <div class="detail-card">
        <?php foreach ($jawaban as $key => $item): ?>
            <div class="soal-card">
                <div class="status <?= $item['status'] == 'Benar' ? 'status-benar' : 'status-salah' ?>">
                    <?= $item['status'] == 'Benar' ? '✓' : '✗' ?>
                </div>
                <div class="pertanyaan">
                    <strong><?= $key + 1 ?>. <?= esc($item['soal']) ?></strong>
                </div>

                <?php 
                $pilihan = ['a','b','c','d','e'];
                foreach($pilihan as $p):
                    $text = $item['pilihan_'.$p] ?? null;
                    if(!$text) continue;
                    $class = 'pilihan';

                    if($text == $item['pilihan_user']) {
                        $class = $item['status'] == 'Benar' ? 'pilihan user-benarr' : 'pilihan user-salah';
                    } elseif($item['status'] == 'Salah' && $text == $item['jawaban_benar']) {
                        $class = 'pilihan benar';
                    }
                ?>
                    <div class="<?= $class ?>"><?= strtoupper($p) ?>. <?= esc($text) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <a href="<?= base_url('agent/riwayat') ?>" class="back-button">Kembali ke Riwayat</a>
    </div>
</div>

<?= $this->endSection() ?>
