<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\SoalModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\HasilKuisModel;
use CodeIgniter\I18n\Time;
use Config\Database; // ✅ CI4 DB connection

/**
 * Controller: Agent
 * Deskripsi: Controller utama untuk alur kuis Agent (CI4).
 * Catatan: Logika asli dipertahankan; hanya perbaikan kompatibilitas CI4, DB, dan dokumentasi.
 */
class Agent extends BaseController
{
    protected $kuisModel;
    protected $soalModel;
    protected $userModel;
    protected $hasilModel;
    protected $db; // ✅ CI4 DB instance

    public function __construct()
    {
        // init models
        $this->kuisModel  = new KuisModel();
        $this->soalModel  = new SoalModel();
        $this->userModel  = new UserModel();
        $this->hasilModel = new HasilKuisModel();

        // ✅ penting di CI4: koneksi DB manual (mengganti "magic" $this->db ala CI3)
        $this->db = Database::connect();
    }

    // --------------------------------------------------
    // Fungsi: ensureAgent()
    // Deskripsi (Description):
    // Validasi sesi user & role (agent). Return redirect jika tidak valid.
    // --------------------------------------------------
    private function ensureAgent()
    {
        $session = session();
        $userId  = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // ⚠️ sesuai instruksi kamu: session kemungkinan hanya punya user_id
        // jika di masa depan kamu simpan 'role', bisa aktifkan validasi role berikut:
        $role = strtolower($session->get('role') ?? '');
        if ($role !== '' && $role !== 'agent') {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        return null;
    }

    /**
     * Buat attempt ketika masuk halaman soal (tetap sama).
     */
    private function startAttempt(array $kuis, int $userId): void
    {
        $kuisId = (int)($kuis['id_kuis'] ?? 0);
        if ($kuisId <= 0) return;

        $existing = $this->hasilModel
            ->where('id_user', $userId)
            ->where('id_kuis', $kuisId)
            ->where('status', 'in_progress')
            ->orderBy('started_at', 'DESC')
            ->first();
        if ($existing) return;

        $alreadyPerfect = $this->hasilModel
            ->where('id_user', $userId)
            ->where('id_kuis', $kuisId)
            ->where('total_skor', 100)
            ->countAllResults();
        if ($alreadyPerfect > 0) return;

        $attemptCount = $this->hasilModel
            ->where('id_user', $userId)
            ->where('id_kuis', $kuisId)
            ->countAllResults();

        $limit = (int)($kuis['batas_pengulangan'] ?? 0);
        if ($limit > 0 && $attemptCount >= $limit) return;

        $this->hasilModel->insert([
            'id_user'            => $userId,
            'id_kuis'            => $kuisId,
            'jumlah_soal'        => 0,
            'jawaban_benar'      => 0,
            'jawaban_salah'      => 0,
            'total_skor'         => 0,
            'tanggal_pengerjaan' => date('Y-m-d H:i:s'),
            'status'             => 'in_progress',
            'started_at'         => date('Y-m-d H:i:s'),
            'jumlah_pengerjaan'  => $attemptCount + 1,
        ]);
    }

    /**
     * Dashboard Agent
     */
    public function dashboard()
    {
        if ($resp = $this->ensureAgent()) return $resp;

        $userId = (int) session()->get('user_id');
        $user   = $this->userModel->getUserWithKategori($userId);
        $katId  = (int) session('kategori_agent_id');

        if (!$user) {
            session()->destroy();
            return redirect()->to('/auth/login')->with('error', 'User tidak ditemukan, silakan login ulang.');
        }

        $kuisList = $this->kuisModel->getAvailableKuisForAgentKategori($katId);

        $now = Time::now('Asia/Jakarta');

        foreach ($kuisList as &$k) {
            // waktu
            $startAt = $k['start_at'] ?? null;
            $endAt   = $k['end_at']   ?? null;
            if (!$startAt && !empty($k['tanggal']) && !empty($k['waktu_mulai'])) {
                $startAt = $k['tanggal'].' '.$k['waktu_mulai'];
            }
            if (!$endAt && !empty($k['tanggal']) && !empty($k['waktu_selesai'])) {
                $endAt = $k['tanggal'].' '.$k['waktu_selesai'];
            }
            $start = $startAt ? Time::parse($startAt, 'Asia/Jakarta') : null;
            $end   = $endAt   ? Time::parse($endAt,   'Asia/Jakarta') : null;

            // default status by time
            if (!$start || !$end)            $ui = 'Tidak Tersedia';
            elseif ($now < $start)           $ui = 'Belum Dibuka';
            elseif ($now > $end)             $ui = 'Sudah Selesai';
            else                             $ui = 'Mulai';

            $timeOk = ($start && $end) && ($now >= $start && $now <= $end);

            // ==== tambahan: batas pengulangan & nilai 100 ====
            $attemptUsed = $this->hasilModel
                ->where('id_user', $userId)
                ->where('id_kuis', (int)$k['id_kuis'])
                ->countAllResults();

            $hasPerfect = $this->hasilModel
                ->where('id_user', $userId)
                ->where('id_kuis', (int)$k['id_kuis'])
                ->where('total_skor', 100)
                ->countAllResults() > 0;

            $limit = (int)($k['batas_pengulangan'] ?? 0);

            $limitOk = ($limit === 0) || ($attemptUsed < $limit);
            if ($timeOk && !$limitOk) $ui = 'Jatah Habis';
            if ($timeOk && $hasPerfect) $ui = 'Nilai 100';

            $k['can_start'] = $timeOk && $limitOk && !$hasPerfect;
            $k['ui_status'] = $k['can_start'] ? 'Mulai' : $ui;
        }

        return view('agent/dashboard', [
            'user' => $user,
            'kuis' => $kuisList,
        ]);
    }

    /**
     * Endpoint AJAX status kuis (real-time)
     */
    public function statusKuis()
    {
        if ($resp = $this->ensureAgent()) return $resp;

        $katId    = (int) session('kategori_agent_id');
        $userId   = (int) session()->get('user_id');
        $kuisList = $this->kuisModel->getAvailableKuisForAgentKategori($katId);

        $now     = Time::now('Asia/Jakarta');
        $result  = [];

        foreach ($kuisList as $k) {
            // waktu
            $startAt = $k['start_at'] ?? (isset($k['tanggal'], $k['waktu_mulai']) ? ($k['tanggal'].' '.$k['waktu_mulai']) : null);
            $endAt   = $k['end_at']   ?? (isset($k['tanggal'], $k['waktu_selesai']) ? ($k['tanggal'].' '.$k['waktu_selesai']) : null);
            $start = $startAt ? Time::parse($startAt, 'Asia/Jakarta') : null;
            $end   = $endAt   ? Time::parse($endAt,   'Asia/Jakarta') : null;

            if (!$start || !$end)            $ui = 'Tidak Tersedia';
            elseif ($now < $start)           $ui = 'Belum Dibuka';
            elseif ($now > $end)             $ui = 'Sudah Selesai';
            else                             $ui = 'Mulai';

            $timeOk = ($start && $end) && ($now >= $start && $now <= $end);

            // batas & perfect
            $attemptUsed = $this->hasilModel
                ->where('id_user', $userId)
                ->where('id_kuis', (int)$k['id_kuis'])
                ->countAllResults();

            $hasPerfect = $this->hasilModel
                ->where('id_user', $userId)
                ->where('id_kuis', (int)$k['id_kuis'])
                ->where('total_skor', 100)
                ->countAllResults() > 0;

            $limit = (int)($k['batas_pengulangan'] ?? 0);

            $limitOk = ($limit === 0) || ($attemptUsed < $limit);
            if ($timeOk && !$limitOk) $ui = 'Jatah Habis';
            if ($timeOk && $hasPerfect) $ui = 'Nilai 100';

            $canStart = $timeOk && $limitOk && !$hasPerfect;

            $result[] = [
                'id_kuis'       => (int) ($k['id_kuis'] ?? 0),
                'nama_kuis'     => (string) ($k['nama_kuis'] ?? ''),
                'topik'         => (string) ($k['topik'] ?? ''),
                'tanggal'       => (string) ($k['tanggal'] ?? ''),
                'waktu_mulai'   => (string) ($k['waktu_mulai'] ?? ''),
                'waktu_selesai' => (string) ($k['waktu_selesai'] ?? ''),
                'start_at'      => $startAt,
                'end_at'        => $endAt,
                'can_start'     => $canStart,
                'ui_status'     => $canStart ? 'Mulai' : $ui,
            ];
        }

        return $this->response->setJSON([
            'ok'   => true,
            'data' => $result
        ]);
    }

    /**
     * ✅ Long-poll status kuis
     */
    public function statusKuisLP()
    {
        if ($resp = $this->ensureAgent()) return $resp;

        $katId    = (int) session('kategori_agent_id');
        $userId   = (int) session()->get('user_id');
        $lastSig  = (string) ($this->request->getGet('sig') ?? '');
        $timeoutS = 25;
        $sleepUs  = 500000;
        $deadline = microtime(true) + $timeoutS;

        $nowTZ = 'Asia/Jakarta';

        do {
            $kuisList = $this->kuisModel->getAvailableKuisForAgentKategori($katId);
            $now = Time::now($nowTZ);

            $result = [];
            foreach ($kuisList as $k) {
                $startAt = $k['start_at'] ?? (isset($k['tanggal'],$k['waktu_mulai']) ? ($k['tanggal'].' '.$k['waktu_mulai']) : null);
                $endAt   = $k['end_at']   ?? (isset($k['tanggal'],$k['waktu_selesai']) ? ($k['tanggal'].' '.$k['waktu_selesai']) : null);

                $start = $startAt ? Time::parse($startAt, $nowTZ) : null;
                $end   = $endAt   ? Time::parse($endAt,   $nowTZ) : null;

                if (!$start || !$end)         $ui = 'Tidak Tersedia';
                elseif ($now < $start)        $ui = 'Belum Dibuka';
                elseif ($now > $end)          $ui = 'Sudah Selesai';
                else                          $ui = 'Mulai';

                $timeOk = ($start && $end) && ($now >= $start && $now <= $end);

                $attemptUsed = $this->hasilModel
                    ->where('id_user', $userId)
                    ->where('id_kuis', (int)$k['id_kuis'])
                    ->countAllResults();

                $hasPerfect = $this->hasilModel
                    ->where('id_user', $userId)
                    ->where('id_kuis', (int)$k['id_kuis'])
                    ->where('total_skor', 100)
                    ->countAllResults() > 0;

                $limit = (int)($k['batas_pengulangan'] ?? 0);

                $limitOk = ($limit === 0) || ($attemptUsed < $limit);
                if ($timeOk && !$limitOk) $ui = 'Jatah Habis';
                if ($timeOk && $hasPerfect) $ui = 'Nilai 100';

                $canStart = $timeOk && $limitOk && !$hasPerfect;

                $result[] = [
                    'id_kuis'       => (int) ($k['id_kuis'] ?? 0),
                    'nama_kuis'     => (string) ($k['nama_kuis'] ?? ''),
                    'topik'         => (string) ($k['topik'] ?? ''),
                    'tanggal'       => (string) ($k['tanggal'] ?? ''),
                    'waktu_mulai'   => (string) ($k['waktu_mulai'] ?? ''),
                    'waktu_selesai' => (string) ($k['waktu_selesai'] ?? ''),
                    'start_at'      => $startAt,
                    'end_at'        => $endAt,
                    'can_start'     => $canStart,
                    'ui_status'     => $canStart ? 'Mulai' : $ui,
                ];
            }

            $sig = md5(json_encode($result));

            if ($sig !== $lastSig) {
                return $this->response->setJSON(['ok' => true, 'sig' => $sig, 'data' => $result]);
            }

            usleep($sleepUs);
        } while (microtime(true) < $deadline);

        return $this->response->setJSON(['ok' => true, 'sig' => $lastSig, 'data' => null]);
    }

    
   public function detailKuis($id_hasil)
{
    // Tambahkan ensureAgent dan cek ownership
    if ($resp = $this->ensureAgent()) return $resp;
    
    $userId = (int) session()->get('user_id');
    $id_hasil = (int) $id_hasil;
    
    if ($id_hasil <= 0) throw PageNotFoundException::forPageNotFound();

    // Ambil data hasil kuis + pastikan milik user yang login
    $hasil = $this->hasilModel
        ->where('id_hasil', $id_hasil)
        ->where('id_user', $userId)
        ->first();
        
    if (!$hasil) throw PageNotFoundException::forPageNotFound('Data hasil tidak ditemukan.');

    // Cek apakah kuis sudah berakhir
    $kuis = $this->kuisModel->find($hasil['id_kuis']);
    if (!$kuis) throw PageNotFoundException::forPageNotFound('Data kuis tidak ditemukan.');

    $endTime = trim(($kuis['tanggal'] ?? '') . ' ' . ($kuis['waktu_selesai'] ?? ''));
    $kuisEnded = $endTime && strtotime($endTime) < time();
    
    // Jika strict: hanya boleh lihat setelah kuis berakhir
    if (!$kuisEnded) {
        return redirect()->to('/agent/riwayat')->with('error', 'Kuis masih berlangsung');
    }

    // === PERBAIKAN: HANYA AMBIL DATA TERAKHIR (JUMLAH PENGERJAAN TERTINGGI) ===
    
    // Cari jumlah pengerjaan tertinggi untuk user ini di kuis ini
    $maxPengerjaan = $this->hasilModel
        ->where('id_kuis', $hasil['id_kuis'])
        ->where('id_user', $userId)
        ->selectMax('jumlah_pengerjaan')
        ->first();
    
    $maxPengerjaan = $maxPengerjaan['jumlah_pengerjaan'] ?? 1;
    
    // Ambil hasil dengan jumlah pengerjaan tertinggi
    $hasilTerakhir = $this->hasilModel
        ->where('id_kuis', $hasil['id_kuis'])
        ->where('id_user', $userId)
        ->where('jumlah_pengerjaan', $maxPengerjaan)
        ->first();
    
    // Update $hasil dengan data terakhir
    if ($hasilTerakhir) {
        $hasil = $hasilTerakhir;
        $id_hasil = $hasilTerakhir['id_hasil']; // Update id_hasil ke yang terakhir
    }

    // Ambil jawaban per soal untuk hasil terakhir
    $jawaban = $this->db->table('kuis_jawaban kj')
    ->select('kj.*, s.soal, s.pilihan_a, s.pilihan_b, s.pilihan_c, s.pilihan_d, s.pilihan_e')
    ->join('soal_kuis s', 's.id_soal = kj.id_soal')
    ->join('kuis_hasil kh', 'kh.id_hasil = kj.id_hasil')
    ->where('kj.id_hasil', $id_hasil)
    ->where('kh.id_user', $userId)
    ->orderBy('s.id_soal', 'ASC')
    ->get()
    ->getResultArray();


    // Alternatif jika masih ada duplikat (harusnya tidak perlu lagi)
    if (count($jawaban) > 0) {
        $filteredJawaban = [];
        $processedSoal = [];
        
        foreach ($jawaban as $item) {
            $idSoal = $item['id_soal'];
            if (!in_array($idSoal, $processedSoal)) {
                $filteredJawaban[] = $item;
                $processedSoal[] = $idSoal;
            }
        }
        $jawaban = $filteredJawaban;
    }

    $data = [
        'kuis' => $kuis,
        'hasil' => $hasil, // Sekarang berisi data terakhir
        'jawaban' => $jawaban,
        'kuisEnded' => $kuisEnded
    ];

    return view('agent/hasil_detail', $data);
}
    
    public function soal($id_kuis = null)
{
    if ($resp = $this->ensureAgent()) {
        return $resp;
    }

    $id_kuis = $id_kuis ?? $this->request->getGet('id');
    $id_kuis = (int) $id_kuis;

    if ($id_kuis <= 0) {
        throw PageNotFoundException::forPageNotFound('ID Kuis tidak valid');
    }

    $katId = (int) session('kategori_agent_id');
    $kuis = $this->kuisModel->getKuisByIdForAgent($id_kuis, $katId);
    if (!$kuis) {
        throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan atau bukan kategori Anda.');
    }

    $startAt = !empty($kuis['start_at']) ? $kuis['start_at']
             : (trim(($kuis['tanggal'] ?? '') . ' ' . ($kuis['waktu_mulai'] ?? '')) ?: null);
    $endAt   = !empty($kuis['end_at']) ? $kuis['end_at']
             : (trim(($kuis['tanggal'] ?? '') . ' ' . ($kuis['waktu_selesai'] ?? '')) ?: null);

    $now = date('Y-m-d H:i:s');
    $within = ($startAt && $endAt)
        ? (strtotime($now) >= strtotime($startAt) && strtotime($now) < strtotime($endAt))
        : false;

    if (!$within) {
        return redirect()->to('/agent/dashboard')->with('error', 'Kuis belum dibuka atau sudah berakhir.');
    }

    // Mulai attempt
    $this->startAttempt($kuis, (int)session()->get('user_id'));

    // Ambil semua soal
    $soalList = $this->soalModel->where('id_kuis', $id_kuis)->findAll() ?? [];

    // 🔀 Acak urutan pilihan di setiap soal
    foreach ($soalList as &$soal) {
        $pilihan = [
            'A' => $soal['pilihan_a'],
            'B' => $soal['pilihan_b'],
            'C' => $soal['pilihan_c'],
            'D' => $soal['pilihan_d'],
            'E' => $soal['pilihan_e'],
        ];

        // ambil key dan acak
        $keys = array_keys($pilihan);
        shuffle($keys);

        // buat ulang urutan pilihan acak
        $soal['pilihan_acak'] = [];
        foreach ($keys as $key) {
            $soal['pilihan_acak'][$key] = $pilihan[$key];
        }
    }

    $data = [
        'user'      => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
        'kuis'      => $kuis,
        'soalList'  => $soalList,
    ];

    return view('agent/soal', $data);
}

    
    public function kerjakan($id_kuis = null)
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $id_kuis = (int) $id_kuis;
        if ($id_kuis <= 0) {
            throw PageNotFoundException::forPageNotFound('ID Kuis tidak valid');
        }

        $katId = (int) session('kategori_agent_id');

        $kuis = $this->kuisModel->getKuisByIdForAgent($id_kuis, $katId);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan atau bukan kategori Anda.');
        }

