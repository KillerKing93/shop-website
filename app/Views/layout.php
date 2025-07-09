<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!empty($settings->logo_url) && file_exists(FCPATH . ltrim($settings->logo_url, '/'))): ?>
        <link rel="icon" type="image/png" href="<?= base_url($settings->logo_url) . '?v=' . time() ?>">
    <?php else: ?>
        <link rel="icon" type="image/png" href="<?= base_url('favicon.ico') ?>">
    <?php endif; ?>
    <title><?= esc($title ?? $settings->site_name) ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-brand img {
            max-height: 40px;
        }

        .hero-section {
            background-size: cover;
            background-position: center;
            color: white;
            padding: 10rem 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        }

        .product-card {
            transition: transform .2s;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .footer {
            background-color: #343a40;
            color: white;
            margin-top: auto;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">
                <?php if (!empty($settings->logo_url)):
                    $logoPath = FCPATH . ltrim($settings->logo_url, '/');
                    if (file_exists($logoPath)) : ?>
                        <img src="<?= base_url($settings->logo_url) . '?v=' . time() ?>" alt="Logo">
                    <?php else: ?>
                        <span style="color:red;font-size:12px;">Logo file not found: <?= $logoPath ?></span>
                <?php endif;
                endif; ?>
                <?= esc($settings->site_name) ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= esc(session()->get('username') ?? 'User') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?= site_url('admin/logout') ?>">Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <!-- Tambahkan link lain di sini jika perlu -->
                </ul>
            </div>
        </div>
    </nav>

    <main id="main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="footer py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0"><?= esc($settings->footer_text) ?></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Menampilkan notifikasi dari session
        <?php if (session()->getFlashdata('message')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '<?= session()->getFlashdata('message') ?>',
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?= session()->getFlashdata('error') ?>',
            });
        <?php endif; ?>
    </script>

    <!-- Page specific scripts -->
    <?= $this->renderSection('scripts') ?>

    <script>
        // Sticky footer only if content is short
        function adjustFooter() {
            const main = document.getElementById('main-content');
            const footer = document.querySelector('.footer');
            const body = document.body;
            // Reset
            body.style.display = '';
            body.style.flexDirection = '';
            main.style.flex = '';
            footer.style.marginTop = '';
            // Check if content height < viewport
            if (document.body.scrollHeight <= window.innerHeight) {
                body.style.display = 'flex';
                body.style.flexDirection = 'column';
                main.style.flex = '1 0 auto';
                footer.style.marginTop = 'auto';
            }
        }
        window.addEventListener('load', adjustFooter);
        window.addEventListener('resize', adjustFooter);
    </script>

    <script>
        // Keranjang icon pakai assets lokal (tidak diubah)
        document.addEventListener('DOMContentLoaded', function() {
            var cartBtn = document.getElementById('cartBtn');
            if (cartBtn) {
                function updateCartIcon() {
                    var cart = [];
                    try {
                        cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    } catch (e) {}
                    var iconEmpty = `<img src="<?= base_url('assets/icon/shopping-cart.png') ?>" alt="Keranjang Kosong" style="width:28px;height:28px;vertical-align:middle;">`;
                    var iconFilled = `<img src="<?= base_url('assets/icon/shopping-cart-filled.png') ?>" alt="Keranjang Berisi" style="width:28px;height:28px;vertical-align:middle;">`;
                    cartBtn.innerHTML = (cart && cart.length > 0 ? iconFilled : iconEmpty) + '<span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' + (cart && cart.length ? cart.reduce((a, b) => a + b.qty, 0) : 0) + '</span>';
                }
                updateCartIcon();
                window.addEventListener('storage', updateCartIcon);
                setInterval(updateCartIcon, 1000); // fallback jika cart berubah tanpa event
            }
        });
    </script>

</body>

</html>