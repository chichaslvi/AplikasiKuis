<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\KuisHasilModel;
use App\Models\SoalModel;
use App\Models\KategoriAgentModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class KuisController extends BaseController
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function mulai(int $idKuis)
    {
        $session = session();

        $userId = (int) $session->get('id_user');
        if (!$userId) {
            return redirect()->back()->with('error', 'Silakan login terlebih dahulu.');
        }

        $kuisModel  = new KuisModel();
        $hasilModel = new KuisHasilModel();
        $db         = \Config\Database::connect();


        $kuis = $kuisModel->find($idKuis);
        if (!$kuis) {
            return redirect()->back()->with('error', 'Kuis tidak ditemukan.');
        }

       
        $now = date('Y-m-d H:i:s');
        if (!empty($kuis['start_at']) && $now < $kuis['start_at']) {
            return redirect()->back()->with('error', 'Kuis belum dimulai.');
        }
        if (!empty($kuis['end_at']) && $now >= $kuis['end_at']) {
            return redirect()->back()->with('error', 'Kuis sudah berakhir.');
        }
        if (isset($kuis['status']) && strtolower($kuis['status']) !== 'active') {
            return redirect()->back()->with('error', 'Kuis tidak aktif.');
        }

        
        $attemptUsed = $hasilModel->countUserAttempts($userId, $idKuis);
        $maxAttempts = (int) ($kuis['batas_pengulangan'] ?? 1);

        if ($attemptUsed >= $maxAttempts) {
            return redirect()->back()->with('error', 'Batas pengerjaan kuis sudah tercapai.');
        }


        try {
            $db->transStart();

            $attemptUsedTx = $hasilModel->where('id_user', $userId)
                                        ->where('id_kuis', $idKuis)
                                        ->countAllResults();

            if ($attemptUsedTx >= $maxAttempts) {
                $db->transComplete();
                return redirect()->back()->with('error', 'Batas pengerjaan kuis sudah tercapai.');
            }

            $hasilModel->insert([
                'id_user'           => $userId,
                'id_kuis'           => $idKuis,
                'status'            => 'in_progress',
                'started_at'        => $now,
                'tanggal_pengerjaan'=> date('Y-m-d'),
                'jumlah_pengerjaan' => $attemptUsedTx + 1,
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal memulai kuis. Coba lagi.');
            }

        } catch (\Throwable $e) {
            if ($db->transStatus() === true) {
                $db->transRollback();
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }


        return redirect()->to(base_url("kuis/soal/{$idKuis}"));
    }


    public function quota(int $idKuis)
    {
        $session  = session();
        $userId   = (int) $session->get('id_user');

        if (!$userId) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $kuisModel  = new KuisModel();
        $hasilModel = new KuisHasilModel();

        $kuis = $kuisModel->find($idKuis);
        if (!$kuis) {
            return $this->response->setJSON(['error' => 'Kuis tidak ditemukan'])->setStatusCode(404);
        }

        $used = $hasilModel->countUserAttempts($userId, $idKuis);
        $max  = (int) ($kuis['batas_pengulangan'] ?? 1);

        return $this->response->setJSON([
            'quiz_id'            => $idKuis,
            'max_attempts'       => $max,
            'used_attempts'      => $used,
            'remaining_attempts' => max(0, $max - $used),
        ]);
    }

    private function getLoggedInKategoriId(): ?int
    {
        $idKategori = (int) (session()->get('id_kategori') ?? 0);
        if ($idKategori > 0) {
            return $idKategori;
        }

        $idUser = (int) (session()->get('id_user') ?? 0);
        if ($idUser <= 0) {
            return null;
        }

        $db  = \Config\Database::connect();
        $row = $db->table('users')->select('id_kategori')->where('id_user', $idUser)->get()->getRow();
        return $row ? (int) $row->id_kategori : null;
    }


    private function autoDeactivate()
    {
        $db = \Config\Database::connect();

        
        $now = date('Y-m-d H:i:s');

        
        $builder = $db->table('kuis');
        $builder->set('status', 'inactive');
        $builder->where('status', 'active');
        $builder->where('end_at <=', $now);
        $builder->update();
    }

    public function pollStatus()
    {
        
        $this->autoDeactivate();

        $kuisModel = new \App\Models\KuisModel();
        $rows = $kuisModel->getAllKuisWithKategori(); 


        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id_kuis'           => (int)($r['id_kuis'] ?? 0),
                'status'            => strtolower((string)($r['status'] ?? 'draft')), 
                'nama_kuis'         => (string)($r['nama_kuis'] ?? ''),
                'topik'             => (string)($r['topik'] ?? ''),
                'tanggal'           => (string)($r['tanggal'] ?? ''),
                'waktu_mulai'       => (string)($r['waktu_mulai'] ?? ''),
                'waktu_selesai'     => (string)($r['waktu_selesai'] ?? ''),
                'nilai_minimum'     => (int)($r['nilai_minimum'] ?? 0),
                'batas_pengulangan' => (int)($r['batas_pengulangan'] ?? 0),
                'kategori'          => (string)($r['kategori'] ?? ($r['kategori_names'] ?? '')),
            ];
        }

        return $this->response->setJSON([
            'ok'   => true,
            'data' => $out
        ]);
    }

    public function index()
    {

        $this->autoDeactivate();

        $kuisModel = new KuisModel();
        $kuisModel->autoActivateDueDrafts();   // <-- DITAMBAHKAN

        // 🔁 Ganti ke model agar status sinkron & tidak dioverride
        $data['kuis'] = $kuisModel->getAllKuisWithKategori();

        return view('admin/kuis/index', $data);
    }

    public function create()
    {
        $kategoriModel = new KategoriAgentModel();
        $data['kategori'] = $kategoriModel->where('is_active', 1)->findAll();
        return view('admin/kuis/create', $data);
    }

    public function store_kuis()
    {
        $kuisModel = new KuisModel();
        $db = \Config\Database::connect();

        $fileExcel = $this->request->getFile('file_excel');
        
        $tanggal      = $this->request->getPost('tanggal_pelaksanaan');
        $waktuMulai   = $this->request->getPost('waktu_mulai');
        $waktuSelesai = $this->request->getPost('waktu_selesai');

        $startAt = new \DateTime("$tanggal $waktuMulai");
        $endAt   = new \DateTime("$tanggal $waktuSelesai");
        if ($endAt <= $startAt) {
            
            $endAt->modify('+1 day');
        }

        $dataKuis = [
            'nama_kuis'         => $this->request->getPost('nama_kuis'),
            'topik'             => $this->request->getPost('topik'),
            'tanggal'           => $tanggal,
            'waktu_mulai'       => $waktuMulai,
            'waktu_selesai'     => $waktuSelesai,
            'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
            'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
            'status'            => 'draft',
            'file_excel'        => null,
            
            'start_at'          => $startAt->format('Y-m-d H:i:s'),
            'end_at'            => $endAt->format('Y-m-d H:i:s'),
        ];

        if ($fileExcel && $fileExcel->isValid() && !$fileExcel->hasMoved()) {
            $newName = $fileExcel->getRandomName();
            $fileExcel->move(WRITEPATH . 'uploads', $newName);
            $dataKuis['file_excel'] = $newName;
        }

        $idKuis = $kuisModel->insert($dataKuis);

        $kategoriDipilih = $this->request->getPost('id_kategori');
        if ($kategoriDipilih && is_array($kategoriDipilih)) {
            $pivot = [];
            foreach ($kategoriDipilih as $idKat) {
                $pivot[] = [
                    'id_kuis'     => $idKuis,
                    'id_kategori' => $idKat
                ];
            }
            $db->table('kuis_kategori')->insertBatch($pivot);
        }

        if (!empty($dataKuis['file_excel'])) {
            $this->importSoal($idKuis, WRITEPATH . 'uploads/' . $dataKuis['file_excel']);
        }

        return redirect()->to('/admin/kuis')->with('success', 'Kuis berhasil ditambahkan.');
    }

    private function importSoal($idKuis, $filePath)
    {
        $db = \Config\Database::connect();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $soalData = [];
        for ($i = 1; $i < count($rows); $i++) { 
            $row = $rows[$i];
            if (!empty($row[0])) {
                $soalData[] = [
                    'id_kuis'   => $idKuis,           
                    'soal'      => $row[0],
                    'pilihan_a' => $row[1] ?? '',
                    'pilihan_b' => $row[2] ?? '',
                    'pilihan_c' => $row[3] ?? '',
                    'pilihan_d' => $row[4] ?? '',
                    'pilihan_e' => $row[5] ?? '',
                    'jawaban'   => $row[6] ?? '',
                ];
            }
        }

        if (!empty($soalData)) {
            $db->table('soal_kuis')->insertBatch($soalData);
        }
    }

    public function edit($id)
    {
        $kuisModel = new KuisModel();
        $kategoriModel = new KategoriAgentModel();

        $data['kuis'] = $kuisModel->find($id);
        if (!$data['kuis']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Kuis dengan ID $id tidak ditemukan.");
        }

        $data['kategori'] = $kategoriModel->where('is_active', 1)->findAll();
        $db = \Config\Database::connect();
        $data['kuisKategori'] = $db->table('kuis_kategori')
                                   ->where('id_kuis', $id)
                                   ->get()
                                   ->getResultArray();

        return view('admin/kuis/edit', $data);
    }

    public function update($id)
    {
        $kuisModel = new KuisModel();
        $db = \Config\Database::connect();

        
        $tanggal      = $this->request->getPost('tanggal');
        $waktuMulai   = $this->request->getPost('waktu_mulai');
        $waktuSelesai = $this->request->getPost('waktu_selesai');

        $startAt = new \DateTime("$tanggal $waktuMulai");
        $endAt   = new \DateTime("$tanggal $waktuSelesai");
        if ($endAt <= $startAt) {
            $endAt->modify('+1 day');
        }
        

        $data = [
            'nama_kuis'         => $this->request->getPost('nama_kuis'),
            'topik'             => $this->request->getPost('topik'),
            'tanggal'           => $tanggal,
            'waktu_mulai'       => $waktuMulai,
            'waktu_selesai'     => $waktuSelesai,
            'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
            'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
            'start_at'          => $startAt->format('Y-m-d H:i:s'),
            'end_at'            => $endAt->format('Y-m-d H:i:s'),
        ];

        $fileExcel = $this->request->getFile('file_excel');
      if ($fileExcel && $fileExcel->isValid()) {
            $newName = $fileExcel->getRandomName();
            $fileExcel->move(WRITEPATH . 'uploads', $newName);
            $data['file_excel'] = $newName;

            $db->table('soal_kuis')->where('id_kuis', $id)->delete();
            $this->importSoal($id, WRITEPATH . 'uploads/' . $newName);
        }

        $kuisModel->update($id, $data);

        $db->table('kuis_kategori')->where('id_kuis', $id)->delete();
        $kategoriDipilih = $this->request->getPost('id_kategori');
        if ($kategoriDipilih) {
            $pivot = [];
            foreach ($kategoriDipilih as $idKat) {
                $pivot[] = [
                    'id_kuis'     => $id,
                    'id_kategori' => $idKat
                ];
            }
            $db->table('kuis_kategori')->insertBatch($pivot);
        }

        return redirect()->to('/admin/kuis')->with('success', 'Data kuis berhasil diperbarui.');
    }

    private function updateStatusKuis($kuisList)
    {
        $kuisModel = new KuisModel();
        $now = date('Y-m-d H:i:s');

        foreach ($kuisList as $kuis) {
            $mulai   = $kuis['tanggal'] . ' ' . $kuis['waktu_mulai'];
            $selesai = $kuis['tanggal'] . ' ' . $kuis['waktu_selesai'];
            $status  = $kuis['status'];

            if ($now < $mulai) {
                $newStatus = 'draft';
            } elseif ($now >= $mulai && $now <= $selesai) {
                $newStatus = 'active';
            } else {
                $newStatus = 'inactive';
            }

            if ($status !== $newStatus) {
                $kuisModel->update($kuis['id_kuis'], ['status' => $newStatus]);
            }
        }
    }

    public function delete($id)
    {
        $kuisModel = new KuisModel();
        $kuis = $kuisModel->find($id);
        if (!$kuis) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Kuis dengan ID $id tidak ditemukan.");
        }

        $kuisModel->delete($id);
        return redirect()->to('/admin/kuis')->with('success', 'Kuis berhasil dihapus.');
    }

    public function archive($id_kuis)
    {
        $soalModel = new SoalModel();
        $dataSoal = $soalModel->where('id_kuis', $id_kuis)->findAll();

        if (empty($dataSoal)) {
            return redirect()->back()->with('error', 'Soal untuk kuis ini tidak ditemukan.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'soal');
        $sheet->setCellValue('B1', 'pilihan_a');
        $sheet->setCellValue('C1', 'pilihan_b');
        $sheet->setCellValue('D1', 'pilihan_c');
        $sheet->setCellValue('E1', 'pilihan_d');
        $sheet->setCellValue('F1', 'pilihan_e');
        $sheet->setCellValue('G1', 'jawaban');

        $row = 2;
        foreach ($dataSoal as $soal) {
            $sheet->setCellValue('A'.$row, $soal['soal']);
            $sheet->setCellValue('B'.$row, $soal['pilihan_a']);
            $sheet->setCellValue('C'.$row, $soal['pilihan_b']);
            $sheet->setCellValue('D'.$row, $soal['pilihan_c']);
            $sheet->setCellValue('E'.$row, $soal['pilihan_d']);
            $sheet->setCellValue('F'.$row, $soal['pilihan_e']);
            $sheet->setCellValue('G'.$row, $soal['jawaban']);
            $row++;
        }

        $fileName = 'arsip_soal_kuis_' . $id_kuis . '_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $fileName .'"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function upload($id)
    {
        $kuisModel = new \App\Models\KuisModel();

        
        $kuis = $kuisModel->find($id);
        if (!$kuis) {
            return redirect()->to('/admin/kuis')
                             ->with('error', 'Kuis tidak ditemukan.');
        }

        
        if ($kuis['status'] !== 'draft') {
            return redirect()->to('/admin/kuis')
                             ->with('error', 'Kuis ini sudah diupload atau nonaktif.');
        }

        
        if (!$kuisModel->uploadKuis($id)) {
            return redirect()->to('/admin/kuis')
                             ->with('error', 'Gagal mengubah status kuis.');
        }


        return redirect()->to('/admin/kuis')
                         ->with('success', 'Kuis berhasil diupload dan status berubah menjadi aktif.');
    }

    public function agentIndex()
    {
        
        $this->autoDeactivate();

        
        $idKategori = $this->getLoggedInKategoriId();

        $kuisModel = new KuisModel();

        
        $data['kuis'] = $idKategori ? $kuisModel->getKuisByKategoriForNow($idKategori) : [];

        return view('agent/dashboard', $data);
    }

    public function kerjakan($id_kuis)
    {
        $kuisModel = new KuisModel();
        $soalModel = new SoalModel();
        $now = date('Y-m-d H:i:s');

        
        $idKategori = $this->getLoggedInKategoriId();
        if (!$idKategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Kuis tidak ditemukan atau tidak tersedia untuk kategori Anda."
            );
        }

        
        $db = \Config\Database::connect();
        $kuis = $db->table('kuis k')
                   ->select('k.*')
                   ->join('kuis_kategori kk', 'kk.id_kuis = k.id_kuis', 'left')
                   ->where('k.id_kuis', $id_kuis)
                   ->where('kk.id_kategori', $idKategori)
                   ->where('k.status', 'active')
                   ->where('k.start_at <=', $now)
                   ->where('k.end_at >',  $now)
                   ->get()->getRowArray();

        if (!$kuis) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Kuis tidak ditemukan atau sudah tidak tersedia."
            );
        }

        $soalList = $soalModel->where('id_kuis', $id_kuis)->findAll();

        return view('agent/soal', [
            'kuis'     => $kuis,
            'soalList' => $soalList
        ]);
    }

    public function kuisAktif()
    {
        $kuisModel = new KuisModel();

        
        $data['kuis'] = $kuisModel->where('status', 'active')
                                  ->orderBy('tanggal', 'DESC')
                                  ->findAll();

        return view('admin/kuis', $data);
    }


    public function start($idKuis)
    {
        $session = session();
        $userId  = (int) ($session->get('id_user') ?? 0);

        if (!$userId) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Silakan login terlebih dahulu.'])->setStatusCode(401);
        }

        $db         = \Config\Database::connect();
        $kuisModel  = new \App\Models\KuisModel();
        $hasilModel = new \App\Models\KuisHasilModel();

        
        $kuis = $kuisModel->find($idKuis);
        if (!$kuis) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Kuis tidak ditemukan'])->setStatusCode(404);
        }

        
        $idKategori = $this->getLoggedInKategoriId();
        if ($idKategori) {
            $allowed = $db->table('kuis_kategori')
                          ->where('id_kuis', $idKuis)
                          ->where('id_kategori', $idKategori)
                          ->countAllResults();
            if ($allowed === 0) {
                return $this->response->setJSON(['ok'=>false,'error'=>'Kuis tidak tersedia untuk kategori Anda'])->setStatusCode(403);
            }
        }

        
        $now = date('Y-m-d H:i:s');
        if (strtolower((string)$kuis['status']) !== 'active') {
            return $this->response->setJSON(['ok' => false, 'error' => 'Kuis tidak aktif'])->setStatusCode(400);
        }
        if (!empty($kuis['start_at']) && $now < $kuis['start_at']) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Kuis belum dimulai'])->setStatusCode(400);
        }
        if (!empty($kuis['end_at']) && $now >= $kuis['end_at']) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Kuis sudah berakhir'])->setStatusCode(400);
        }

        
        $nilaiMax = $hasilModel
            ->where('id_user', $userId)
            ->where('id_kuis', $idKuis)
            ->selectMax('nilai')
            ->get()
            ->getRowArray();
        if (!empty($nilaiMax) && (int)$nilaiMax['nilai'] === 100) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Nilai sudah 100. Kuis dianggap selesai.'])->setStatusCode(403);
        }

        
        $attemptUsed = $hasilModel
            ->where('id_user', $userId)
            ->where('id_kuis', $idKuis)
            ->countAllResults();
        $maxAttempts = (int)($kuis['batas_pengulangan'] ?? 1);
        if ($attemptUsed >= $maxAttempts) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Batas percobaan sudah habis'])->setStatusCode(403);
        }

        
        try {
            $db->transStart();

            $hasilModel->insert([
                'id_user'           => $userId,
                'id_kuis'           => $idKuis,
                'status'            => 'in_progress',
                'started_at'        => $now,
                'tanggal_pengerjaan'=> date('Y-m-d'),
                'jumlah_pengerjaan' => $attemptUsed + 1,
            ]);
            $idHasil = $hasilModel->getInsertID();

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            
            return $this->response->setJSON([
                'ok'         => true,
                'message'    => 'Attempt baru dibuat',
                'attempt_id' => $idHasil,
                'redirect'   => base_url("quiz/attempt/{$idHasil}")
            ]);

        } catch (\Throwable $e) {
            if ($db->transStatus() === true) {
                $db->transRollback();
            }
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'Gagal memulai kuis: '.$e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    
    private function getAttemptOrFail(int $idHasil, int $userId): array
    {
        $db = \Config\Database::connect();

        $attempt = $db->table('kuis_hasil')->where('id', $idHasil)->get()->getRowArray();
        if (!$attempt) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Attempt tidak ditemukan.');
        }
        if ((int)$attempt['id_user'] !== $userId) {
            
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Attempt tidak ditemukan.');
        }

        $kuis = $db->table('kuis')->where('id_kuis', $attempt['id_kuis'])->get()->getRowArray();
        if (!$kuis) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kuis tidak ditemukan.');
        }

        return [$attempt, $kuis];
    }

    
    private function hitungNilai(int $idKuis, array $jawabanInput): array
    {
        $soalModel = new SoalModel();
        $soalList  = $soalModel->where('id_kuis', $idKuis)->findAll();

        $total = count($soalList);
        $benar = 0;

        foreach ($soalList as $s) {
            $sid     = (int)$s['id_soal'];
            $kunci   = strtolower(trim((string)$s['jawaban']));
            $jawaban = strtolower(trim((string)($jawabanInput[$sid] ?? '')));
            if ($kunci !== '' && $jawaban !== '' && $jawaban === $kunci) {
                $benar++;
            }
        }

        $nilai = $total > 0 ? (int) round(($benar / $total) * 100) : 0;

        return [$nilai, $total, $benar];
    }

    
    public function attempt($idHasil)
    {
        $session = session();
        $userId  = (int) ($session->get('id_user') ?? 0);
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        [$attempt, $kuis] = $this->getAttemptOrFail((int)$idHasil, $userId);

        
        $now = date('Y-m-d H:i:s');
        if (!empty($kuis['end_at']) && $now >= $kuis['end_at']) {
            
            if (($attempt['status'] ?? '') === 'in_progress') {
                return redirect()->to(base_url("quiz/attempt/{$idHasil}/result"))
                                ->with('warning', 'Waktu kuis telah berakhir.');
            }
        }

        
        $soalModel = new SoalModel();
        $soalList  = $soalModel->where('id_kuis', $attempt['id_kuis'])->findAll();

        
        return view('agent/soal', [
            'kuis'      => $kuis,
            'soalList'  => $soalList,
            'attemptId' => (int)$idHasil, 
        ]);
    }

    
    public function result($idHasil)
    {
        $session = session();
        $userId  = (int) ($session->get('id_user') ?? 0);
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        [$attempt, $kuis] = $this->getAttemptOrFail((int)$idHasil, $userId);

        if (($attempt['status'] ?? '') === 'in_progress') {
            
            return redirect()->to(base_url("quiz/attempt/{$idHasil}"))
                             ->with('warning', 'Selesaikan kuis terlebih dahulu.');
        }

        return $this->response->setJSON([
            'ok'           => true,
            'attempt_id'   => (int)$idHasil,
            'quiz'         => ['id'=>(int)$kuis['id_kuis'], 'nama'=>$kuis['nama_kuis'] ?? ''],
            'status'       => $attempt['status'],
            'nilai'        => (int)($attempt['nilai'] ?? 0),
            'started_at'   => $attempt['started_at'] ?? null,
            'finished_at'  => $attempt['finished_at'] ?? null,
        ]);
    }
}
