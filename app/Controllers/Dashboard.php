<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role');

        if ($role == 'admin') return redirect()->to('/Admin/dashboard');
        if ($role == 'petugas') return redirect()->to('/Petugas/dashboard');
        if ($role == 'user') return redirect()->to('/User/dashboard');

        return redirect()->to('/login');
    }
}
