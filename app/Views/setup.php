<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Website Awal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f0f2f5;
        }

        .setup-card {
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>

<body>
    <div class="card shadow-lg setup-card">
        <div class="card-body p-5">
            <h2 class="card-title text-center mb-4">Setup Website Awal</h2>
            <p class="text-center text-muted mb-4">Lengkapi form di bawah ini untuk konfigurasi awal website dan membuat akun admin pertama.</p>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('setup') ?>" method="POST">
                <?= csrf_field() ?>

                <h5 class="mt-4">Informasi Website</h5>
                <div class="mb-3">
                    <label for="site_name" class="form-label">Nama Website</label>
                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?= old('site_name') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="contact_whatsapp" class="form-label">Nomor WhatsApp Penjual</label>
                    <input type="text" class="form-control" id="contact_whatsapp" name="contact_whatsapp" value="<?= old('contact_whatsapp') ?>" placeholder="Contoh: 6281234567890" required>
                </div>

                <h5 class="mt-4">Akun Admin</h5>
                <div class="mb-3">
                    <label for="admin_username" class="form-label">Username Admin</label>
                    <input type="text" class="form-control" id="admin_username" name="admin_username" value="<?= old('admin_username') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="admin_password" class="form-label">Password Admin</label>
                    <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Selesaikan Setup</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>