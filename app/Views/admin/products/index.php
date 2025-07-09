<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Daftar Produk</h5>
        <!-- Tombol tambah produk tetap, tapi tidak link ke halaman lain -->
        <button id="showAddProductForm" class="btn btn-primary">Tambah Produk</button>
    </div>
    <div class="card-body">
        <!-- Search bar -->
        <div class="mb-3">
            <input type="text" id="searchProduct" class="form-control" placeholder="Cari produk...">
        </div>
        <!-- Form tambah produk (inline, hidden by default) -->
        <div id="addProductFormContainer" class="mb-4" style="display:none;">
            <form id="addProductForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add_name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="add_name" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add_price" class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="add_price" name="price" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="add_tags" class="form-label">Tags (pisahkan dengan koma)</label>
                    <input type="text" class="form-control" id="add_tags" name="tags" placeholder="baju, pria, katun">
                </div>
                <div class="mb-3">
                    <label for="add_description" class="form-label">Deskripsi Produk</label>
                    <textarea class="form-control" id="add_description" name="description" rows="3"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add_thumbnail" class="form-label">Thumbnail (Gambar Utama)</label>
                            <input class="form-control" type="file" id="add_thumbnail" name="thumbnail" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add_gallery" class="form-label">Galeri Gambar (bisa pilih banyak)</label>
                            <input class="form-control" type="file" id="add_gallery" name="gallery[]" multiple>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Simpan Produk</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr data-product-id="<?= $product->id ?>">
                                <td>
                                    <img src="<?= base_url($product->thumbnail ?: 'https://placehold.co/100x100') ?>" alt="<?= esc($product->name) ?>" style="width: 80px; height: 80px; object-fit: cover;">
                                </td>
                                <td class="product-name"><?= esc($product->name) ?></td>
                                <td class="product-price">Rp <?= number_format($product->price, 0, ',', '.') ?></td>
                                <td><?= date('d M Y', strtotime($product->created_at)) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-product-btn" data-id="<?= $product->id ?>">Edit</button>
                                    <a href="<?= site_url('admin/products/delete/' . $product->id) ?>" class="btn btn-sm btn-danger delete-button">Hapus</a>
                                </td>
                            </tr>
                            <!-- Slot form edit inline, hidden by default -->
                            <tr class="edit-form-row" id="edit-form-row-<?= $product->id ?>" style="display:none;">
                                <td colspan="5">
                                    <form class="editProductForm" data-id="<?= $product->id ?>" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Produk</label>
                                                    <input type="text" class="form-control" name="name" value="<?= esc($product->name) ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Harga</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" name="price" value="<?= esc($product->price) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tags (pisahkan dengan koma)</label>
                                            <input type="text" class="form-control" name="tags" value="<?= esc($product->tags) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi Produk</label>
                                            <textarea class="form-control summernote" name="description" rows="3"><?= esc($product->description) ?></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Thumbnail (Gambar Utama)</label>
                                                    <input class="form-control" type="file" name="thumbnail">
                                                    <?php if ($product->thumbnail): ?>
                                                        <img src="<?= base_url($product->thumbnail) ?>" alt="Thumbnail" class="mt-2" style="max-width: 150px;">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Galeri Gambar (bisa pilih banyak)</label>
                                                    <input class="form-control" type="file" name="gallery[]" multiple>
                                                    <?php $gallery = json_decode($product->gallery); ?>
                                                    <?php if (is_array($gallery)): ?>
                                                        <div class="mt-2 d-flex flex-wrap">
                                                            <?php foreach ($gallery as $img): ?>
                                                                <img src="<?= base_url($img) ?>" alt="Gallery" class="me-2 mb-2" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tidak ada tombol simpan, auto-save saat blur -->
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada produk.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JS untuk handle form tambah/edit inline dan auto-save -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Tampilkan form tambah produk
    const showAddBtn = document.getElementById('showAddProductForm');
    const addFormContainer = document.getElementById('addProductFormContainer');
    showAddBtn.addEventListener('click', function() {
        addFormContainer.style.display = addFormContainer.style.display === 'none' ? 'block' : 'none';
    });

    // Submit tambah produk via AJAX
    const addForm = document.getElementById('addProductForm');
    addForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(addForm);
        fetch('<?= site_url('admin/products/create') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json ? res.json() : res)
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Produk berhasil ditambahkan!',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    // Reset form dan reload halaman (atau update tabel via JS jika ingin lebih dinamis)
                    addForm.reset();
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal tambah produk',
                        text: data.message || 'Terjadi kesalahan.'
                    });
                }
            });
    });

    // Edit produk inline
    const editBtns = document.querySelectorAll('.edit-product-btn');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = btn.getAttribute('data-id');
            const row = document.getElementById('edit-form-row-' + id);
            const isOpen = row.style.display !== 'none';
            // Tutup semua form edit lain
            document.querySelectorAll('.edit-form-row').forEach(r => r.style.display = 'none');
            document.querySelectorAll('.edit-product-btn').forEach(b => b.innerText = 'Edit');
            if (!isOpen) {
                row.style.display = '';
                btn.innerText = 'Tutup Form';
            } else {
                row.style.display = 'none';
                btn.innerText = 'Edit';
            }
        });
    });

    // Auto-save edit produk saat blur
    const editForms = document.querySelectorAll('.editProductForm');
    editForms.forEach(form => {
        form.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('blur', function() {
                const id = form.getAttribute('data-id');
                const formData = new FormData(form);
                fetch('<?= site_url('admin/products/update/') ?>' + id, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json ? res.json() : res)
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Produk diperbarui!',
                                timer: 1000,
                                showConfirmButton: false
                            });
                            // Bisa update tampilan tabel jika ingin
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal update',
                                text: data.message || 'Terjadi kesalahan.'
                            });
                        }
                    });
            });
        });
    });

    // Search bar filter
    const searchInput = document.getElementById('searchProduct');
    searchInput.addEventListener('input', function() {
        const val = searchInput.value.toLowerCase();
        document.querySelectorAll('#productTableBody tr[data-product-id]').forEach(row => {
            const name = row.querySelector('.product-name').innerText.toLowerCase();
            row.style.display = name.includes(val) ? '' : 'none';
            // Sembunyikan juga form edit jika baris disembunyikan
            const id = row.getAttribute('data-product-id');
            const editRow = document.getElementById('edit-form-row-' + id);
            if (editRow) editRow.style.display = name.includes(val) ? editRow.style.display : 'none';
        });
    });

    // Inisialisasi Summernote dengan auto-save onBlur untuk form edit inline
    $(document).ready(function() {
        $('.edit-form-row .summernote').each(function() {
            var $textarea = $(this);
            $textarea.summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                callbacks: {
                    onBlur: function() {
                        var $form = $textarea.closest('form');
                        $textarea.val($textarea.summernote('code'));
                        const id = $form.data('id');
                        const formData = new FormData($form[0]);
                        fetch('<?= site_url('admin/products/update/') ?>' + id, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(res => res.json ? res.json() : res)
                            .then(data => {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Produk diperbarui!',
                                        timer: 1000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal update',
                                        text: data.message || 'Terjadi kesalahan.'
                                    });
                                }
                            });
                    }
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>