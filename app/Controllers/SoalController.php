<?php
namespace App\Controllers;

use App\Models\SoalModel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class SoalController extends BaseController
{
    public function import_excel()
    {
        $file = $this->request->getFile('file_excel');
        
        if ($file->isValid() && !$file->hasMoved()) {
            $filePath = $file->getTempName();

            $reader = new Xlsx();
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $soalModel = new SoalModel();

            // Loop mulai dari baris kedua (baris pertama biasanya header)
            for ($i = 1; $i < count($sheet); $i++) {
                $data = [
                    'pertanyaan' => $sheet[$i][0], // Kolom A
                    'opsi_a'     => $sheet[$i][1], // Kolom B
                    'opsi_b'     => $sheet[$i][2], // Kolom C
                    'opsi_c'     => $sheet[$i][3], // Kolom D
                    'opsi_d'     => $sheet
                    [$i][4], // Kolom D
                    'opsi_e'     => $sheet[$i][5], // Kolom E
                    'jawaban'    => $sheet[$i][6], // Kolom F
                ];

                $soalModel->insert($data);
            }

            return redirect()->back()->with('success', 'Import soal berhasil!');
        } else {
            return redirect()->back()->with('error', 'Gagal upload file.');
        }
    }
}
