<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Selamat Datang, <?= esc(session()->get('username')) ?>!</h5>
        <p class="card-text">Anda berada di halaman dashboard admin. Gunakan menu di sebelah kiri untuk mengelola website Anda.</p>
    </div>
</div>

<?= $this->endSection() ?>