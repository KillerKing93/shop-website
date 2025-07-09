<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            'site_name' => 'Toko Online Keren',
            'site_description' => 'Deskripsi singkat mengenai toko online Anda.',
            'logo_url' => '/uploads/logos/default-logo.png',
            'hero_title' => 'Selamat Datang di Toko Kami!',
            'hero_subtitle' => 'Temukan produk terbaik dengan harga paling terjangkau.',
            'hero_image_url' => '/uploads/heros/default-hero.jpg',
            'contact_whatsapp' => '6281234567890',
            'footer_text' => '© ' . date('Y') . ' Toko Online Keren. All Rights Reserved.'
        ];

        $data = [
            'key'    => 'website_config',
            'value'  => json_encode($settings)
        ];

        $this->db->table('settings')->insert($data);
    }
}
