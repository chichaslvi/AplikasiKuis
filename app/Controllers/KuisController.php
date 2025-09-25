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

        // Update status setiap kali index dibuka
        $this->updateStatusKuis($data['kuis']);

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

        $dataKuis = [
            'nama_kuis'         => $this->request->getPost('nama_kuis'),
            'topik'             => $this->request->getPost('topik'),
            'tanggal'           => $this->request->getPost('tanggal_pelaksanaan'),
            'waktu_mulai'       => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'     => $this->request->getPost('waktu_selesai'),
            'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
            'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
            'status'            => 'draft',
            'file_excel'        => null
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

    // pastikan kuis ada & statusnya draft
    $kuis = $kuisModel->find($id);
    if (!$kuis) {
        return redirect()->to('/admin/kuis')->with('error', 'Kuis tidak ditemukan.');
    }

    if ($kuis['status'] !== 'draft') {
        return redirect()->to('/admin/kuis')->with('error', 'Kuis ini sudah diupload atau nonaktif.');
    }

    // update status jadi active
    $kuisModel->uploadKuis($id);

    return redirect()->to('/admin/kuis')->with('success', 'Kuis berhasil diupload dan kini dapat dilihat oleh agent.');
}

}
