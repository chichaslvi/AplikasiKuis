<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\SoalModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\HasilKuisModel;
use CodeIgniter\I18n\Time; // ⬅️ ditambahkan

class Agent extends BaseController
{
    protected $kuisModel;
    protected $soalModel;
    protected $userModel;
    protected $hasilModel; // ➕ tambahkan di sini

    public function __construct()
    {
        $this->kuisModel  = new KuisModel();
        $this->soalModel  = new SoalModel();
        $this->userModel  = new UserModel();
        $this->hasilModel = new HasilKuisModel(); // ➕ inisialisasi di sini
    }

    /**
     * Pastikan user sudah login & role = agent.
     * Return RedirectResponse jika gagal, atau null jika valid.
     */
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
            // rakit datetime mulai/selesai
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

            $k['can_start'] = ($start && $end) && ($now >= $start && $now <= $end);

            if (!$start || !$end)            $k['ui_status'] = 'Tidak Tersedia';
            elseif ($now < $start)           $k['ui_status'] = 'Belum Dibuka';
            elseif ($now > $end)             $k['ui_status'] = 'Sudah Selesai';
            else                             $k['ui_status'] = 'Mulai';
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
        $kuisList = $this->kuisModel->getAvailableKuisForAgentKategori($katId);

        $now     = Time::now('Asia/Jakarta');
        $result  = [];

