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
            height: 100vh;
            top: 0;
            left: 0;
            transition: margin-left 0.3s, padding-top 0.2s;
            padding-top: 40px;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .sidebar h4 {
            margin-top: 60px;
            margin-bottom: 1.5rem;
            transition: margin-top 0.2s;
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
            padding-top: 64px;
        }

        .footer-admin {
            background-color: #343a40;
            color: white;
            margin-top: auto;
            text-align: center;
            padding: 1rem 0;
            margin-left: 250px;
            transition: margin-left 0.3s;
        }

        .sidebar-collapsed .sidebar {
            margin-left: -250px;
        }

        .sidebar-collapsed .content {
            margin-left: 0;
            width: 100%;
        }

        .sidebar-toggle-btn {
            position: fixed;
            top: 18px;
            left: 18px;
            z-index: 1101;
            background: #343a40;
            color: #ffc107;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: background 0.2s, color 0.2s;
        }

        .sidebar-toggle-btn:focus,
        .sidebar-toggle-btn:hover {
            color: #ffc107;
            background: #495057;
            outline: 2px solid #ffc107;
        }

        body.sidebar-collapsed .sidebar-toggle-btn {
            background: #343a40;
            color: #ffc107;
        }

        body.sidebar-collapsed .sidebar h4 {
            margin-top: 40px;
        }

        .footer-admin {
            background-color: #343a40;
            color: white;
            margin-top: auto;
            text-align: center;
            padding: 1rem 0;
            margin-left: 250px;
            transition: margin-left 0.3s;
        }

        body.sidebar-collapsed .footer-admin {
            margin-left: 0;
        }

        @media (max-width: 991px) {
            .sidebar {
                width: 200px;
                padding-top: 40px;
            }

            .sidebar-collapsed .sidebar {
                margin-left: -200px;
            }

            .content {
                margin-left: 200px;
                width: calc(100% - 200px);
                padding-top: 72px;
            }

            .sidebar-collapsed .content {
                margin-left: 0;
                width: 100%;
            }

            .footer-admin {
                margin-left: 200px;
            }

            body.sidebar-collapsed .footer-admin {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle Sidebar" type="button">
        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
            <rect width="16" height="2" y="2" rx="1" />
            <rect width="16" height="2" y="7" rx="1" />
            <rect width="16" height="2" y="12" rx="1" />
        </svg>
    </button>
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
            console.log('jQuery ready, mencoba inisialisasi Summernote...');
            if ($('.summernote').length) {
                console.log('Elemen .summernote ditemukan:', $('.summernote').length);
            } else {
                console.warn('Tidak ada elemen .summernote di halaman ini!');
            }
            if (typeof $.fn.summernote === 'undefined') {
                console.error('Plugin Summernote TIDAK TERDETEKSI! Pastikan summernote-bs5.min.js termuat.');
                // alert('Gagal: Plugin Summernote tidak terdeteksi!\nCek koneksi internet atau pastikan CDN summernote-bs5.min.js termuat.');
                return;
            }
            try {
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
                console.log('Inisialisasi Summernote berhasil!');
            } catch (e) {
                console.error('Gagal inisialisasi Summernote:', e);
                alert('Gagal inisialisasi Summernote: ' + e);
            }

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
    <script>
        // Sidebar toggle (vanilla JS, always works, no nested DOMContentLoaded)
        var btn = document.getElementById('sidebarToggle');

        function setSidebarDefault() {
            if (window.innerWidth <= 991) {
                document.body.classList.add('sidebar-collapsed');
            } else {
                document.body.classList.remove('sidebar-collapsed');
            }
        }
        setSidebarDefault();
        window.addEventListener('resize', setSidebarDefault);
        if (btn) {
            btn.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }
    </script>
</body>

</html>