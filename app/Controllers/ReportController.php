<?php
namespace App\Controllers;

class ReportController extends BaseController
{
    public function index()
    {
        return view('admin/report/index'); 
    }
}
