<?php

namespace App\Controllers;

class RoleController extends BaseController
{
    public function index()
    {
        return view('admin/roles/index'); 
    }
}
