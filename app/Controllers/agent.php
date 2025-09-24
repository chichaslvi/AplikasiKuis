<?php 
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\SoalModel;

class Agent extends BaseController
{
    protected $kuisModel;
    protected $soalModel;

    public function __construct()
    {
        $this->kuisModel = new KuisModel();
        $this->soalModel = new SoalModel();
    }

    /**
     * Dashboard Agent
     */
    public function dashboard()
    {
        // Ambil kuis yang tersedia untuk agent (upcoming & active)
        $data['kuis'] = $this->kuisModel->getAvailableKuisForAgent();

        return view('agent/dashboard', $data);
    }

    /**
     * Detail Kuis
     */
    public function detailKuis($id)
    {
        $kuis = $this->kuisModel->getKuisByIdWithKategori($id);
        if (!$kuis) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Kuis tidak ditemukan");
        }

        return view('agent/detail_kuis', ['kuis' => $kuis]);
    }

    /**
     * Soal Kuis
     */
    public function soal($id_kuis = null)
    {
        // Bisa ambil dari parameter URL atau query string (?id=1)
        if ($id_kuis === null) {
            $id_kuis = $this->request->getGet('id');
        }

        if (!$id_kuis) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("ID Kuis tidak ditemukan");
        }

        // Ambil data kuis
        $kuis = $this->kuisModel->getKuisByIdWithKategori($id_kuis);
        if (!$kuis) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Kuis tidak ditemukan");
        }

        // Ambil soal berdasarkan id_kuis
        $soal = $this->soalModel->where('id_kuis', $id_kuis)->findAll();

        return view('agent/soal', [
            'kuis' => $kuis,
            'soal' => $soal
        ]);
    }
}
