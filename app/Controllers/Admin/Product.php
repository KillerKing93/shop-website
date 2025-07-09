<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class Product extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $settingModel = new \App\Models\SettingModel();
        $data = [
            'settings' => $settingModel->getWebsiteConfig(),
            'title'    => 'Manajemen Produk',
            'products' => $productModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('admin/products/index', $data);
    }

    public function new()
    {
        $settingModel = new \App\Models\SettingModel();
        $data = [
            'settings' => $settingModel->getWebsiteConfig(),
            'title'    => 'Tambah Produk Baru',
            'validation' => \Config\Services::validation()
        ];
        return view('admin/products/form', $data);
    }

    public function create()
    {
        $productModel = new ProductModel();
        $settingModel = new \App\Models\SettingModel();
        $settings = $settingModel->getWebsiteConfig();

        // Validasi
        $rules = [
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'thumbnail' => 'uploaded[thumbnail]|max_size[thumbnail,2048]|is_image[thumbnail]',
            'gallery.*' => 'max_size[gallery,2048]|is_image[gallery]'
        ];
        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Proses upload thumbnail
        $thumbFile = $this->request->getFile('thumbnail');
        $thumbName = $thumbFile->getRandomName();
        $thumbFile->move(ROOTPATH . 'public/uploads/products', $thumbName);

        // Proses upload gallery
        $galleryPaths = [];
        $galleryFiles = $this->request->getFiles();
        if ($galleryFiles && isset($galleryFiles['gallery'])) {
            foreach ($galleryFiles['gallery'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $galleryName = $file->getRandomName();
                    $file->move(ROOTPATH . 'public/uploads/products', $galleryName);
                    $galleryPaths[] = '/uploads/products/' . $galleryName;
                }
            }
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'price' => $this->request->getPost('price'),
            'description' => $this->request->getPost('description'),
            'tags' => $this->request->getPost('tags'),
            'seller_whatsapp' => $settings->contact_whatsapp,
            'thumbnail' => '/uploads/products/' . $thumbName,
            'gallery' => json_encode($galleryPaths)
        ];

        $productModel->insert($data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Produk berhasil ditambahkan!'
            ]);
        }

        return redirect()->to('/admin/products')->with('message', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $productModel = new ProductModel();
        $settingModel = new \App\Models\SettingModel();
        $data = [
            'settings' => $settingModel->getWebsiteConfig(),
            'title'    => 'Edit Produk',
            'product'  => $productModel->find($id),
            'validation' => \Config\Services::validation()
        ];

        if (empty($data['product'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan: ' . $id);
        }

        return view('admin/products/form', $data);
    }

    public function update($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);
        $settingModel = new \App\Models\SettingModel();
        $settings = $settingModel->getWebsiteConfig();

        // Validasi
        $rules = [
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'thumbnail' => 'max_size[thumbnail,2048]|is_image[thumbnail]',
            'gallery.*' => 'max_size[gallery,2048]|is_image[gallery]'
        ];
        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'price' => $this->request->getPost('price'),
            'description' => $this->request->getPost('description'),
            'tags' => $this->request->getPost('tags'),
        ];

        // Proses update thumbnail
        $thumbFile = $this->request->getFile('thumbnail');
        if ($thumbFile && $thumbFile->isValid() && !$thumbFile->hasMoved()) {
            // Hapus file lama
            if ($product->thumbnail && file_exists(ROOTPATH . 'public' . $product->thumbnail)) {
                unlink(ROOTPATH . 'public' . $product->thumbnail);
            }
            $thumbName = $thumbFile->getRandomName();
            $thumbFile->move(ROOTPATH . 'public/uploads/products', $thumbName);
            $data['thumbnail'] = '/uploads/products/' . $thumbName;
        }

        // Proses update gallery
        $galleryFiles = $this->request->getFiles();
        if ($galleryFiles && isset($galleryFiles['gallery']) && !empty($galleryFiles['gallery'][0]->getName())) {
            // Ambil galeri lama
            $oldGallery = json_decode($product->gallery, true) ?: [];
            $galleryPaths = $oldGallery;
            foreach ($galleryFiles['gallery'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $galleryName = $file->getRandomName();
                    $file->move(ROOTPATH . 'public/uploads/products', $galleryName);
                    $galleryPaths[] = '/uploads/products/' . $galleryName;
                }
            }
            $data['gallery'] = json_encode($galleryPaths);
        }

        $productModel->update($id, $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Produk diperbarui!'
            ]);
        }

        return redirect()->to('/admin/products')->with('message', 'Produk berhasil diperbarui.');
    }

    public function delete($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);
        $settingModel = new \App\Models\SettingModel();
        $settings = $settingModel->getWebsiteConfig();

        if ($product) {
            // Hapus thumbnail
            if ($product->thumbnail && file_exists(ROOTPATH . 'public' . $product->thumbnail)) {
                unlink(ROOTPATH . 'public' . $product->thumbnail);
            }
            // Hapus gallery
            $gallery = json_decode($product->gallery);
            if (is_array($gallery)) {
                foreach ($gallery as $image) {
                    if (file_exists(ROOTPATH . 'public' . $image)) {
                        unlink(ROOTPATH . 'public' . $image);
                    }
                }
            }
            $productModel->delete($id);
            return redirect()->to('/admin/products')->with('message', 'Produk berhasil dihapus.');
        } else {
            return redirect()->to('/admin/products')->with('error', 'Produk tidak ditemukan.');
        }
    }

    /**
     * Hapus satu gambar dari galeri produk (AJAX)
     */
    public function deleteGalleryImage($productId)
    {
        $imageUrl = $this->request->getPost('image');
        if (!$imageUrl) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gambar tidak ditemukan.'
            ]);
        }
        $productModel = new ProductModel();
        $product = $productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan.'
            ]);
        }
        $gallery = json_decode($product->gallery, true) ?: [];
        $newGallery = array_filter($gallery, function ($img) use ($imageUrl) {
            return $img !== $imageUrl;
        });
        // Hapus file fisik
        $filePath = ROOTPATH . 'public' . $imageUrl;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        // Update database
        $productModel->update($productId, [
            'gallery' => json_encode(array_values($newGallery))
        ]);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Gambar galeri dihapus.'
        ]);
    }

    /**
     * Update urutan galeri (opsional, jika frontend mendukung drag & drop)
     */
    public function updateGallery($productId)
    {
        $gallery = $this->request->getPost('gallery'); // array of image URLs
        if (!is_array($gallery)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data galeri tidak valid.'
            ]);
        }
        $productModel = new ProductModel();
        $product = $productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan.'
            ]);
        }
        $productModel->update($productId, [
            'gallery' => json_encode($gallery)
        ]);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Galeri diperbarui.'
        ]);
    }
}
