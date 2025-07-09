<?php

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\UserModel;
use Config\Database;

class Setup extends BaseController
{
    protected $userModel;
    protected $forge;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->forge = Database::forge();
    }

    public function index()
    {
        $isSetupComplete = false;
        try {
            $db = \Config\Database::connect();
            // Cek dulu apakah tabel 'users' ada
            if ($db->tableExists('users')) {
                // Jika tabel ada, baru hitung isinya
                if ($this->userModel->countAllResults() > 0) {
                    $isSetupComplete = true;
                }
            }
        } catch (\Throwable $e) {
            // Jika terjadi error apapun, anggap setup belum selesai.
            $isSetupComplete = false;
        }

        if ($isSetupComplete) {
            // Mode 2: Jika sudah ada admin, tampilkan halaman reset.
            return $this->resetPage();
        } else {
            // Mode 1: Jika belum, tampilkan form setup awal.
            return $this->initialSetup();
        }
    }

    private function initialSetup()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'site_name' => 'required',
                'contact_whatsapp' => 'required',
                'admin_username' => 'required|min_length[4]',
                'admin_password' => 'required|min_length[8]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            try {
                $migrate = \Config\Services::migrations();
                $migrate->latest();
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Gagal menjalankan migrasi database: ' . $e->getMessage());
            }

            // Simpan Konfigurasi
            $settingModel = new SettingModel();
            $configData = [
                'site_name' => $this->request->getPost('site_name'),
                'site_description' => 'Website penjualan online Anda.',
                'logo_url' => '',
                'hero_title' => 'Selamat Datang di ' . $this->request->getPost('site_name'),
                'hero_subtitle' => 'Temukan produk terbaik kami.',
                'hero_image_url' => '',
                'contact_whatsapp' => $this->request->getPost('contact_whatsapp'),
                'footer_text' => '© ' . date('Y') . ' ' . $this->request->getPost('site_name'),
            ];
            $settingModel->saveWebsiteConfig($configData);

            // Simpan Admin
            $userData = [
                'username' => $this->request->getPost('admin_username'),
                'password_hash' => password_hash($this->request->getPost('admin_password'), PASSWORD_DEFAULT),
            ];
            $this->userModel->insert($userData);

            return redirect()->to('/admin/login')->with('message', 'Setup berhasil! Silakan login.');
        }

        return view('setup');
    }

    private function resetPage()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/admin/login')->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        $data = [
            'settings' => $this->settings,
            'title'    => 'Reset Konfigurasi Website'
        ];

        return view('admin/reset_setup', $data);
    }

    public function reset()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/admin/login');
        }

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->back();
        }

        $this->forge->dropTable('products', true);
        $this->forge->dropTable('settings', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('migrations', true);

        session()->destroy();

        $dbFile = WRITEPATH . 'database/website.db';
        if (file_exists($dbFile)) {
            unlink($dbFile);
        }

        return redirect()->to('/setup')->with('message', 'Website berhasil direset. Silakan lakukan setup ulang.');
    }
}
