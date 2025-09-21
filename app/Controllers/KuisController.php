<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\KategoriAgentModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KuisController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT k.*, 
                   GROUP_CONCAT(ka.nama_kategori SEPARATOR ', ') AS kategori
            FROM kuis k
            LEFT JOIN kuis_kategori kk ON k.id_kuis = kk.id_kuis
            LEFT JOIN kategori_agent ka ON kk.id_kategori = ka.id_kategori
            GROUP BY k.id_kuis
            ORDER BY k.id_kuis DESC
        ");

        $data['kuis'] = $query->getResultArray();
        return view('admin/kuis/index', $data);
    }

 public function create()
    {
        $kategoriModel = new KategoriAgentModel();

        // Ambil hanya kategori aktif (is_active = 1)
        $data['kategori'] = $kategoriModel->where('is_active', 1)->findAll();

        return view('admin/kuis/create', $data);
    }
   public function store_kuis() 
{
    $kuisModel = new KuisModel();
    $db = \Config\Database::connect();

    $fileExcel = $this->request->getFile('file_excel');

    $dataKuis = [
        'nama_kuis'         => $this->request->getPost('nama_kuis'),
        'topik'             => $this->request->getPost('topik'),
        'tanggal'           => $this->request->getPost('tanggal_pelaksanaan'),
        'waktu_mulai'       => $this->request->getPost('waktu_mulai'),
        'waktu_selesai'     => $this->request->getPost('waktu_selesai'),
        'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
        'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
        'file_excel'        => null // default null
    ];

    // Upload file Excel jika ada
    if ($fileExcel && $fileExcel->isValid() && !$fileExcel->hasMoved()) {
        $newName = $fileExcel->getRandomName();
        $fileExcel->move(WRITEPATH . 'uploads', $newName);
        $dataKuis['file_excel'] = $newName;
    }

    // Insert kuis
    $idKuis = $kuisModel->insert($dataKuis);

    // Insert pivot kategori
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

    // Import soal dari Excel jika ada
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
        // Asumsikan baris pertama adalah header
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (!empty($row[0])) { // Cek kolom soal tidak kosong
                $soalData[] = [
                    'id_kuis'     => $idKuis,
                    'soal'        => $row[0],
                    'pilihan_a'   => $row[1] ?? null,
                    'pilihan_b'   => $row[2] ?? null,
                    'pilihan_c'   => $row[3] ?? null,
                    'pilihan_d'   => $row[4] ?? null,
                    'jawaban'     => $row[5] ?? null,
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

    // Ambil data kuis
    $data['kuis'] = $kuisModel->find($id);
    if (!$data['kuis']) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException("Kuis dengan ID $id tidak ditemukan.");
    }

    // Ambil hanya kategori aktif
    $data['kategori'] = $kategoriModel->where('is_active', 1)->findAll();

    // Ambil kategori yang sudah dipilih dari pivot table kuis_kategori
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

        $data = [
            'nama_kuis'         => $this->request->getPost('nama_kuis'),
            'topik'             => $this->request->getPost('topik'),
            'tanggal'           => $this->request->getPost('tanggal'),
            'waktu_mulai'       => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'     => $this->request->getPost('waktu_selesai'),
            'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
            'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
        ];

        $fileExcel = $this->request->getFile('file_excel');
        if ($fileExcel && $fileExcel->isValid()) {
            $newName = $fileExcel->getRandomName();
            $fileExcel->move(WRITEPATH . 'uploads', $newName);
            $data['file_excel'] = $newName;

            // Hapus soal lama
            $db->table('soal_kuis')->where('id_kuis', $id)->delete();

            // Import soal baru
            $this->importSoal($id, WRITEPATH . 'uploads/' . $newName);
        }

        $kuisModel->update($id, $data);

        // Update pivot kategori
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

    public function upload($id)
    {
        $kuisModel = new KuisModel();
        $kuisModel->update($id, ['status' => 'active']);
        return redirect()->to('/admin/kuis')->with('success', 'Kuis berhasil diaktifkan.');
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

    public function archive($id)
{
    $kuisModel = new KuisModel();
    $kuis = $kuisModel->find($id);

    // Pastikan kuis ada dan file_excel ada
    if (!$kuis || empty($kuis['file_excel'])) {
        return redirect()->back()->with('error', 'File arsip tidak ditemukan.');
    }

    $filePath = WRITEPATH . 'uploads/' . $kuis['file_excel'];

    // Cek file fisik
    if (!file_exists($filePath)) {
        return redirect()->back()->with('error', 'File tidak tersedia di server.');
    }

    // Set headers untuk download / buka di browser
    return $this->response
        ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->setHeader('Content-Disposition', 'inline; filename="' . $kuis['file_excel'] . '"')
        ->setBody(file_get_contents($filePath));
}

}