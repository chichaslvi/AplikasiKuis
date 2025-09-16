<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\KategoriAgentModel;
use App\Models\TeamLeaderModel;

class UserController extends BaseController
{
    // ========================
    // Menampilkan daftar user
    // ========================
    public function index()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('users u')
            ->select('u.*, ka.nama_kategori, tl.nama as nama_tl')
            ->join('kategori_agent ka', 'ka.id_kategori = u.kategori_agent_id', 'left')
            ->join('team_leader tl', 'tl.id = u.team_leader_id', 'left');

        // filter role kalau ada
        $role = $this->request->getGet('role');
        if ($role) {
            $builder->where('u.role', $role);
        }

        $users = $builder->get()->getResultArray();

        return view('admin/users/index', [
            'users'        => $users,
            'selectedRole' => $role
        ]);
    }

    // ========================
    // Form tambah Admin & Reviewer
    // ========================
    public function create_admin()
    {
        return view('admin/users/create_admin');
    }

    public function store_admin()
    {
        $userModel = new UserModel();

        if (!$this->validate([
            'nama'     => 'required',
            'nik'      => 'required|is_unique[users.nik]',
            'password' => 'required|min_length[6]',
            'role'     => 'required'
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

    // ========================
    // Form tambah Agent
    // ========================
    public function create_agent()
    {
        $kategoriAgent = (new KategoriAgentModel())->where('is_active', 1)->findAll();
        $teamLeaders   = (new TeamLeaderModel())->where('is_active', 1)->findAll();

        return view('admin/users/create_agent', [
            'kategoriAgent' => $kategoriAgent,
            'teamLeaders'   => $teamLeaders
        ]);
    }

    public function store_agent()
    {
        $userModel = new UserModel();

        if (!$this->validate([
            'nama'              => 'required',
            'nik'               => 'required|is_unique[users.nik]',
            'password'          => 'required|min_length[6]',
            'kategori_agent_id' => 'required',
            'team_leader_id'    => 'required'
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

    // ========================
    // Form edit User umum
    // ========================
    public function edit($id)
    {
        $userModel = new UserModel();
        $user      = $userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User dengan ID $id tidak ditemukan");
        }

        return view('admin/users/edit', ['user' => $user]);
    }

    public function update($id)
    {
        $userModel = new UserModel();
        $user      = $userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User dengan ID $id tidak ditemukan");
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'nik'  => $this->request->getPost('nik'),
            'role' => $this->request->getPost('role'),
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($user['role'] === 'agent') {
            $data['kategori_agent_id'] = $this->request->getPost('kategori_agent_id');
            $data['team_leader_id']    = $this->request->getPost('team_leader_id');
        }

        $userModel->update($id, $data);

        return redirect()->to('/admin/users')->with('success', 'User berhasil diperbarui!');
    }

    // ========================
    // Aktivasi / Nonaktifkan / Hapus
    // ========================
    public function deactivate($id)
    {
        $userModel = new UserModel();
        $userModel->update($id, ['is_active' => 0]);
        return redirect()->to('admin/users')->with('success', 'User dinonaktifkan');
    }

    public function activate($id)
    {
        $userModel = new UserModel();
        $userModel->update($id, ['is_active' => 1]);
        return redirect()->to('admin/users')->with('success', 'User diaktifkan');
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        $userModel->delete($id, true); // ✅ hapus permanen, bukan soft delete
        return redirect()->to('admin/users')->with('success', 'User berhasil dihapus permanen!');
    }

    // ========================
    // Form edit Agent khusus
    // ========================
    public function edit_agent($id)
    {
        $userModel     = new UserModel();
        $kategoriModel = new KategoriAgentModel();
        $teamModel     = new TeamLeaderModel();

        $agent = $userModel->find($id);

        if (!$agent || $agent['role'] !== 'agent') {
            return redirect()->to('/admin/users')->with('error', 'Agent tidak ditemukan');
        }

        $data = [
            'agent'       => $agent,
            'kategoris'   => $kategoriModel->where('is_active', 1)->findAll(),
            'teamLeaders' => $teamModel->where('is_active', 1)->findAll(),
            'validation'  => \Config\Services::validation()
        ];

        return view('admin/users/edit_agent', $data);
    }

    public function update_agent($id)
    {
        $userModel  = new UserModel();
        $validation = \Config\Services::validation();

        $validationRules = [
            'nama'              => 'required',
            'nik'               => 'required',
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

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);

        return redirect()->to('/admin/users')->with('success', 'Agent berhasil diperbarui.');
    }
}
