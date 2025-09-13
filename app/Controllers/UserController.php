<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    // Tampilkan daftar user
    public function index()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('users u')
            ->select('u.*, ka.nama_kategori, tl.nama as nama')
            ->join('kategori_agent ka', 'ka.id_kategori = u.kategori_agent_id', 'left')
            ->join('team_leader tl', 'tl.id = u.team_leader_id', 'left');

        // cek apakah ada filter role
        $role = $this->request->getGet('role');
        if ($role) {
            $builder->where('u.role', $role);
        }

        $users = $builder->get()->getResultArray();

        return view('admin/users/index', [
            'users' => $users,
            'selectedRole' => $role
        ]);
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
        $db = \Config\Database::connect();

        $kategoriAgent = $db->table('kategori_agent')
                            ->select('id_kategori, nama_kategori')
                            ->get()
                            ->getResultArray();

        $teamLeaders = $db->table('team_leader')
                          ->select('id, nama')
                          ->get()
                          ->getResultArray();

        return view('admin/users/create_agent', [
            'kategoriAgent' => $kategoriAgent,
            'teamLeaders'   => $teamLeaders
        ]);
    }

    // Proses simpan data agent
    public function store_agent()
    {
        $userModel = new UserModel();

        if (!$this->validate([
            'nama'             => 'required',
            'nik'              => 'required|is_unique[users.nik]',
            'password'         => 'required|min_length[6]',
            'kategori_agent_id'=> 'required',
            'team_leader_id'   => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama'              => $this->request->getPost('nama'),
            'nik'               => $this->request->getPost('nik'),
            'password'          => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'              => 'agent',
            'kategori_agent_id' => $this->request->getPost('kategori_agent_id'),
            'team_leader_id'    => $this->request->getPost('team_leader_id'),
        ];

        $userModel->insert($data);

        return redirect()->to('/admin/users')->with('success', 'Akun Agent berhasil ditambahkan!');
    }

    // Menampilkan form edit user
    public function edit($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User dengan ID $id tidak ditemukan");
        }

        return view('admin/users/edit', ['user' => $user]);
    }

    // Proses update user
    public function update($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User dengan ID $id tidak ditemukan");
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'nik'  => $this->request->getPost('nik'),
            'role' => $this->request->getPost('role'),
        ];

        // Update password hanya jika diisi
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // Jika role agent, update tambahan
        if ($user['role'] === 'agent') {
            $data['kategori_agent_id'] = $this->request->getPost('kategori_agent_id');
            $data['team_leader_id']    = $this->request->getPost('team_leader_id');
        }

        $userModel->update($id, $data);

        return redirect()->to('/admin/users')->with('success', 'User berhasil diperbarui!');
    }

    // Proses hapus user
    public function delete($id)
    {
        $userModel = new UserModel();
        $userModel->delete($id);

        return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus!');
    }

    // =========================
    // Tambahan untuk Edit Agent (Sudah diperbaiki)
    // =========================
    public function edit_agent($id)
    {
        $userModel = new UserModel();
        $db = \Config\Database::connect();

        $data['agent'] = $userModel->find($id);

        // Ambil daftar kategori agent langsung dari database
        $data['kategoris'] = $db->table('kategori_agent')
                                ->select('id_kategori, nama_kategori')
                                ->get()
                                ->getResultArray();

        // Ambil daftar team leader langsung dari database
        $data['teamLeaders'] = $db->table('team_leader')
                                  ->select('id, nama')
                                  ->get()
                                  ->getResultArray();

        $data['validation'] = \Config\Services::validation();

        return view('admin/users/edit_agent', $data);
    }

    public function update_agent($id)
    {
        $userModel = new UserModel();
        $validation = \Config\Services::validation();

        // Validasi input
        $validationRules = [
            'nama' => 'required',
            'nik' => 'required',
            'kategori_agent_id' => 'required',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nama'              => $this->request->getPost('nama'),
            'nik'               => $this->request->getPost('nik'),
            'kategori_agent_id' => $this->request->getPost('kategori_agent_id'),
            'team_leader_id'    => $this->request->getPost('team_leader_id') ?: null,
        ];

        // Update password jika diisi
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);

        return redirect()->to('/admin/users')->with('success', 'Agent berhasil diperbarui.');
    }
}
