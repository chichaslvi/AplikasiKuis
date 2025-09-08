<?php

namespace App\Controllers;

class SoalController extends BaseController
{
    public function index()
    {
        return view('admin/soal/index'); 
    }
    public function create()
    {
        // tampilkan form tambah soal
        return view('admin/soal/create');
    }
}
