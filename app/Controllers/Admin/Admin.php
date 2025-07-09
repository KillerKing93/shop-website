<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SettingModel;

class Admin extends BaseController
{
    public function index()
    {
        // Jika sudah login, arahkan ke dashboard. Jika belum, ke halaman login.
        return (session()->get('isLoggedIn')) ? redirect()->to('/admin/dashboard') : redirect()->to('/admin/login');
    }

    public function login()
    {
        // Jika sudah login, jangan tampilkan halaman login lagi
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        return view('admin/login');
    }

    public function authenticate()
    {
        $session = session();
        $userModel = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user->password_hash)) {
            $sessionData = [
                'user_id'    => $user->id,
                'username'   => $user->username,
                'isLoggedIn' => TRUE
            ];
            $session->set($sessionData);

            // Cek jika ada URL redirect
            $redirect_url = $session->get('redirect_url') ?? '/admin/dashboard';
            $session->remove('redirect_url');

            return redirect()->to($redirect_url);
        } else {
            return redirect()->to('/admin/login')->with('error', 'Username atau Password salah.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login')->with('message', 'Anda telah berhasil logout.');
    }

    public function dashboard()
    {
        $settingModel = new \App\Models\SettingModel();
        $data = [
            'settings' => $settingModel->getWebsiteConfig(),
            'title'    => 'Dashboard'
        ];
        return view('admin/dashboard', $data);
    }

    public function settings()
    {
        $settingModel = new \App\Models\SettingModel();
        $data = [
            'settings' => $settingModel->getWebsiteConfig(),
            'title'    => 'Pengaturan Website'
        ];
        return view('admin/settings', $data);
    }

    public function updateSettings()
    {
        $settingModel = new SettingModel();
        $currentConfig = (array) $settingModel->getWebsiteConfig();

        $isAjax = $this->request->isAJAX();

        // Validasi
        $rules = [
            'site_name' => 'required',
            'contact_whatsapp' => 'required',
        ];
        // Hanya validasi file jika bukan AJAX (auto-save)
        if (!$isAjax) {
            $rules['logo'] = 'max_size[logo,1024]|is_image[logo]|mime_in[logo,image/jpg,image/jpeg,image/png]';
            $rules['hero_image'] = 'max_size[hero_image,2048]|is_image[hero_image]|mime_in[hero_image,image/jpg,image/jpeg,image/png]';
        }

        if (!$this->validate($rules)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Proses upload logo
        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $logoDir = ROOTPATH . 'public/uploads/logos';
            $logoName = $logoFile->getRandomName();
            if (!$logoFile->move($logoDir, $logoName)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal upload logo: ' . $logoFile->getErrorString()
                    ]);
                }
                return redirect()->back()->with('error', 'Gagal upload logo: ' . $logoFile->getErrorString());
            }
            $currentConfig['logo_url'] = '/uploads/logos/' . $logoName;
        } elseif ($logoFile && $logoFile->getError() !== 4 && !$logoFile->isValid()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal upload logo: ' . $logoFile->getErrorString()
                ]);
            }
            return redirect()->back()->with('error', 'Gagal upload logo: ' . $logoFile->getErrorString());
        }

        // Proses upload hero image
        $heroFile = $this->request->getFile('hero_image');
        if ($heroFile && $heroFile->isValid() && !$heroFile->hasMoved()) {
            $heroDir = ROOTPATH . 'public/uploads/heros';
            $heroName = $heroFile->getRandomName();
            if (!$heroFile->move($heroDir, $heroName)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal upload hero image: ' . $heroFile->getErrorString()
                    ]);
                }
                return redirect()->back()->with('error', 'Gagal upload hero image: ' . $heroFile->getErrorString());
            }
            $currentConfig['hero_image_url'] = '/uploads/heros/' . $heroName;
        } elseif ($heroFile && $heroFile->getError() !== 4 && !$heroFile->isValid()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal upload hero image: ' . $heroFile->getErrorString()
                ]);
            }
            return redirect()->back()->with('error', 'Gagal upload hero image: ' . $heroFile->getErrorString());
        }

        // Update data dari form
        $currentConfig['site_name'] = $this->request->getPost('site_name');
        $currentConfig['site_description'] = $this->request->getPost('site_description');
        $currentConfig['hero_title'] = $this->request->getPost('hero_title');
        $currentConfig['hero_subtitle'] = $this->request->getPost('hero_subtitle');
        $currentConfig['contact_whatsapp'] = $this->request->getPost('contact_whatsapp');
        $currentConfig['footer_text'] = $this->request->getPost('footer_text');

        $settingModel->saveWebsiteConfig($currentConfig);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pengaturan berhasil diperbaharui.'
            ]);
        }

        return redirect()->to('/admin/settings')->with('message', 'Pengaturan berhasil diperbarui.');
    }

    public function uploadLogo()
    {
        // Debug: pastikan fungsi dipanggil
        file_put_contents(WRITEPATH . 'logs/logo_upload_debug.txt', date('c') . " masuk uploadLogo()\n", FILE_APPEND);

        $settingModel = new SettingModel();
        $currentConfig = (array) $settingModel->getWebsiteConfig();

        $logoFile = $this->request->getFile('logo');
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

        if (!$logoFile) {
            return redirect()->back()->with('error', 'Tidak ada file logo yang dikirim.');
        }
        if (!$logoFile->isValid()) {
            return redirect()->back()->with('error', 'File logo tidak valid: ' . $logoFile->getErrorString());
        }
        if (!in_array($logoFile->getMimeType(), $allowedTypes)) {
            return redirect()->back()->with('error', 'Logo harus berupa gambar (jpg, jpeg, png, webp).');
        }
        if ($logoFile->getSize() > 1024 * 1024) {
            return redirect()->back()->with('error', 'Logo maksimal 1MB.');
        }

        $logoDir = ROOTPATH . 'public/uploads/logos';
        if (!is_dir($logoDir)) {
            mkdir($logoDir, 0777, true);
        }
        $logoName = $logoFile->getRandomName();
        if (!$logoFile->move($logoDir, $logoName)) {
            return redirect()->back()->with('error', 'Gagal upload logo: ' . $logoFile->getErrorString());
        }

        // Hapus logo lama jika ada dan bukan default
        if (!empty($currentConfig['logo_url']) && file_exists(ROOTPATH . 'public' . $currentConfig['logo_url'])) {
            if (basename($currentConfig['logo_url']) != 'default-logo.png') {
                @unlink(ROOTPATH . 'public' . $currentConfig['logo_url']);
            }
        }

        // Update config dan simpan ke database
        $currentConfig['logo_url'] = '/uploads/logos/' . $logoName;
        $result = $settingModel->saveWebsiteConfig($currentConfig);

        // Debug: cek hasil update
        $after = $settingModel->getWebsiteConfig();
        file_put_contents(WRITEPATH . 'logs/logo_upload_debug.txt', date('c') . " after upload: " . print_r($after, true) . "\n", FILE_APPEND);

        if (isset($after->logo_url) && $after->logo_url === $currentConfig['logo_url']) {
            return redirect()->back()->with('message', 'Logo berhasil diupload dan diperbarui: ' . htmlspecialchars($after->logo_url));
        } else {
            return redirect()->back()->with('error', 'Logo berhasil diupload, tapi path tidak terupdate di database! Cek log debug.');
        }
    }

    public function uploadHero()
    {
        $settingModel = new SettingModel();
        $currentConfig = (array) $settingModel->getWebsiteConfig();

        $heroFile = $this->request->getFile('hero_image');
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!$heroFile || !$heroFile->isValid()) {
            return redirect()->back()->with('error', 'File hero tidak valid.');
        }
        if (!in_array($heroFile->getMimeType(), $allowedTypes) || $heroFile->getSize() > 2 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Hero image harus berupa gambar (jpg, jpeg, png, webp) dan maksimal 2MB.');
        }

        $heroDir = ROOTPATH . 'public/uploads/heros';
        $heroName = $heroFile->getRandomName();
        if (!$heroFile->move($heroDir, $heroName)) {
            return redirect()->back()->with('error', 'Gagal upload hero image: ' . $heroFile->getErrorString());
        }
        // Hapus hero lama jika ada dan bukan default
        if (!empty($currentConfig['hero_image_url']) && file_exists(ROOTPATH . 'public' . $currentConfig['hero_image_url'])) {
            if (basename($currentConfig['hero_image_url']) != 'default-hero.jpg') {
                @unlink(ROOTPATH . 'public' . $currentConfig['hero_image_url']);
            }
        }
        $currentConfig['hero_image_url'] = '/uploads/heros/' . $heroName;
        $settingModel->saveWebsiteConfig($currentConfig);

        return redirect()->back()->with('message', 'Hero image berhasil diupload.');
    }
}
