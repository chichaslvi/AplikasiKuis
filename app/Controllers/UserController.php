<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\KategoriAgentModel;
use App\Models\TeamLeaderModel;

class UserController extends BaseController
{
    public function __construct()
    {
        // Pastikan hanya admin yang bisa akses controller ini
        if (session()->get('role') !== 'admin') {
            redirect()->to('/auth/login')->send();
            exit;
        }
    }

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
            'nama' => 'required',
            'nik'  => 'required|trim|is_unique[users.nik]',
            'role' => 'required|in_list[admin,reviewer,agent]',
            // password opsional; jika kosong, default = NIK
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama = trim($this->request->getPost('nama'));
        $nik  = trim($this->request->getPost('nik'));
        $role = trim($this->request->getPost('role'));

        $plainPassword = $this->request->getPost('password');
        if ($plainPassword === null || $plainPassword === '') {
            // default password = NIK
            $plainPassword = $nik;
        }

        $data = [
            'nama'     => $nama,
            'nik'      => $nik,
            'role'     => $role,
            'password' => $plainPassword, // PLAIN → di-hash oleh UserModel::prepareInsert
            'is_active'=> 1,
            // must_change_password & last_password_change diatur model
        ];

        $userModel->insert($data);

        return redirect()->to('/admin/users')->with('success', 'Akun berhasil ditambahkan! User akan diminta ubah password saat login pertama.');
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
            'nik'               => 'required|trim|is_unique[users.nik]',
            'kategori_agent_id' => 'required',
            'team_leader_id'    => 'required',
            // password opsional; jika kosong, default = NIK
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama = trim($this->request->getPost('nama'));
        $nik  = trim($this->request->getPost('nik'));

        $plainPassword = $this->request->getPost('password');
        if ($plainPassword === null || $plainPassword === '') {
            $plainPassword = $nik; // default
        }

        $data = [
            'nama'               => $nama,
            'nik'                => $nik,
            'password'           => $plainPassword, // PLAIN → di-hash oleh model
            'role'               => 'agent',
            'kategori_agent_id'  => $this->request->getPost('kategori_agent_id'),
            'team_leader_id'     => $this->request->getPost('team_leader_id'),
            'is_active'          => 1,
            // must_change_password & last_password_change diatur model
        ];

        $userModel->insert($data);

        return redirect()->to('/admin/users')->with('success', 'Akun Agent berhasil ditambahkan! User akan diminta ubah password saat login pertama.');
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
            'nama' => trim($this->request->getPost('nama')),
            'nik'  => trim($this->request->getPost('nik')),
            'role' => trim($this->request->getPost('role')),
        ];

        // Jika admin mengisi password baru → kirim plain; model yang hash & set last_password_change
        $plainPassword = $this->request->getPost('password');
        if (!empty($plainPassword)) {
            $data['password'] = $plainPassword; // PLAIN
            // jangan set must_change_password di sini
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
        $userModel->delete($id, true);
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
            'nama'              => trim($this->request->getPost('nama')),
            'nik'               => trim($this->request->getPost('nik')),
            'kategori_agent_id' => $this->request->getPost('kategori_agent_id'),
            'team_leader_id'    => $this->request->getPost('team_leader_id') ?: null,
        ];

        $plainPassword = $this->request->getPost('password');
        if (!empty($plainPassword)) {
            $data['password'] = $plainPassword; // PLAIN → model hash & set last_password_change
        }

        $userModel->update($id, $data);

        return redirect()->to('/admin/users')->with('success', 'Agent berhasil diperbarui.');
    }
}