        $startAt = !empty($kuis['start_at']) ? $kuis['start_at']
                 : (trim(($kuis['tanggal'] ?? '') . ' ' . ($kuis['waktu_mulai'] ?? '')) ?: null);
        $endAt   = !empty($kuis['end_at']) ? $kuis['end_at']
                 : (trim(($kuis['tanggal'] ?? '') . ' ' . ($kuis['waktu_selesai'] ?? '')) ?: null);

        $now = date('Y-m-d H:i:s');
        $within = ($startAt && $endAt)
            ? (strtotime($now) >= strtotime($startAt) && strtotime($now) < strtotime($endAt))
            : false;

        if (!$within) {
            return redirect()->to('/agent/dashboard')->with('error', 'Kuis belum dibuka atau sudah berakhir.');
        }

        $this->startAttempt($kuis, (int)session()->get('user_id'));

        $soalList = $this->soalModel->where('id_kuis', $id_kuis)->findAll() ?? [];

        return view('agent/kuis/kerjakan', [
            'title'    => 'Kerjakan Kuis',
            'kuis'     => $kuis,
            'soalList' => $soalList,
            'user'     => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
        ]);
    }

    // --------------------------------------------------
    // Fungsi: submitKuis()
    // Deskripsi (Description):
    // Menerima jawaban dari front-end (JSON), menyimpan jawaban per soal,
    // menghitung skor, dan menandai attempt sebagai 'finished'.
    // Catatan: Logika asli dipertahankan; ditambah inisialisasi $this->db agar insert jawaban aman.
    // --------------------------------------------------
