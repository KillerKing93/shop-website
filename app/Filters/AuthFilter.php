<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika session 'isLoggedIn' tidak ada atau false
        if (!session()->get('isLoggedIn')) {
            // Simpan URL yang coba diakses
            session()->set('redirect_url', current_url());
            return redirect()->to('/admin/login')
                ->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request
    }
}
