<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\KuisModel;

class Dashboard extends BaseController
{
    public function admin()
    {
        $userModel = new UserModel();

        $data = [
            'countAdmin'    => $userModel->where('role', 'admin')->countAllResults(),
            'countReviewer' => $userModel->where('role', 'reviewer')->countAllResults(),
            'countAgent'    => $userModel->where('role', 'agent')->countAllResults(),
        ];

        return view('admin/dashboard', $data);
    }

    public function reviewer()
    {
        $userModel = new UserModel();
        $kuisModel = new KuisModel();

        $data = [
            'countAdmin'    => $userModel->where('role', 'admin')->countAllResults(),
            'countReviewer' => $userModel->where('role', 'reviewer')->countAllResults(),
            'countAgent'    => $userModel->where('role', 'agent')->countAllResults(),
            'countKuis'     => $kuisModel->countAllResults(),
        ];

        return view('reviewer/dashboard', $data);
    }

    public function agent()
    {
        $userModel = new UserModel();

        $data = [
            'countAgent'    => $userModel->where('role', 'agent')->countAllResults(),
            'countReviewer' => $userModel->where('role', 'reviewer')->countAllResults(),
            'countAdmin'    => $userModel->where('role', 'admin')->countAllResults(),
        ];

        return view('agent/dashboard', $data);
    }
}
