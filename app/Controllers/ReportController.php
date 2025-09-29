<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\HasilKuisModel;
;

class ReportController extends BaseController
{
    protected $kuisModel;
    protected $hasilModel;

    public function __construct()
    {
        $this->kuisModel  = new KuisModel();
        $this->hasilModel = new HasilKuisModel();
    }

    // 📌 Halaman daftar kuis
    public function index()
    {
        $data['kuis'] = $this->kuisModel->findAll(); // ambil semua kuis dari DB
        return view('admin/report/index', $data);
    }

    // 📌 Detail hasil kuis per peserta
    public function detail($id)
    {
        $kuis = $this->kuisModel->find($id);
        if (!$kuis) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Kuis dengan ID $id tidak ditemukan.");
        }

        // ambil hasil pengerjaan agent
        $peserta = $this->hasilModel
            ->select('users.nama, users.username, kuis_hasil.nilai, kuis_hasil.pengulangan')
            ->join('users', 'users.id = kuis_hasil.id_user')
            ->where('kuis_hasil.id_kuis', $id)
            ->findAll();

        $detail = [
            'id'           => $kuis['id_kuis'],
            'nama_kuis'    => $kuis['nama_kuis'],
            'topik'     => $kuis['topik'],
            'tanggal'      => $kuis['tanggal'],
            'waktu_mulai'  => $kuis['waktu_mulai'],
            'waktu_selesai'=> $kuis['waktu_selesai'],
            'peserta'      => $peserta
        ];

        return view('admin/report/detail', ['detail' => $detail]);
    }
}
