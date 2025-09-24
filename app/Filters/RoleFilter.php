<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // cek apakah user sudah login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $role = strtolower($session->get('role'));

        // kalau filter dipasang tapi role tidak sesuai
        if (!in_array($role, $arguments)) {
            switch ($role) {
                case 'admin':
                    return redirect()->to('/admin/dashboard');
                case 'reviewer':
                    return redirect()->to('/reviewer/dashboard');
                case 'agent':
                    return redirect()->to('/agent/dashboard');
                default:
                    return redirect()->to('/auth/login')->with('error', 'Role tidak dikenal');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // kosong
    }
}
