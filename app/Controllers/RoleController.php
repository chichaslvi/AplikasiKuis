<?php

namespace App\Controllers;

use App\Models\KategoriAgentModel;
use App\Models\TeamLeaderModel;

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
    // NONAKTIFKAN
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

    // =============================
    // AKTIFKAN
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

    // ✅ ALIAS supaya route `activateTeam` juga jalan
    public function activateTeam($id)
    {
        return $this->aktifkanTeam($id);
    }

    // ✅ ALIAS supaya route `activateKategori` juga jalan
    public function activateKategori($id)
    {
        return $this->aktifkanKategori($id);
    }

    // =============================
    // HAPUS PERMANEN
    // =============================

    public function destroyKategori($id)
    {
        $kategoriModel = new KategoriAgentModel();
        $kategori = $kategoriModel->find($id);

        if (!$kategori) {
            return redirect()->to('/admin/roles')->with('error', 'Kategori tidak ditemukan');
        }

        $kategoriModel->delete($id);
        return redirect()->to('/admin/roles')->with('success', 'Kategori berhasil dihapus permanen');
    }

    public function destroyTeam($id)
    {
        $teamModel = new TeamLeaderModel();
        $team = $teamModel->find($id);

        if (!$team) {
            return redirect()->to('/admin/roles')->with('error', 'Team Leader tidak ditemukan');
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
