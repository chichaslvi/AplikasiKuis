<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\SoalModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\HasilKuisModel;


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
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $userId = (int) session()->get('user_id');
        $user   = $this->userModel->getUserWithKategori($userId);

        if (!$user) {
            session()->destroy();
            return redirect()->to('/auth/login')->with('error', 'User tidak ditemukan, silakan login ulang.');
        }

        $data = [
            'user' => $user,
            'kuis' => $this->kuisModel->getAvailableKuisForAgent(),
        ];

        return view('agent/dashboard', $data);
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

        $kuis = $this->kuisModel->getKuisByIdWithKategori($id);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound("Kuis dengan ID {$id} tidak ditemukan");
        }

        $data = [
            'user' => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
            'kuis' => $kuis
        ];

        return view('agent/detail_kuis', $data);
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

        $kuis = $this->kuisModel->getKuisByIdWithKategori($id_kuis);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan');
        }

        // === FIX: pakai $soalList dan kirim ke view dengan key 'soalList' ===
        $soalList = $this->soalModel->where('id_kuis', $id_kuis)->findAll() ?? [];

        $data = [
            'user'      => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
            'kuis'      => $kuis,
            'soalList'  => $soalList,   // dipakai oleh agent/soal.php
            // 'soal'    => $soalList,   // (opsional) kalau ada bagian view lain yang masih pakai $soal
        ];

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

        $kuis = $this->kuisModel->getKuisByIdWithKategori($id_kuis);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound('Kuis tidak ditemukan');
        }

        $soalList = $this->soalModel->where('id_kuis', $id_kuis)->findAll() ?? [];

        return view('agent/kuis/kerjakan', [
            'title'    => 'Kerjakan Kuis',
            'kuis'     => $kuis,
            'soalList' => $soalList, // sudah benar
            'user'     => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
        ]);
    }

    /**
     * Riwayat Kuis (dummy)
     */
    public function riwayat()
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $userId = (int) session()->get('user_id');

        $riwayatKuis = [
            [
                'id_kuis'   => 1,
                'nama_kuis' => 'Kuis A',
                'sub_soal'  => 'Kuis Peningkatan Mutu',
                'tanggal'   => 'Kamis, 25 Januari 2024',
                'waktu'     => '11:00 - 12:00',
            ],
            [
                'id_kuis'   => 2,
                'nama_kuis' => 'Kuis B',
                'sub_soal'  => 'Kuis Pengetahuan Produk',
                'tanggal'   => 'Senin, 10 Februari 2024',
                'waktu'     => '09:00 - 10:00',
            ]
        ];

        $data = [
            'user'        => $this->userModel->getUserWithKategori($userId),
            'riwayatKuis' => $riwayatKuis
        ];

        return view('agent/riwayat', $data);
    }

    /**
     * Hasil Kuis (dummy ringkas)
     */
    public function hasil($id_kuis = null)
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $id_kuis = (int) $id_kuis;
        if ($id_kuis <= 0) {
            throw PageNotFoundException::forPageNotFound('ID Kuis tidak valid');
        }

        $kuis = [
            'id_kuis'   => $id_kuis,
            'nama_kuis' => "Kuis Dummy {$id_kuis}",
            'topik'     => "Topik Dummy {$id_kuis}",
        ];

        $hasil = [
            'sisa_waktu'     => '00:10:00',
            'jumlah_soal'    => 10,
            'jawaban_benar'  => 8,
            'jawaban_salah'  => 2,
            'total_skor'     => 80,
        ];

        $data = [
            'title' => 'Hasil Kuis',
            'kuis'  => $kuis,
            'hasil' => $hasil
        ];

        return view('agent/hasil', $data);
    }

    /**
     * Detail Hasil Kuis (dummy per-soal)
     */
    public function detailHasil($id_kuis = null)
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $id_kuis = (int) $id_kuis;
        if ($id_kuis <= 0) {
            throw PageNotFoundException::forPageNotFound('ID Kuis tidak valid');
        }

        $kuis = [
            'id_kuis'   => $id_kuis,
            'nama_kuis' => 'Kuis A Agent Pertamina',
            'topik'     => 'Pengetahuan Produk Pertamina'
        ];

        $jawaban = [
            [
                'soal'           => 'Pertamina memiliki layanan digital ...?',
                'pilihan_a'      => 'MyPertamina',
                'pilihan_b'      => 'PertaminaGo',
                'pilihan_c'      => 'BBMOnline',
                'pilihan_d'      => 'FuelApp',
                'pilihan_e'      => 'PetrolNet',
                'pilihan_user'   => 'PertaminaGo',
                'jawaban_benar'  => 'MyPertamina',
                'status'         => 'Salah'
            ],
            [
                'soal'           => 'Apa warna khas yang digunakan Pertamina?',
                'pilihan_a'      => 'Biru, Hijau, Merah',
                'pilihan_b'      => 'Merah, Kuning, Hijau',
                'pilihan_c'      => 'Biru, Putih, Merah',
                'pilihan_d'      => 'Hijau, Biru, Merah',
                'pilihan_e'      => 'Merah, Biru, Kuning',
                'pilihan_user'   => 'Biru, Hijau, Merah',
                'jawaban_benar'  => 'Biru, Hijau, Merah',
                'status'         => 'Benar'
            ],
        ];

        $data = [
            'kuis'    => $kuis,
            'jawaban' => $jawaban
        ];

        return view('agent/hasil_detail', $data);
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
    $lulus = (int) ($skor >= (int)($kuis['nilai_minimum'] ?? 0));

    // Simpan / upsert (jika user submit 2x, overwrite)
    $existing = $this->hasilModel->where(['id_user'=>$userId,'id_kuis'=>$idKuis])->first();
    $data = [
        'id_user'        => $userId,
        'id_kuis'        => $idKuis,
        'jumlah_soal'    => $total,
        'jawaban_benar'  => $benar,
        'jawaban_salah'  => $salah,
        'total_skor'     => $skor,
        'lulus'          => $lulus,
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
            'total' => $total, 'benar' => $benar, 'salah' => $salah, 'skor' => $skor, 'lulus' => $lulus
        ],
    ]);
}



}
