<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\UserModel;

class Home extends BaseController
{
    public function index()
    {
        // ================= LOGIKA BARU DENGAN PERBAIKAN =================
        try {
            // Cek apakah ada user di database.
            $userModel = new UserModel();
            if ($userModel->countAllResults() === 0) {
                // Jika tidak ada user, arahkan ke halaman setup.
                return redirect()->to('/setup');
            }
        } catch (\Throwable $e) {
            // Jika terjadi error (misalnya tabel belum ada),
            // tetap arahkan ke halaman setup.
            return redirect()->to('/setup');
        }
        // ===============================================================

        $productModel = new ProductModel();
        $settingModel = new \App\Models\SettingModel();
        $data = [
            'settings' => $settingModel->getWebsiteConfig(),
            'products' => $productModel->orderBy('created_at', 'DESC')->findAll(8)
        ];
        return view('home', $data);
    }

    public function product($slug)
    {
        $productModel = new ProductModel();
        $product = $productModel->where('slug', $slug)->first();

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $settingModel = new \App\Models\SettingModel();
        $data = [
            'settings' => $settingModel->getWebsiteConfig(),
            'product' => $product
        ];
        return view('product_detail', $data);
    }
}
