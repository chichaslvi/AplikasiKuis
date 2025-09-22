<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Agent extends BaseController
{
    public function dashboard()
    {
        // tampilkan view dashboard agent
        return view('agent/dashboard');
    }

    public function soal()
    {
        // tampilkan view soal agent
        return view('agent/soal');
    }
}
