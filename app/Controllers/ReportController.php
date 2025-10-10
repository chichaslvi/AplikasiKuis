<?php

namespace App\Controllers;

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
    ->orderBy('waktu_mulai', 'DESC')
    ->findAll();


    // Ambil semua user yang role = 'team_leader' untuk dropdown filter
    $teamLeaders = $this->UserModel
        ->where('role', 'team_leader')
        ->findAll();
    $data['teamLeaders'] = $teamLeaders;

    return view('admin/report/index', $data);
}



    // 📌 Detail hasil kuis per peserta
    public function detail($id)
{
    $teamLeaderId = $this->request->getGet('team_leader_id');

    $query = $this->hasilModel
    ->select('
        agent.nama AS nama_agent,
        kategori_agent.nama_kategori AS kategori_agent,
        team_leader.nama AS nama_tl,
        a.total_skor,
        a.jumlah_pengerjaan
    ')
    ->from('kuis_hasil a')
    ->join('users AS agent', 'agent.id = a.id_user')
    ->join('kategori_agent', 'kategori_agent.id_kategori = agent.kategori_agent_id', 'left')
    ->join('team_leader', 'team_leader.id = agent.team_leader_id', 'left')
    ->where('a.id_kuis', $id)
    ->where('a.id_hasil = (
        SELECT MAX(b.id_hasil)
        FROM kuis_hasil b
        WHERE b.id_user = a.id_user
          AND b.id_kuis = a.id_kuis
    )')
    ->groupBy('a.id_user')  // ✅ Tambahkan baris ini!
    ->orderBy('agent.nama', 'ASC');


    if (!empty($teamLeaderId)) {
        $query->where('agent.team_leader_id', $teamLeaderId);
    }

    $peserta = $query->findAll();

    $teamLeaders = $this->db->table('team_leader')->get()->getResultArray();

    $data = [
        'peserta' => $peserta,
        'teamLeaders' => $teamLeaders,
        'selectedTL' => $teamLeaderId,
        'id_kuis' => $id,
        'detail' => $this->kuisModel->find($id)
    ];

    return view('admin/report/detail', $data);
}


public function download($id)
{
    $teamLeaderId = $this->request->getGet('team_leader_id');

    $query = $this->hasilModel
    ->select('
        agent.nama AS nama_agent,
        kategori_agent.nama_kategori AS kategori_agent,
        team_leader.nama AS nama_tl,
        a.total_skor,
        a.jumlah_pengerjaan
    ')
    ->from('kuis_hasil a')
    ->join('users AS agent', 'agent.id = a.id_user')
    ->join('kategori_agent', 'kategori_agent.id_kategori = agent.kategori_agent_id', 'left')
    ->join('team_leader', 'team_leader.id = agent.team_leader_id', 'left')
    ->where('a.id_kuis', $id)
    ->where('a.id_hasil = (
        SELECT MAX(b.id_hasil)
        FROM kuis_hasil b
        WHERE b.id_user = a.id_user
          AND b.id_kuis = a.id_kuis
    )')
    ->groupBy('a.id_user')  // ✅ Tambahkan baris ini!
    ->orderBy('agent.nama', 'ASC');


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
    $html = view('admin/report/download', $data);

    // Panggil Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output file PDF
    $dompdf->stream("laporan_kuis_{$id}.pdf", ["Attachment" => true]);
}


}