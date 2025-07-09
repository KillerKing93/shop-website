<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'price',
        'tags',
        'thumbnail',
        'gallery',
        'seller_whatsapp'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Callbacks
    protected $beforeInsert = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        if (isset($data['data']['name'])) {
            $slug = url_title($data['data']['name'], '-', true);
            $data['data']['slug'] = $slug;
        }
        return $data;
    }
}
