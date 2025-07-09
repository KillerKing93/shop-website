<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <form action="<?= isset($product) ? site_url('admin/products/update/' . $product->id) : site_url('admin/products/create') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="name" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $product->name ?? '') ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="price" name="price" value="<?= old('price', $product->price ?? '') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags (pisahkan dengan koma)</label>
                        <input type="text" class="form-control" id="tags" name="tags" value="<?= old('tags', $product->tags ?? '') ?>" placeholder="baju, pria, katun">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi Produk</label>
                <textarea class="form-control summernote" id="description" name="description" rows="5"><?= old('description', $product->description ?? '') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail (Gambar Utama)</label>
                        <input class="form-control" type="file" id="thumbnail" name="thumbnail" <?= isset($product) ? '' : 'required' ?>>
                        <?php if (isset($product->thumbnail)): ?>
                            <img src="<?= base_url($product->thumbnail) ?>" alt="Thumbnail" class="mt-2" style="max-width: 150px;">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="gallery" class="form-label">Galeri Gambar (bisa pilih banyak)</label>
                        <input class="form-control" type="file" id="gallery" name="gallery[]" multiple>
                        <?php if (isset($product->gallery)): ?>
                            <div class="mt-2 d-flex flex-wrap">
                                <?php $gallery = json_decode($product->gallery); ?>
                                <?php if (is_array($gallery)): foreach ($gallery as $img): ?>
                                        <img src="<?= base_url($img) ?>" alt="Gallery" class="me-2 mb-2" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                <?php endforeach;
                                endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan Produk</button>
            <a href="<?= site_url('admin/products') ?>" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>