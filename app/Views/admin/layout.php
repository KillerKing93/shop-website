<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?> | <?= esc($settings->site_name) ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f4f7f6;
            flex-direction: column;
        }

        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            position: fixed;
            height: 100%;
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            padding: 1rem;
        }

        .sidebar a:hover,
        .sidebar .nav-link.active {
            background: #495057;
            color: white;
        }

        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 2rem;
            flex: 1 0 auto;
        }

        .footer-admin {
            background-color: #343a40;
            color: white;
            margin-top: auto;
            text-align: center;
            padding: 1rem 0;
        }
    </style>
</head>

<body>

    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-center mb-4">
            <a href="<?= site_url('/') ?>" class="text-white text-decoration-none">
                <?= esc($settings->site_name) ?>
            </a>
        </h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= (uri_string() == 'admin/dashboard') ? 'active' : '' ?>">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/products') ?>" class="nav-link <?= (strpos(uri_string(), 'admin/products') !== false) ? 'active' : '' ?>">
                    Manajemen Produk
                </a>
            </li>
            <li>
                <a href="<?= site_url('admin/settings') ?>" class="nav-link <?= (uri_string() == 'admin/settings') ? 'active' : '' ?>">
                    Pengaturan Website
                </a>
            </li>
            <li>
                <a href="<?= site_url('setup') ?>" class="nav-link text-warning <?= (uri_string() == 'setup') ? 'active' : '' ?>">
                    Reset Website
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <strong><?= esc(session()->get('username')) ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="<?= site_url('admin/logout') ?>">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="content">
        <h1 class="mb-4">
            <a href="<?= site_url('admin/dashboard') ?>" class="text-dark text-decoration-none">
                <?= esc($title ?? '') ?>
            </a>
        </h1>
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="footer-admin">
        &copy; <?= date('Y') ?> <?= esc($settings->site_name) ?> - Admin Panel
    </footer>

    <!-- jQuery (diperlukan untuk Summernote) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Summernote
            $('.summernote').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

            // Notifikasi Session
            <?php if (session()->getFlashdata('message')): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: '<?= session()->getFlashdata('message') ?>',
                    timer: 2000,
                    showConfirmButton: false
                });
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '<?= session()->getFlashdata('error') ?>'
                });
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                let errorText = '<ul>';
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    errorText += '<li><?= esc($error) ?></li>';
                <?php endforeach; ?>
                errorText += '</ul>';
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan Validasi',
                    html: errorText
                });
            <?php endif; ?>

            // Konfirmasi Hapus untuk link <a>
            $('.delete-button').on('click', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.location.href = href;
                    }
                })
            });

            // Konfirmasi untuk Tombol Reset Website
            $('#reset-button').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'ANDA YAKIN?',
                    text: "Ini adalah tindakan terakhir. Website akan kembali ke kondisi awal!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, SAYA YAKIN, RESET SEKARANG!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika dikonfirmasi, submit form reset
                        $('#reset-form').submit();
                    }
                })
            });
        });
    </script>
</body>

</html>