public function submitKuis()
{
    if ($resp = $this->ensureAgent()) return $resp;

    $payload = $this->request->getJSON(true);
    $idKuis  = (int)($payload['id_kuis'] ?? 0);
    $answers = $payload['answers'] ?? [];

    log_message('debug', 'Answers received: ' . json_encode($answers));

    if ($idKuis <= 0 || !is_array($answers) || empty($answers)) {
        return $this->response->setStatusCode(400)
            ->setJSON(['ok' => false, 'msg' => 'Data tidak valid']);
    }

    $userId = (int) session()->get('user_id');
    $kuis = $this->kuisModel->find($idKuis);

    if (!$kuis) {
        return $this->response->setStatusCode(404)
            ->setJSON(['ok' => false, 'msg' => 'Kuis tidak ditemukan']);
    }

    // Ambil semua soal kuis
    $soalList = $this->soalModel->where('id_kuis', $idKuis)->findAll();
    $total = count($soalList);

    // Buat/hasil attempt
    $attempt = $this->hasilModel
        ->where('id_user', $userId)
        ->where('id_kuis', $idKuis)
        ->orderBy('started_at', 'DESC')
        ->first();

    if ($attempt && $attempt['status'] === 'in_progress') {
        $idHasil = (int)$attempt['id_hasil'];
    } else {
        // Hapus jawaban lama
        $this->db->table('kuis_jawaban')
            ->whereIn('id_hasil', function ($builder) use ($userId, $idKuis) {
                return $builder->select('id_hasil')
                    ->from('kuis_hasil')
                    ->where('id_user', $userId)
                    ->where('id_kuis', $idKuis);
            })
            ->delete();

        $this->hasilModel->insert([
            'id_user' => $userId,
            'id_kuis' => $idKuis,
            'jumlah_soal' => $total,
            'jawaban_benar' => 0,
            'jawaban_salah' => 0,
            'total_skor' => 0,
            'status' => 'in_progress',
            'started_at' => date('Y-m-d H:i:s'),
            'tanggal_pengerjaan' => date('Y-m-d H:i:s')
        ]);

        $idHasil = $this->hasilModel->getInsertID();
    }

    // ============================
    // FIX: Mapping answers ke soal IDs yang benar
    // ============================
    $benar = 0;
    $soalIds = array_column($soalList, 'id_soal');
    
    log_message('debug', 'Soal IDs from DB: ' . implode(', ', $soalIds));
    log_message('debug', 'Answer keys from frontend: ' . implode(', ', array_keys($answers)));

    // Normalisasi answers - handle kedua kemungkinan
    $answersMapped = [];
    $i = 0;
    
    foreach ($answers as $key => $val) {
        $intKey = (int)$key;
        
        // Coba mapping: jika key adalah index (1,2,3), map ke soal ID
        if ($intKey >= 1 && $intKey <= count($soalIds) && !in_array($intKey, $soalIds)) {
            // Key adalah index, map ke soal ID yang sesuai
            $mappedSoalId = $soalIds[$intKey - 1]; // -1 karena array mulai dari 0
            $answersMapped[$mappedSoalId] = trim($val);
            log_message('debug', 'Mapped index ' . $key . ' to soal ID ' . $mappedSoalId);
        } else {
            // Key sudah adalah soal ID
            $answersMapped[$intKey] = trim($val);
            log_message('debug', 'Using direct soal ID: ' . $key);
        }
    }

    foreach ($soalList as $row) {
        $idSoal = (int)$row['id_soal'];
        $userAnswer = $answersMapped[$idSoal] ?? '';
        $correctAnswer = trim($row['jawaban'] ?? '');

        $status = 'Salah';

        if (!empty($userAnswer)) {
            $normalizedUser = strtolower(preg_replace('/\s+/', ' ', trim($userAnswer)));
            $normalizedCorrect = strtolower(preg_replace('/\s+/', ' ', trim($correctAnswer)));

            if ($normalizedUser === $normalizedCorrect) {
                $status = 'Benar';
                $benar++;
            }
        }

        $this->db->table('kuis_jawaban')->insert([
            'id_hasil'      => $idHasil,
            'id_soal'       => $idSoal,
            'jawaban_user'  => $userAnswer,
            'jawaban_benar' => $correctAnswer,
            'status'        => $status
        ]);
    }

    $salah = $total - $benar;
    $skor  = $total > 0 ? (int) round(($benar / $total) * 100) : 0;

    $this->hasilModel->update($idHasil, [
        'jawaban_benar' => $benar,
        'jawaban_salah' => $salah,
        'total_skor'    => $skor,
        'status'        => 'finished',
        'finished_at'   => date('Y-m-d H:i:s')
    ]);

    return $this->response->setJSON([
        'ok' => true,
        'id_hasil' => $idHasil,
        'ringkas' => [
            'total' => $total,
            'benar' => $benar,
            'salah' => $salah,
            'skor'  => $skor
        ]
    ]);
}

    // --------------------------------------------------
    // Fungsi: riwayat()
    // Deskripsi:
    // Menampilkan daftar riwayat hasil kuis milik user login.
    // --------------------------------------------------
    public function riwayat()
    {
        $userId = session()->get('user_id'); // atau nanti kita pastikan lagi

        $data['riwayat'] = $this->hasilModel->getRiwayatByUser($userId);
        return view('agent/riwayat', $data);
    }


    public function hasil($idHasil = null)
{
    $userId = session()->get('user_id');

    // 🔒 Pastikan parameter ada
    if (!$idHasil || !is_numeric($idHasil)) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('ID hasil tidak valid atau tidak ditemukan.');
    }

    $db = \Config\Database::connect();

    // 🔍 Ambil hasil berdasarkan id_hasil & user
    $hasil = $db->table('kuis_hasil kh')
        ->select('kh.*, k.nama_kuis, k.topik')
        ->join('kuis k', 'k.id_kuis = kh.id_kuis')
        ->where('kh.id_user', $userId)
        ->where('kh.id_hasil', $idHasil)
        ->get()
        ->getRowArray();

    // ⚠️ Jika tidak ditemukan, kembalikan ke riwayat
    if (!$hasil) {
        return redirect()
            ->to(base_url('agent/riwayat'))
            ->with('error', 'Data hasil kuis tidak ditemukan atau tidak sesuai dengan akun Anda.');
    }

    // 🧠 Pastikan kolom penting tidak null agar tidak error di view
    $hasil['jumlah_soal'] = $hasil['jumlah_soal'] ?? 0;
    $hasil['total_skor'] = $hasil['total_skor'] ?? 0;

    // ✅ Kirim data ke view
    return view('agent/hasil', ['hasil' => $hasil]);
}


    
     public function ulangiQuiz($id_kuis = null)
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $id_kuis = $id_kuis ?? $this->request->getGet('id');
        $id_kuis = (int) $id_kuis;

        if ($id_kuis <= 0) {
            throw PageNotFoundException::forPageNotFound('ID Kuis tidak valid');
        }

        $katId = (int) session('kategori_agent_id');

        $kuis = $this->kuisModel->getKuisByIdForAgent($id_kuis, $katId);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan atau bukan kategori Anda.');
        }

        $startAt = !empty($kuis['start_at']) ? $kuis['start_at']
                 : (trim(($kuis['tanggal'] ?? '') . ' ' . ($kuis['waktu_mulai'] ?? '')) ?: null);
        $endAt   = !empty($kuis['end_at']) ? $kuis['end_at']
                 : (trim(($kuis['tanggal'] ?? '') . ' ' . ($kuis['waktu_selesai'] ?? '')) ?: null);

        $now = date('Y-m-d H:i:s');
        $within = ($startAt && $endAt)
            ? (strtotime($now) >= strtotime($startAt) && strtotime($now) < strtotime($endAt))
            : false;

        if (!$within) {
            return redirect()->to('/agent/dashboard')->with('error', 'Kuis belum dibuka atau sudah berakhir.');
        }

        $this->startAttempt($kuis, (int)session()->get('user_id'));

        $soalList = $this->soalModel->where('id_kuis', $id_kuis)->findAll() ?? [];

        $data = [
            'user'      => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
            'kuis'      => $kuis,
            'soalList'  => $soalList,
        ];

        return view('agent/soal', $data);
    }

}
