<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\KategoriAgentModel;

class KuisController extends BaseController
{
    public function index()
    {
        $kuisModel = new KuisModel();
        $data['kuis'] = $kuisModel->findAll();

        return view('admin/kuis/index', $data); 
    }

    public function create()
    {
        $kategoriModel = new KategoriAgentModel();
        $data['kategori'] = $kategoriModel->findAll();

        return view('admin/kuis/create', $data);
    }

    public function store_kuis()
    {
        $kuisModel = new KuisModel();

        $kategori = $this->request->getPost('id_kategori');

        $data = [
            'nama_kuis'         => $this->request->getPost('nama_kuis'),
            'topik'             => $this->request->getPost('topik'),
            'tanggal'           => $this->request->getPost('tanggal_pelaksanaan'),
            'waktu_mulai'       => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'     => $this->request->getPost('waktu_selesai'),
            'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
            'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
            'id_kategori'       => is_array($kategori) ? implode(',', $kategori) : $kategori,
        ];

        $kuisModel->insert($data);

        return redirect()->to('/admin/kuis')->with('success', 'Kuis berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kuisModel = new KuisModel();
        $kategoriModel = new KategoriAgentModel();

        $data['kuis'] = $kuisModel->find($id);
        $data['kategori'] = $kategoriModel->findAll();

        if (!$data['kuis']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Kuis dengan ID $id tidak ditemukan.");
        }

        return view('admin/kuis/edit', $data);
    }

    public function update($id)
    {
        $kuisModel = new KuisModel();

        $kategori = $this->request->getPost('id_kategori');

        $data = [
            'nama_kuis'         => $this->request->getPost('nama_kuis'),
            'topik'             => $this->request->getPost('topik'),
            'tanggal'           => $this->request->getPost('tanggal'),
            'waktu_mulai'       => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'     => $this->request->getPost('waktu_selesai'),
            'nilai_minimum'     => $this->request->getPost('nilai_minimum'),
            'batas_pengulangan' => $this->request->getPost('batas_pengulangan'),
            'id_kategori'       => is_array($kategori) ? implode(',', $kategori) : $kategori,
        ];

        $kuisModel->update($id, $data);

        return redirect()->to('/admin/kuis')->with('success', 'Data kuis berhasil diperbarui');
    
    }
    public function upload($id)
{
    $kuisModel = new KuisModel();

    // update status jadi active
    $kuisModel->update($id, ['status' => 'active']);

    return redirect()->to('/admin/kuis')->with('success', 'Kuis berhasil diaktifkan.');
}

public function delete($id)
{
    $kuisModel = new KuisModel();

    // pastikan data ada dulu
    $kuis = $kuisModel->find($id);
    if (!$kuis) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException("Kuis dengan ID $id tidak ditemukan.");
    }

    $kuisModel->delete($id);

    return redirect()->to('/admin/kuis')->with('success', 'Kuis berhasil dihapus.');
}
public function archive($id)
{
    $kuisModel = new \App\Models\KuisModel();
    $kuis = $kuisModel->find($id);

    if (!$kuis || empty($kuis['file_excel'])) {
        return redirect()->back()->with('error', 'File arsip tidak ditemukan.');
    }

    $filePath = WRITEPATH . 'uploads/' . $kuis['file_excel']; // sesuaikan path

    if (!file_exists($filePath)) {
        return redirect()->back()->with('error', 'File tidak tersedia di server.');
    }

    // Buka file excel langsung (tanpa download)
    return $this->response
        ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->setHeader('Content-Disposition', 'inline; filename="' . $kuis['file_excel'] . '"')
        ->setBody(file_get_contents($filePath));
}


}