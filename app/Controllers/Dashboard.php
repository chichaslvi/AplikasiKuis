<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function admin()
    {
        // Arahkan ke view: app/Views/dashboard/admin.php
        return view('admin/dashboard');
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
