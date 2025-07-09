<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'            => 'Produk Contoh 1',
                'slug'            => 'produk-contoh-1',
                'description'     => '<p>Ini adalah deskripsi <strong>lengkap</strong> dari produk contoh pertama. Dibuat menggunakan editor teks.</p>',
                'price'           => '150000.00',
                'tags'            => 'baju,pria,katun',
                'thumbnail'       => '/uploads/products/sample-1-thumb.jpg',
                'gallery'         => json_encode([
                    '/uploads/products/sample-1-gallery-1.jpg',
                    '/uploads/products/sample-1-gallery-2.jpg'
                ]),
                'seller_whatsapp' => '6281234567890',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'name'            => 'Produk Contoh 2',
                'slug'            => 'produk-contoh-2',
                'description'     => '<p>Ini adalah deskripsi produk kedua. <em>Kualitas terjamin!</em></p>',
                'price'           => '250000.00',
                'tags'            => 'sepatu,wanita,kulit',
                'thumbnail'       => '/uploads/products/sample-2-thumb.jpg',
                'gallery'         => json_encode([]),
                'seller_whatsapp' => '6281234567890',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('products')->insertBatch($data);
    }
}
