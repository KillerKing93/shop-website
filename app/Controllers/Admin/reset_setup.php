<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="card border-danger">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Peringatan Keras!</h5>
    </div>
    <div class="card-body">
        <p class="card-text">
            Anda akan melakukan reset total pada website ini. Tindakan ini akan:
        </p>
        <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item list-group-item-danger">Menghapus **SEMUA** produk.</li>
            <li class="list-group-item list-group-item-danger">Menghapus **SEMUA** konfigurasi website.</li>
            <li class="list-group-item list-group-item-danger">Menghapus **SEMUA** akun admin.</li>
            <li class="list-group-item list-group-item-danger">Mengembalikan website ke kondisi awal (memerlukan setup ulang).</li>
        </ul>
        <p>
            Tindakan ini <strong>TIDAK DAPAT DIBATALKAN</strong>. Pastikan Anda benar-benar yakin sebelum melanjutkan.
        </p>

        <form id="reset-form" action="<?= site_url('setup/reset') ?>" method="POST">
            <?= csrf_field() ?>
            <button type="button" id="reset-button" class="btn btn-danger w-100">Saya Mengerti dan Ingin Mereset Website</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>