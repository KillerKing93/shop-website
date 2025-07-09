<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<?php $siteUrl = site_url('admin/settings'); ?>
<div class="card">
    <div class="card-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>
        <form id="settingsForm" action="<?= $siteUrl ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Nama Website</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="<?= esc($settings->site_name) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="contact_whatsapp" class="form-label">Nomor WhatsApp Penjual</label>
                        <input type="text" class="form-control" id="contact_whatsapp" name="contact_whatsapp" value="<?= esc($settings->contact_whatsapp) ?>">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="site_description" class="form-label">Deskripsi Singkat Website</label>
                <textarea class="form-control" id="site_description" name="site_description" rows="3"><?= esc($settings->site_description) ?></textarea>
            </div>

            <hr>
            <h5>Tampilan Hero Section</h5>
            <div class="mb-3">
                <label for="hero_title" class="form-label">Judul Hero</label>
                <input type="text" class="form-control" id="hero_title" name="hero_title" value="<?= esc($settings->hero_title) ?>">
            </div>
            <div class="mb-3">
                <label for="hero_subtitle" class="form-label">Sub-judul Hero</label>
                <input type="text" class="form-control" id="hero_subtitle" name="hero_subtitle" value="<?= esc($settings->hero_subtitle) ?>">
            </div>

            <hr>
            <h5>Teks Footer</h5>
            <div class="mb-3">
                <label for="footer_text" class="form-label">Teks Footer</label>
                <input type="text" class="form-control" id="footer_text" name="footer_text" value="<?= esc($settings->footer_text) ?>">
            </div>

            <hr>
            <h5>Aset Gambar</h5>
            <div class="row">
                <div class="col-md-6">
                    <form action="<?= site_url('admin/settings/upload-logo') ?>" method="POST" enctype="multipart/form-data" class="mb-3">
                        <?= csrf_field() ?>
                        <label for="logo" class="form-label">Logo Website (Max: 1MB)</label>
                        <input class="form-control" type="file" id="logo" name="logo" required>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah.</small>
                    </form>
                    <?php if (!empty($settings->logo_url)): ?>
                        <img src="<?= base_url($settings->logo_url) . '?v=' . time() ?>" alt="Logo Saat Ini" style="max-height: 50px; background: #ccc; padding: 5px; border-radius: 5px;">
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <form action="<?= site_url('admin/settings/upload-hero') ?>" method="POST" enctype="multipart/form-data" class="mb-3">
                        <?= csrf_field() ?>
                        <label for="hero_image" class="form-label">Gambar Latar Hero (Max: 2MB)</label>
                        <input class="form-control" type="file" id="hero_image" name="hero_image" required>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah.</small>
                    </form>
                    <?php if (!empty($settings->hero_image_url)): ?>
                        <img src="<?= base_url($settings->hero_image_url) . '?v=' . time() ?>" alt="Hero Saat Ini" style="max-width: 200px; border-radius: 5px;">
                    <?php endif; ?>
                </div>
            </div>

        </form>
    </div>
</div>

<hr>
<!-- Panel Ubah Password Admin -->
<div class="card mt-4">
    <div class="card-header bg-light">
        <strong>Ubah Password Admin</strong>
    </div>
    <div class="card-body">
        <form id="changePasswordForm" action="<?= site_url('admin/change-password') ?>" method="POST" autocomplete="off">
            <?= csrf_field() ?>
            <div id="changePasswordMsg"></div>
            <div class="mb-3">
                <label for="current_password" class="form-label">Password Lama</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password">
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Password</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Helper: debounce
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    const form = document.getElementById('settingsForm');
    let lastData = new FormData(form);

    function serializeForm(form) {
        const fd = new FormData(form);
        const obj = {};
        fd.forEach((v, k) => obj[k] = v);
        return JSON.stringify(obj);
    }

    // Ganti event input+debounce menjadi event blur
    form.querySelectorAll('input:not([type=file]), textarea').forEach(el => {
        el.addEventListener('blur', function() {
            const formData = new FormData(form);
            formData.delete('logo');
            formData.delete('hero_image');
            fetch('<?= $siteUrl ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update nama website di admin layout jika ada
                        const siteName = form.site_name.value;
                        document.querySelectorAll('.navbar-brand, .sidebar h4 a').forEach(el => {
                            el.innerText = siteName;
                        });
                        // Update teks footer jika ada elemen dengan id/footer
                        const footerText = form.footer_text.value;
                        const footerEl = document.getElementById('footer_text_display');
                        if (footerEl) footerEl.innerText = footerText;
                        Swal.fire({
                            icon: 'success',
                            title: 'Setting diperbaharui!',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    } else if (data.status === 'error') {
                        let msg = data.message;
                        if (typeof msg === 'object') {
                            msg = Object.values(msg).join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal update',
                            text: msg || 'Terjadi kesalahan.'
                        });
                    }
                });
        });
    });

    // Auto-upload logo
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function() {
            if (!logoInput.files.length) return;
            const formData = new FormData();
            formData.append('logo', logoInput.files[0]);
            fetch('<?= site_url('admin/settings/upload-logo') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error('HTTP ' + res.status + ': ' + text);
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.status === 'success' || (data.message && data.message.includes('berhasil'))) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Logo berhasil diupload!',
                            timer: 1200,
                            showConfirmButton: false
                        });
                        // Update preview logo (cari img setelah input logo)
                        const img = logoInput.parentElement.parentElement.querySelector('img[alt="Logo Saat Ini"]');
                        if (img) {
                            img.src = img.src.split('?')[0] + '?v=' + Date.now();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal upload logo',
                            text: (data.message || 'Terjadi kesalahan.')
                        });
                        console.log(data);
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal upload logo',
                        text: err.message
                    });
                    console.error('Upload logo error:', err);
                });
        });
    }
    // Auto-upload hero_image
    const heroInput = document.getElementById('hero_image');
    if (heroInput) {
        heroInput.addEventListener('change', function() {
            if (!heroInput.files.length) return;
            const formData = new FormData();
            formData.append('hero_image', heroInput.files[0]);
            fetch('<?= site_url('admin/settings/upload-hero') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json ? res.json() : res)
                .then(data => {
                    // SweetAlert selalu muncul
                    if (data.status === 'success' || (data.message && data.message.includes('berhasil'))) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Hero image berhasil diupload!',
                            timer: 1200,
                            showConfirmButton: false
                        });
                        // Update preview hero (cari img setelah input hero_image)
                        const img = heroInput.parentElement.parentElement.querySelector('img[alt="Hero Saat Ini"]');
                        if (img) {
                            img.src = img.src.split('?')[0] + '?v=' + Date.now();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal upload hero',
                            text: (data.message || 'Terjadi kesalahan.')
                        });
                    }
                });
        });
    }
</script>

<?= $this->endSection() ?>