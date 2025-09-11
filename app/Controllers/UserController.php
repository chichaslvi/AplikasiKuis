<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    // Tampilkan daftar user
    public function index()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll(); // ambil semua user dari database

        return view('admin/users/index', ['users' => $users]); 
    }

    // Menampilkan form tambah admin & reviewer
    public function create_admin()
    {
        return view('admin/users/create_admin'); 
    }

    // Proses simpan data admin & reviewer
    public function store_admin()
    {
        $userModel = new UserModel();

        // Validasi input sederhana
        if (!$this->validate([
            'nama' => 'required',
            'nik' => 'required|is_unique[users.nik]',
            'password' => 'required|min_length[6]',
            'role' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama'     => $this->request->getPost('nama'),
            'nik'      => $this->request->getPost('nik'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role'),
        ];

        $userModel->insert($data);

        return redirect()->to('/admin/users')->with('success', 'Akun berhasil ditambahkan!');
    }

    // Menampilkan form tambah agent
    public function create_agent()
    {
        return view('admin/users/create_agent');
    }

    // Proses simpan data agent
    public function store_agent()
    {
        $userModel = new UserModel();

        if (!$this->validate([
            'nama'           => 'required',
            'nik'            => 'required|is_unique[users.nik]',
            'password'       => 'required|min_length[6]',
            'kategori_agent' => 'required',
            'team_leader'    => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama'           => $this->request->getPost('nama'),
            'nik'            => $this->request->getPost('nik'),
            'password'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'           => 'agent',
            'kategori_agent' => $this->request->getPost('kategori_agent'),
            'team_leader'    => $this->request->getPost('team_leader'),
        ];

        $userModel->insert($data);

        return redirect()->to('/admin/users')->with('success', 'Akun Agent berhasil ditambahkan!');
    }
}
