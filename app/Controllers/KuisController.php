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

        // ambil kategori multiple (array)
        $kategori = $this->request->getPost('id_kategori');

        $data = [
            'nama_kuis'         => $this->request->getPost('nama_kuis'),
            'topik'             => $this->request->getPost('topik'),
            'tanggal'           => $this->request->getPost('tanggal'),
            'waktu_mulai'       => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'     => $this->request->getPost('waktu_selesai'),
            'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
            'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
            // simpan kategori sebagai string "1,3,5"
            'id_kategori'       => is_array($kategori) ? implode(',', $kategori) : $kategori,
        ];

        $kuisModel->insert($data);

        return redirect()->to('/admin/kuis')->with('success', 'Kuis berhasil ditambahkan');
    }
}
