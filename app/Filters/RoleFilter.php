<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $roles = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if ($roles && !in_array(session()->get('role'), $roles)) {
            return redirect()->to('/blocked');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
