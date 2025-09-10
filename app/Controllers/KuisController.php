<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\KategoriAgentModel;

class KuisController extends BaseController
{
    public function index()
    {
        return view('admin/kuis/index'); 
    }

    public function create()
    {
        $kategoriModel = new KategoriAgentModel();
        $data['kategori'] = $kategoriModel->findAll();

        return view('admin/kuis/create', $data);
    }

    public function store()
    {
        $kuisModel = new KuisModel();

        $data = [
            'nama_kuis'         => $this->request->getPost('nama_kuis'),
            'topik'             => $this->request->getPost('topik'),
            'tanggal'           => $this->request->getPost('tanggal'),
            'waktu_mulai'       => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'     => $this->request->getPost('waktu_selesai'),
            'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
            'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
            'id_kategori'       => $this->request->getPost('id_kategori'),
        ];

        $kuisModel->insert($data);

        return redirect()->to('/admin/kuis')->with('success', 'Kuis berhasil ditambahkan');
    }
}
