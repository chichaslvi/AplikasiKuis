<?php

namespace App\Controllers\Reviewer;

use App\Controllers\BaseController;
use App\Models\KuisModel;
use App\Models\HasilKuisModel;
use App\Models\UserModel;
use Dompdf\Dompdf;   // ✅ ini benar
use Dompdf\Options;  // ✅ ini benar

class ReportController extends BaseController
{
    protected $kuisModel;
    protected $hasilModel;
    protected $UserModel;
    protected $db;

    public function __construct()
    {
        $this->kuisModel  = new KuisModel();
        $this->hasilModel = new HasilKuisModel();
        $this->UserModel  = new UserModel();
        $this->db         = \Config\Database::connect();
    }


    // 📌 Halaman daftar kuis
   public function index()
{
    // Ambil semua kuis kecuali yang status = draft
    $data['kuis'] = $this->kuisModel
        ->where('status !=', 'draft')
        ->orderBy('tanggal', 'DESC')
        ->findAll();

    // Ambil semua user yang role = 'team_leader' untuk dropdown filter
    $teamLeaders = $this->UserModel
        ->where('role', 'team_leader')
        ->findAll();
    $data['teamLeaders'] = $teamLeaders;

    return view('reviewer/report/index', $data);
}



    // 📌 Detail hasil kuis per peserta
    public function detail($id)
{
    $teamLeaderId = $this->request->getGet('team_leader_id');

    $query = $this->hasilModel
    ->select('
        agent.nama as nama_agent,
        kategori_agent.nama_kategori as kategori_agent,
        team_leader.nama as nama_tl,
        kuis_hasil.total_skor,
        kuis_hasil.jumlah_pengerjaan
    ')
    ->join('users as agent', 'agent.id = kuis_hasil.id_user')
    ->join('kategori_agent', 'kategori_agent.id_kategori = agent.kategori_agent_id', 'left')
    ->join('team_leader', 'team_leader.id = agent.team_leader_id', 'left')
    ->where('kuis_hasil.id_kuis', $id);

if (!empty($teamLeaderId)) {
    $query->where('agent.team_leader_id', $teamLeaderId); 
}


    $peserta = $query->findAll();

   $db = \Config\Database::connect();  
$teamLeaders = $db->table('team_leader')->get()->getResultArray();


    $data = [
        'peserta'      => $peserta,
        'teamLeaders'  => $teamLeaders,
        'selectedTL'   => $teamLeaderId,
        'id_kuis'      => $id,
        'detail'       => $this->kuisModel->find($id)
    ];

    return view('reviewer/report/detail', $data);
}

public function download($id)
{
    $teamLeaderId = $this->request->getGet('team_leader_id');

    $query = $this->hasilModel
        ->select('
            agent.nama as nama_agent,
            kategori_agent.nama_kategori as kategori_agent,
            team_leader.nama as nama_tl,
            kuis_hasil.total_skor,
            kuis_hasil.jumlah_pengerjaan
        ')
        ->join('users as agent', 'agent.id = kuis_hasil.id_user')
        ->join('kategori_agent', 'kategori_agent.id_kategori = agent.kategori_agent_id', 'left')
        ->join('team_leader', 'team_leader.id = agent.team_leader_id', 'left')
        ->where('kuis_hasil.id_kuis', $id);

    if ($teamLeaderId) {
        $query->where('agent.team_leader_id', $teamLeaderId);
    }

    $peserta = $query->findAll();

    $db = \Config\Database::connect();
    $teamLeaders = $db->table('team_leader')->get()->getResultArray();

    $data = [
        'peserta'      => $peserta,
        'teamLeaders'  => $teamLeaders,
        'selectedTL'   => $teamLeaderId,
        'id_kuis'      => $id,
        'detail'       => $this->kuisModel->find($id)
    ];

    // Render ke view khusus download (HTML)
    $html = view('reviewer/report/download', $data);

    // Panggil Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output file PDF
    $dompdf->stream("laporan_kuis_{$id}.pdf", ["Attachment" => true]);
}


}