        foreach ($kuisList as $k) {
            // rakit datetime mulai/selesai
            $startAt = $k['start_at'] ?? (isset($k['tanggal'], $k['waktu_mulai']) ? ($k['tanggal'].' '.$k['waktu_mulai']) : null);
            $endAt   = $k['end_at']   ?? (isset($k['tanggal'], $k['waktu_selesai']) ? ($k['tanggal'].' '.$k['waktu_selesai']) : null);

            $start = $startAt ? Time::parse($startAt, 'Asia/Jakarta') : null;
            $end   = $endAt   ? Time::parse($endAt,   'Asia/Jakarta') : null;

            $canStart = ($start && $end) && ($now >= $start && $now <= $end);

            if (!$start || !$end)            $ui_status = 'Tidak Tersedia';
            elseif ($now < $start)           $ui_status = 'Belum Dibuka';
            elseif ($now > $end)             $ui_status = 'Sudah Selesai';
            else                             $ui_status = 'Mulai';

            // ➕ kirim detail minimal agar dashboard bisa render kartu baru tanpa refresh
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
                'ui_status'     => $ui_status,
            ];
        }

        return $this->response->setJSON([
            'ok'   => true,
            'data' => $result
        ]);
    }

    /**
     * ✅ Long-poll: push perubahan kuis ke dashboard agent tanpa refresh
     * Client mengirim ?sig=... (tanda versi terakhir). Server balas cepat jika ada perubahan.
     */
    public function statusKuisLP()
    {
        if ($resp = $this->ensureAgent()) return $resp;

        $katId    = (int) session('kategori_agent_id');
        $lastSig  = (string) ($this->request->getGet('sig') ?? '');
        $timeoutS = 25;          // durasi long-poll (detik)
        $sleepUs  = 500000;      // cek setiap 0.5s
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

                $canStart = ($start && $end) && ($now >= $start && $now <= $end);

                if (!$start || !$end)         $ui = 'Tidak Tersedia';
                elseif ($now < $start)        $ui = 'Belum Dibuka';
                elseif ($now > $end)          $ui = 'Sudah Selesai';
                else                          $ui = 'Mulai';

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
                    'ui_status'     => $ui,
                ];
            }

            // tanda versi payload
            $sig = md5(json_encode($result));

            // kalau ada perubahan → kirim segera
            if ($sig !== $lastSig) {
                return $this->response->setJSON(['ok' => true, 'sig' => $sig, 'data' => $result]);
            }

            usleep($sleepUs);
        } while (microtime(true) < $deadline);

        // tidak ada perubahan dalam window → balas kosong agar client langsung long-poll lagi
        return $this->response->setJSON(['ok' => true, 'sig' => $lastSig, 'data' => null]);
    }

    /**
     * Detail Kuis
     */
    public function detailKuis($id = null)
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $id = (int) $id;
        if ($id <= 0) {
            throw PageNotFoundException::forPageNotFound('ID Kuis tidak valid');
        }

        // 🧩 Ambil kategori agent dari session
        $katId = (int) session('kategori_agent_id');

        // ✅ Pastikan kuis hanya bisa diakses sesuai kategori
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

    /**
     * Daftar Soal Kuis
     */
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

        // 🧩 Ambil kategori agent dari session
        $katId = (int) session('kategori_agent_id');

        // ✅ Pastikan kuis hanya bisa diakses sesuai kategori
        $kuis = $this->kuisModel->getKuisByIdForAgent($id_kuis, $katId);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan atau bukan kategori Anda.');
        }

        // ==== LOCK BY WAKTU (tahan akses sebelum waktunya / sesudah berakhir) ====
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
        // ==== END LOCK ====

        $soalList = $this->soalModel->where('id_kuis', $id_kuis)->findAll() ?? [];

        $data = [
            'user'      => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
            'kuis'      => $kuis,
            'soalList'  => $soalList,
        ];

        return view('agent/soal', $data);
    }

    /**
     * Ulangi Kuis (pakai view yang sama dengan soal)
     * Selaras dengan route:
     * - /agent/ulangi-quiz/(:num)
     * - /ulangi-quiz/(:num)  [alias global]
     */
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

        // 🧩 Ambil kategori agent dari session
        $katId = (int) session('kategori_agent_id');

        // ✅ Pastikan kuis hanya bisa diakses sesuai kategori
        $kuis = $this->kuisModel->getKuisByIdForAgent($id_kuis, $katId);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan atau bukan kategori Anda.');
        }

        // ==== LOCK BY WAKTU (samakan dengan soal) ====
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
        // ==== END LOCK ====

        $soalList = $this->soalModel->where('id_kuis', $id_kuis)->findAll() ?? [];

        $data = [
            'user'      => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
            'kuis'      => $kuis,
            'soalList'  => $soalList,
        ];

        // pakai view yang sama
        return view('agent/soal', $data);
    }

    /**
     * Kerjakan Kuis (halaman pengerjaan)
     */
    public function kerjakan($id_kuis = null)
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $id_kuis = (int) $id_kuis;
        if ($id_kuis <= 0) {
            throw PageNotFoundException::forPageNotFound('ID Kuis tidak valid');
        }

        // 🧩 Ambil kategori agent dari session
        $katId = (int) session('kategori_agent_id');

        // ✅ Pastikan kuis hanya bisa diakses sesuai kategori
        $kuis = $this->kuisModel->getKuisByIdForAgent($id_kuis, $katId);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan atau bukan kategori Anda.');
        }

        // ==== LOCK BY WAKTU (tahan akses sebelum waktunya / sesudah berakhir) ====
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
        // ==== END LOCK ====

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

        // Terima JSON { id_kuis, answers: { "1":"A", "2":"C", ... } }
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

        // Ambil urutan soal seperti di view (findAll default by PK ASC)
        $soalList = $this->soalModel->where('id_kuis', $idKuis)->orderBy('id','ASC')->findAll();

        $benar = 0; $total = count($soalList);

        // Cocokkan jawaban user (huruf) dengan kunci (huruf/teks)
        $i = 1;
        foreach ($soalList as $row) {
            $userKey = $answers[(string)$i] ?? $answers[$i] ?? null; // "A"/"B"/...
            if (!$userKey) { $i++; continue; }

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
                    if ($val !== null && strcasecmp($kunciRaw, (string)$val) === 0) { $kunciKey = $k; break; }
                }
            }

            if ($kunciKey && $userKey === $kunciKey) $benar++;
            $i++;
        }

        $salah = $total - $benar;
        $skor  = $total > 0 ? round(($benar / $total) * 100) : 0;
        // Simpan / upsert (jika user submit 2x, overwrite)
        $existing = $this->hasilModel->where(['id_user'=>$userId,'id_kuis'=>$idKuis])->first();
        $data = [
            'id_user'        => $userId,
            'id_kuis'        => $idKuis,
            'jumlah_soal'    => $total,
            'jawaban_benar'  => $benar,
            'jawaban_salah'  => $salah,
            'total_skor'     => $skor,
            'finished_at'    => date('Y-m-d H:i:s'),
        ];
        if ($existing) {
            $this->hasilModel->update($existing['id'], $data);
        } else {
            $this->hasilModel->insert($data);
        }

        return $this->response->setJSON([
            'ok' => true,
            'ringkas' => [
                'total' => $total, 'benar' => $benar, 'salah' => $salah, 'skor' => $skor],
        ]);
    }
}
