<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['key', 'value'];

    /**
     * Mengambil semua konfigurasi dan mengembalikannya sebagai objek.
     * Kunci utama adalah 'website_config'.
     *
     * @return object|null
     */
    public function getWebsiteConfig()
    {
        $setting = $this->where('key', 'website_config')->first();

        if ($setting) {
            return json_decode($setting->value);
        }

        // Mengembalikan objek kosong jika tidak ada konfigurasi
        return (object) [
            'site_name' => 'Website Belum Dikonfigurasi',
            'site_description' => '',
            'logo_url' => '',
            'hero_title' => 'Selamat Datang',
            'hero_subtitle' => 'Silakan konfigurasikan website Anda melalui halaman setup.',
            'hero_image_url' => '',
            'contact_whatsapp' => '',
            'footer_text' => '© ' . date('Y'),
        ];
    }

    /**
     * Menyimpan atau memperbarui konfigurasi website.
     *
     * @param array $data
     * @return bool
     */
    public function saveWebsiteConfig(array $data)
    {
        $existing = $this->where('key', 'website_config')->first();
        $jsonData = json_encode($data);

        if ($existing) {
            // PATCH: update berdasarkan key, bukan id
            return $this->where('key', 'website_config')->set(['value' => $jsonData])->update();
        } else {
            return $this->insert([
                'key'   => 'website_config',
                'value' => $jsonData,
            ]);
        }
    }
}
