<?php

namespace App\Controllers;

use App\Models\UserModel; // ✅ tambahkan ini biar bisa pakai UserModel

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

        // Arahkan ke view: app/Views/dashboard/admin.php
        return view('admin/dashboard', $data);
    }

    public function reviewer()
    {
        // Arahkan ke view: app/Views/dashboard/reviewer.php
        return view('reviewer/dashboard');
    }

    public function agent()
    {
        // Arahkan ke view: app/Views/dashboard/agent.php
        return view('agent/dashboard');
    }
}
