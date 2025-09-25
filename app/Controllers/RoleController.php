<?php

namespace App\Controllers;

use App\Models\KategoriAgentModel;
use App\Models\TeamLeaderModel;
use App\Models\UserModel;

class RoleController extends BaseController
{
    public function index()
    {
        $kategoriModel = new KategoriAgentModel();
        $teamModel     = new TeamLeaderModel();

        $data['kategori_active']   = $kategoriModel->where('is_active', 1)->findAll();
        $data['kategori_inactive'] = $kategoriModel->where('is_active', 0)->findAll();

        $data['team_active']   = $teamModel->where('is_active', 1)->findAll();
        $data['team_inactive'] = $teamModel->where('is_active', 0)->findAll();

        return view('admin/roles/index', $data);
    }

    // =============================
    // STORE
    // =============================

    public function storeKategori()
    {
        $kategoriModel = new KategoriAgentModel();

        $nama = $this->request->getPost('nama');
        if ($nama) {
            $exists = $kategoriModel->where('nama_kategori', $nama)->first();
            if (!$exists) {
                $kategoriModel->insert([
                    'nama_kategori' => $nama,
                    'is_active'     => 1
                ]);
            }
        }

        return redirect()->to('/admin/roles')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function storeTeam()
    {
        $teamModel = new TeamLeaderModel();

        $nama = $this->request->getPost('nama');
        if ($nama) {
            $exists = $teamModel->where('nama', $nama)->first();
            if (!$exists) {
                $teamModel->insert([
                    'nama'      => $nama,
                    'is_active' => 1
                ]);
            }
        }

        return redirect()->to('/admin/roles')->with('success', 'Team Leader berhasil ditambahkan');
    }

    // =============================
    // NONAKTIFKAN / DEACTIVATE
    // =============================

    public function nonaktifkanKategori($id)
    {
        $kategoriModel = new KategoriAgentModel();
        $kategori = $kategoriModel->find($id);

        if (!$kategori) {
            return redirect()->to('/admin/roles')->with('error', 'Kategori tidak ditemukan');
        }

        $kategoriModel->update($id, ['is_active' => 0]);
        return redirect()->to('/admin/roles')->with('success', 'Kategori Agent berhasil dinonaktifkan');
    }

    public function nonaktifkanTeam($id)
    {
        $teamModel = new TeamLeaderModel();
        $team = $teamModel->find($id);

        if (!$team) {
            return redirect()->to('/admin/roles')->with('error', 'Team Leader tidak ditemukan');
        }

        $teamModel->update($id, ['is_active' => 0]);
        return redirect()->to('/admin/roles')->with('success', 'Team Leader berhasil dinonaktifkan');
    }

    // ✅ Alias supaya URL deactivate juga jalan
    public function deactivateKategori($id)
    {
        return $this->nonaktifkanKategori($id);
    }

    public function deactivateTeam($id)
    {
        return $this->nonaktifkanTeam($id);
    }

    // =============================
    // AKTIFKAN / ACTIVATE
    // =============================

    public function aktifkanKategori($id)
    {
        $kategoriModel = new KategoriAgentModel();
        $kategori = $kategoriModel->find($id);

        if (!$kategori) {
            return redirect()->to('/admin/roles')->with('error', 'Kategori tidak ditemukan');
        }

        $kategoriModel->update($id, ['is_active' => 1]);
        return redirect()->to('/admin/roles')->with('success', 'Kategori Agent berhasil diaktifkan');
    }

    public function aktifkanTeam($id)
    {
        $teamModel = new TeamLeaderModel();
        $team = $teamModel->find($id);

        if (!$team) {
            return redirect()->to('/admin/roles')->with('error', 'Team Leader tidak ditemukan');
        }

        $teamModel->update($id, ['is_active' => 1]);
        return redirect()->to('/admin/roles')->with('success', 'Team Leader berhasil diaktifkan');
    }

    // ✅ Alias supaya URL activate juga jalan
    public function activateKategori($id)
    {
        return $this->aktifkanKategori($id);
    }

    public function activateTeam($id)
    {
        return $this->aktifkanTeam($id);
    }

    // =============================
    // HAPUS PERMANEN (DESTROY)
    // =============================

    public function destroyKategori($id)
    {
        $kategoriModel = new KategoriAgentModel();
        $userModel     = new UserModel();

        $kategori = $kategoriModel->find($id);

        if (!$kategori) {
            return redirect()->to('/admin/roles')->with('error', 'Kategori tidak ditemukan');
        }

        // Pastikan sudah nonaktif sebelum hapus permanen
        if ($kategori['is_active'] == 1) {
            return redirect()->to('/admin/roles')->with('error', 'Nonaktifkan dulu sebelum hapus permanen');
        }

        // 🔎 Cek apakah masih ada user aktif pakai kategori ini
        $users = $userModel->where('kategori_agent_id', $id)->countAllResults();
        if ($users > 0) {
            return redirect()->to('/admin/roles')->with('error', 'Masih ada user di kategori ini');
        }

        // Jika aman, hapus
        $kategoriModel->delete($id);

        return redirect()->to('/admin/roles')->with('success', 'Kategori berhasil dihapus permanen');
    }
    
    public function destroyTeam($id)
    {
        $teamModel = new TeamLeaderModel();
        $userModel = new UserModel();

        $team = $teamModel->find($id);

        if (!$team) {
            return redirect()->to('/admin/roles')->with('error', 'Team Leader tidak ditemukan');
        }

        if ($team['is_active'] == 1) {
            return redirect()->to('/admin/roles')->with('error', 'Nonaktifkan dulu sebelum hapus permanen');
        }

        $users = $userModel->countActiveByTeam($id);
        if ($users > 0) {
            return redirect()->to('/admin/roles')->with('error', 'Masih ada user aktif di bawah Team Leader ini');
        }

        $teamModel->delete($id);
        return redirect()->to('/admin/roles')->with('success', 'Team Leader berhasil dihapus permanen');
    }

    // =============================
    // DELETE (Soft Delete)
    // =============================

    public function deleteTeam($id)
    {
        $teamModel = new TeamLeaderModel();

        $team = $teamModel->find($id);
        if ($team) {
            $teamModel->update($id, ['is_active' => 0]);
            return redirect()->to('/admin/roles')->with('success', 'Team Leader berhasil dinonaktifkan.');
        }

        return redirect()->to('/admin/roles')->with('error', 'Team Leader tidak ditemukan.');
    }

    public function deleteKategori($id)
    {
        $kategoriModel = new KategoriAgentModel();

        $kategori = $kategoriModel->find($id);
        if ($kategori) {
            $kategoriModel->update($id, ['is_active' => 0]);
            return redirect()->to('/admin/roles')->with('success', 'Kategori Agent berhasil dinonaktifkan.');
        }

        return redirect()->to('/admin/roles')->with('error', 'Kategori tidak ditemukan.');
    }
}
