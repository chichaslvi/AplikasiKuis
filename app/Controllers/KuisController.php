<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\SoalModel;
use App\Models\KategoriAgentModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KuisController extends BaseController
{
    public function __construct()
    {
        // Paksa semua date() & strtotime() di controller ini pakai WIB
        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * ✅ Turunkan semua kuis active menjadi inactive jika end_at sudah lewat.
     */
    private function autoDeactivate()
{
    $db = \Config\Database::connect();

    // Paksa timezone MySQL ke WIB (UTC+07:00)
    $db->query("SET time_zone = '+07:00'");

    // Turunkan semua kuis active yang end_at sudah lewat (dibandingkan dengan NOW() di MySQL)
    $db->query("
        UPDATE kuis
        SET status = 'inactive'
        WHERE LOWER(status) = 'active'
          AND end_at IS NOT NULL
          AND end_at <= NOW()
    ");
}


    public function index()
    {
        // ✅ pastikan kuis yang sudah lewat waktu diturunkan
        $this->autoDeactivate();

        // 🔁 Ganti ke model agar status sinkron & tidak dioverride
        $kuisModel = new KuisModel();
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

    // === Hitung start_at & end_at dari tanggal + waktu ===
    $tanggal      = $this->request->getPost('tanggal_pelaksanaan');
    $waktuMulai   = $this->request->getPost('waktu_mulai');
    $waktuSelesai = $this->request->getPost('waktu_selesai');

    $startAt = new \DateTime("$tanggal $waktuMulai");
    $endAt   = new \DateTime("$tanggal $waktuSelesai");
    if ($endAt <= $startAt) {
        // kalau jam selesai <= jam mulai, anggap lewat tengah malam
        $endAt->modify('+1 day');
    }
    // ================================================

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
        // simpan window waktu:
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
        for ($i = 1; $i < count($rows); $i++) { // mulai dari baris ke-2
            $row = $rows[$i];
            if (!empty($row[0])) {
                $soalData[] = [
                    'id_kuis'   => $idKuis,           // wajib isi
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

    // === Hitung ulang start_at & end_at ===
    $tanggal      = $this->request->getPost('tanggal');
    $waktuMulai   = $this->request->getPost('waktu_mulai');
    $waktuSelesai = $this->request->getPost('waktu_selesai');

    $startAt = new \DateTime("$tanggal $waktuMulai");
    $endAt   = new \DateTime("$tanggal $waktuSelesai");
    if ($endAt <= $startAt) {
        $endAt->modify('+1 day');
    }
    // =====================================

    $data = [
        'nama_kuis'         => $this->request->getPost('nama_kuis'),
        'topik'             => $this->request->getPost('topik'),
        'tanggal'           => $tanggal,
        'waktu_mulai'       => $waktuMulai,
        'waktu_selesai'     => $waktuSelesai,
        'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
        'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
        // simpan window waktu:
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

        // 1. Cari kuis berdasarkan ID
        $kuis = $kuisModel->find($id);
        if (!$kuis) {
            return redirect()->to('/admin/kuis')
                             ->with('error', 'Kuis tidak ditemukan.');
        }

        // 2. Pastikan status masih draft
        if ($kuis['status'] !== 'draft') {
            return redirect()->to('/admin/kuis')
                             ->with('error', 'Kuis ini sudah diupload atau nonaktif.');
        }

        // 3. Jalankan update status melalui model
        if (!$kuisModel->uploadKuis($id)) {
            return redirect()->to('/admin/kuis')
                             ->with('error', 'Gagal mengubah status kuis.');
        }

        // 4. Kalau berhasil
        return redirect()->to('/admin/kuis')
                         ->with('success', 'Kuis berhasil diupload dan status berubah menjadi aktif.');
    }

    public function agentIndex()
    {
        // ✅ pastikan kuis kadaluarsa diturunkan
        $this->autoDeactivate();

        $kuisModel = new KuisModel();
        $now = date('Y-m-d H:i:s');

        // ✅ tampilkan hanya kuis active yang sedang dalam window waktu
        $data['kuis'] = $kuisModel->where('status', 'active')
                                  ->where('start_at <=', $now)
                                  ->where('end_at >',  $now)
                                  ->orderBy('start_at', 'ASC')
                                  ->findAll();

        return view('agent/dashboard', $data);
    }

    public function kerjakan($id_kuis)
{
    $kuisModel = new KuisModel();
    $soalModel = new SoalModel();
    $now = date('Y-m-d H:i:s');

    // Hanya boleh mengerjakan jika kuis masih ACTIVE dan masih dalam window waktu
    $kuis = $kuisModel->where('id_kuis', $id_kuis)
                      ->where('status', 'active')
                      ->where('start_at <=', $now)
                      ->where('end_at >',  $now)
                      ->first();

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

        // ambil hanya kuis dengan status active
        $data['kuis'] = $kuisModel->where('status', 'active')
                                  ->orderBy('tanggal', 'DESC')
                                  ->findAll();

        return view('admin/kuis', $data);
    }

    public function soal($id_kuis)
{
    $kuisModel = new KuisModel();
    $soalModel = new SoalModel();
    $now = date('Y-m-d H:i:s');

    // Pastikan hanya kuis ACTIVE & dalam window waktu yang bisa diakses
    $kuis = $kuisModel->where('id_kuis', $id_kuis)
                      ->where('status', 'active')
                      ->where('start_at <=', $now)
                      ->where('end_at >',  $now)
                      ->first();

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
}
