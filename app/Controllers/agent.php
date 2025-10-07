<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\SoalModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\HasilKuisModel;
use CodeIgniter\I18n\Time;

class Agent extends BaseController
{
    protected $kuisModel;
    protected $soalModel;
    protected $userModel;
    protected $hasilModel;

    public function __construct()
    {
        $this->kuisModel  = new KuisModel();
        $this->soalModel  = new SoalModel();
        $this->userModel  = new UserModel();
        $this->hasilModel = new HasilKuisModel();
    }

    private function ensureAgent()
    {
        $session = session();
        $userId  = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $role = strtolower($session->get('role') ?? '');
        if ($role !== 'agent') {
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

    // ======= bagian lain TIDAK DIUBAH =======

    public function detailKuis($id = null)
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $id = (int) $id;
        if ($id <= 0) {
            throw PageNotFoundException::forPageNotFound('ID Kuis tidak valid');
        }

        $katId = (int) session('kategori_agent_id');

        $kuis = $this->kuisModel->getKuisByIdForAgent($id, $katId);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound("Kuis tidak ditemukan atau bukan kategori Anda.");
        }

        $data = [
            'user' => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
            'kuis' => $kuis
        ];

        return view('agent/detailKuis', $data);
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

        $this->startAttempt($kuis, (int)session()->get('user_id'));

        $soalList = $this->soalModel->where('id_kuis', $id_kuis)->findAll() ?? [];

        $data = [
            'user'      => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
            'kuis'      => $kuis,
            'soalList'  => $soalList,
        ];

        return view('agent/soal', $data);
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

    public function submitKuis()
    {
        if ($resp = $this->ensureAgent()) return $resp;

        // === LOG untuk memastikan request masuk ===
        log_message('info', '[submitKuis] HIT user_id={uid}', ['uid' => (int)session()->get('user_id')]);

        $payload = $this->request->getJSON(true);
        $idKuis  = (int)($payload['id_kuis'] ?? 0);
        $answers = $payload['answers'] ?? [];

        if ($idKuis <= 0 || !is_array($answers)) {
            return $this->response->setStatusCode(400)->setJSON(['ok'=>false,'msg'=>'Data tidak valid']);
        }

        $userId = (int) session()->get('user_id');
        $kuis   = $this->kuisModel->select('id_kuis, nilai_minimum, topik, nama_kuis')->find($idKuis);
        if (!$kuis) {
            return $this->response->setStatusCode(404)->setJSON(['ok'=>false,'msg'=>'Kuis tidak ditemukan']);
        }

        $soalList = $this->soalModel
            ->where('id_kuis', $idKuis)
            ->orderBy($this->soalModel->primaryKey, 'ASC')
            ->findAll();

        $benar = 0;
        $total = count($soalList);

        $i = 1;
        foreach ($soalList as $row) {
            $userKey = $answers[(string)$i] ?? $answers[$i] ?? null;
            if ($userKey) {
                $opt = [
                    'A' => $row['pilihan_a'] ?? null,
                    'B' => $row['pilihan_b'] ?? null,
                    'C' => $row['pilihan_c'] ?? null,
                    'D' => $row['pilihan_d'] ?? null,
                    'E' => $row['pilihan_e'] ?? null,
                ];
                $kunciRaw = trim((string)($row['jawaban'] ?? ''));
                $kunciKey = null;
                if (in_array($kunciRaw, ['A','B','C','D','E'], true)) {
                    $kunciKey = $kunciRaw;
                } else {
                    foreach ($opt as $k => $val) {
                        if ($val !== null && strcasecmp($kunciRaw, (string)$val) === 0) {
                            $kunciKey = $k;
                            break;
                        }
                    }
                }
                if ($kunciKey && $userKey === $kunciKey) $benar++;
            }
            $i++;
        }

        $salah = $total - $benar;
        $skor  = $total > 0 ? (int) round(($benar / $total) * 100) : 0;

        $attempt = $this->hasilModel
            ->where('id_user', $userId)
            ->where('id_kuis', $idKuis)
            ->where('status', 'in_progress')
            ->orderBy('started_at', 'DESC')
            ->first();

        $dataUpdate = [
            'jumlah_soal'        => $total,
            'jawaban_benar'      => $benar,
            'jawaban_salah'      => $salah,
            'total_skor'         => $skor,
            'status'             => 'finished',
            'finished_at'        => date('Y-m-d H:i:s'),
        ];

        if ($attempt) {
            $ok = $this->hasilModel->update((int)$attempt['id_hasil'], $dataUpdate);
            log_message('info', '[submitKuis] UPDATE attempt={id} ok={ok}', [
                'id' => (int)$attempt['id_hasil'],
                'ok' => $ok ? 1 : 0
            ]);
        } else {
            $dataInsert = [
                'id_user'            => $userId,
                'id_kuis'            => $idKuis,
                'jumlah_soal'        => $total,
                'jawaban_benar'      => $benar,
                'jawaban_salah'      => $salah,
                'total_skor'         => $skor,
                'tanggal_pengerjaan' => date('Y-m-d H:i:s'),
                'status'             => 'finished',
                'finished_at'        => date('Y-m-d H:i:s'),
            ];
            $newId = $this->hasilModel->insert($dataInsert, true);
            log_message('info', '[submitKuis] INSERT id_hasil={id}', ['id' => (int)$newId]);
        }

        $payloadResp = [
            'ok' => true,
            'ringkas' => [
                'total' => $total,
                'benar' => $benar,
                'salah' => $salah,
                'skor'  => $skor
            ],
        ];

        // (opsional) kirim token baru bila CI4 meregenerasi token setiap request
        $hdrName = function_exists('csrf_header') ? csrf_header() : 'X-CSRF-TOKEN';
        $hdrHash = function_exists('csrf_hash')   ? csrf_hash()   : '';

        return $this->response
            ->setHeader('X-CSRF-HEADER', $hdrName)
            ->setHeader('X-CSRF-TOKEN',  $hdrHash)
            ->setJSON($payloadResp);
    }
    public function riwayat()
{
    $userId = session()->get('user_id'); // atau nanti kita pastikan lagi

    $data['riwayat'] = $this->hasilModel->getRiwayatByUser($userId);
    return view('agent/riwayat', $data);
}


    // halaman hasil kuis
    public function hasil($idKuis)
{
    $userId = session()->get('user_id');
    $db = \Config\Database::connect();

    $hasil = $db->table('kuis_hasil kh')
        ->select('kh.*, k.nama_kuis, k.topik')
        ->join('kuis k', 'k.id_kuis = kh.id_kuis')
        ->where('kh.id_user', $userId)
        ->where('kh.id_kuis', $idKuis)
        ->get()
        ->getRowArray();

    $data['hasil'] = $hasil; // ✅ ubah ke 'hasil'

    return view('agent/hasil', $data);
}
}
