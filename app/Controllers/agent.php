<?php 
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\SoalModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Agent extends BaseController
{
    protected $kuisModel;
    protected $soalModel;
    protected $userModel;

    public function __construct()
    {
        $this->kuisModel = new KuisModel();
        $this->soalModel = new SoalModel();
        $this->userModel = new UserModel();
    }

    /**
     * Simple check: user harus login dan role = agent.
     * Mengembalikan RedirectResponse jika gagal, atau null jika oke.
     */
    private function ensureAgent()
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $role = strtolower($session->get('role') ?? '');
        if ($role !== 'agent') {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        return null;
    }

    /**
     * Dashboard Agent
     */
    public function dashboard()
    {
        if ($resp = $this->ensureAgent()) {
            return $resp; // redirect jika belum login / bukan agent
        }

        $userId = (int) session()->get('user_id');
        // ✅ ambil user + kategori (nama_kategori) sesuai tabel kamu
        $user = $this->userModel->getUserWithKategori($userId);

        if (!$user) {
            // user hilang di DB — logout & arahkan login lagi
            session()->destroy();
            return redirect()->to('/auth/login')->with('error', 'User tidak ditemukan, silakan login ulang.');
        }

        $data = [
            'user' => $user,
            'kuis' => $this->kuisModel->getAvailableKuisForAgent(),
        ];

        return view('agent/dashboard', $data);
    }

    /**
     * Detail Kuis
     */
    public function detailKuis($id = null)
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $id = (int) $id;
        if ($id <= 0) {
            throw PageNotFoundException::forPageNotFound("ID Kuis tidak valid");
        }

        $kuis = $this->kuisModel->getKuisByIdWithKategori($id);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound("Kuis dengan ID {$id} tidak ditemukan");
        }

        $data = [
            'user' => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
            'kuis' => $kuis
        ];

        return view('agent/detail_kuis', $data);
    }

    /**
     * Soal Kuis
     */
    public function soal($id_kuis = null)
    {
        if ($resp = $this->ensureAgent()) {
            return $resp;
        }

        $id_kuis = $id_kuis ?? $this->request->getGet('id');
        $id_kuis = (int) $id_kuis;

        if ($id_kuis <= 0) {
            throw PageNotFoundException::forPageNotFound("ID Kuis tidak valid");
        }

        $kuis = $this->kuisModel->getKuisByIdWithKategori($id_kuis);
        if (!$kuis) {
            throw PageNotFoundException::forPageNotFound("Kuis tidak ditemukan");
        }

        $soal = $this->soalModel->where('id_kuis', $id_kuis)->findAll() ?? [];

        $data = [
            'user' => $this->userModel->getUserWithKategori((int) session()->get('user_id')),
            'kuis' => $kuis,
            'soal' => $soal
        ];

        return view('agent/soal', $data);
    }
    
     public function kerjakan($id_kuis)
    {
        $soalModel = new SoalKuisModel();
        $soalList = $soalModel->where('id_kuis', $id_kuis)->findAll();

        return view('agent/kuis/kerjakan', [
            'title' => 'Kerjakan Kuis',
            'soalList' => $soalList,
        ]);
    }
}
