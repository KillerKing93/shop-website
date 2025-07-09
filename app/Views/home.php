<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- Hero Section dengan cache buster -->
<?php
$heroPath = isset($settings->hero_image_url) ? FCPATH . ltrim($settings->hero_image_url, '/') : null;
$heroExists = $heroPath && file_exists($heroPath);
?>
<div class="hero-section text-center" style="background-image: url('<?= $heroExists ? base_url($settings->hero_image_url) . '?v=' . time() : 'https://placehold.co/1920x600/343a40/ffffff?text=Hero+Image' ?>');">
    <div class="container">
        <h1 class="display-4 fw-bold"><?= esc($settings->hero_title) ?></h1>
        <p class="lead"><?= esc($settings->hero_subtitle) ?></p>
        <?php if (!$heroExists && !empty($settings->hero_image_url)): ?>
            <span style="color:red;font-size:12px;">Hero image not found: <?= $heroPath ?></span>
        <?php endif; ?>
    </div>
</div>

<!-- Floating Cart Button (pojok kanan bawah, dengan logo SVG keranjang custom) -->
<button id="cartBtn" class="btn btn-primary position-fixed shadow-lg d-flex align-items-center justify-content-center" style="bottom:30px;right:30px;z-index:1050;border-radius:50%;width:64px;height:64px;padding:0;">
    <span style="position:relative;display:flex;align-items:center;justify-content:center;width:52px;height:52px;">
        <!-- SVG/gambar keranjang akan diisi via JS -->
    </span>
</button>

<!-- Products Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">Produk Kami</h2>
    <!-- Search bar dan filter tags -->
    <div class="row mb-4 g-2 align-items-center flex-wrap">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <input type="text" id="searchProduct" class="form-control rounded-pill px-4 py-2 shadow-sm" placeholder="Cari produk..." style="font-size:1.1rem;">
        </div>
        <div class="col-12 col-md-6 d-flex align-items-center gap-2">
            <button id="openFilterModalBtn" class="btn btn-outline-warning rounded-pill px-4 py-2 shadow-sm d-flex align-items-center" type="button">
                <i class="bi bi-funnel me-2"></i> <span>Filter</span>
            </button>
        </div>
    </div>
    <!-- Filter summary info -->
    <div id="activeFilterSummary" class="mb-3" style="display:none;"></div>

    <!-- Modal Filter Tag -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="searchTagInput" class="form-control mb-3" placeholder="Cari tag...">
                    <div id="filterTagList" class="d-flex flex-wrap gap-2" style="max-height:220px;overflow:auto;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-warning" id="applyFilterBtn">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="productList">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <?php $gallery = json_decode($product->gallery ?? '[]'); ?>
                <div class="col product-item" data-id="<?= $product->id ?>" data-name="<?= strtolower(esc($product->name)) ?>" data-tags="<?= strtolower(esc($product->tags)) ?>" data-product='<?= json_encode(["id" => $product->id, "name" => $product->name, "price" => $product->price, "thumbnail" => $product->thumbnail, "description" => $product->description, "tags" => $product->tags, "gallery" => $gallery]) ?>'>
                    <div class="card h-100 product-card d-flex flex-column">
                        <img src="<?= base_url($product->thumbnail ?: 'https://placehold.co/600x400/cccccc/ffffff?text=Gambar+Produk') ?>" class="card-img-top product-img" alt="<?= esc($product->name) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1 text-truncate" title="<?= esc($product->name) ?>"><?= esc($product->name) ?></h5>
                            <p class="card-text fw-bold text-primary mb-1" style="min-height:24px;">Rp <?= number_format($product->price, 0, ',', '.') ?></p>
                            <div class="mb-2" style="min-height:28px;">
                                <?php if (!empty($product->tags)): ?>
                                    <?php foreach (explode(',', $product->tags) as $tag): ?>
                                        <span class="badge bg-secondary me-1 mb-1"><?= trim($tag) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <p class="card-text small mb-2 product-desc-html" title="<?= strip_tags($product->description) ?>"> <?= mb_strimwidth($product->description, 0, 120, '...') ?> </p>
                            <div class="product-actions mt-auto d-flex flex-column align-items-center gap-2">
                                <div class="input-group justify-content-center" style="max-width:140px;">
                                    <button class="btn btn-outline-secondary btn-qty-minus" type="button">-</button>
                                    <input type="number" class="form-control text-center qty-input" value="1" min="1" style="max-width:50px;">
                                    <button class="btn btn-outline-secondary btn-qty-plus" type="button">+</button>
                                </div>
                                <button class="btn btn-success w-100 btn-add-cart"><i class="bi bi-cart-plus"></i> Tambahkan ke Keranjang</button>
                            </div>
                            <button class="btn btn-link btn-detail mt-2" data-id="<?= $product->id ?>">Lihat Detail</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">Belum ada produk yang tersedia saat ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail Produk (galeri carousel + zoom) -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-6 text-center">
                    <div id="detailGalleryCarouselWrapper"></div>
                </div>
                <div class="col-md-6">
                    <p id="detailDesc"></p>
                    <p class="fw-bold text-primary mb-1">Harga: <span id="detailPrice"></span></p>
                    <div class="mb-2" id="detailTags"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Zoom Gambar Galeri -->
<div class="modal fade" id="zoomImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 d-flex justify-content-center align-items-center" style="min-height:400px;">
                <img id="zoomedImage" src="" class="img-fluid rounded shadow" style="max-height:80vh;max-width:95vw;object-fit:contain;">
            </div>
        </div>
    </div>
</div>

<!-- Modal Keranjang Floating -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Keranjang Belanja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="cartItems"></div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="fw-bold">Total: <span id="cartTotal">Rp 0</span></div>
                    <a href="#" id="waOrderBtn" class="btn btn-success" target="_blank"><i class="bi bi-whatsapp"></i> Pesan via WhatsApp</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Carousel arrow custom gold */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-image: none !important;
        width: 2.5rem;
        height: 2.5rem;
        background-color: gold;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .product-img {
        height: 180px;
        object-fit: contain;
        width: 100%;
        background: #fff;
        border-bottom: 1px solid #eee;
        padding: 8px 0;
    }

    .product-card .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-card .product-desc {
        min-height: 36px;
        max-height: 36px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-bottom: 0.5rem;
    }

    .product-card .card-body {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        padding-bottom: 0.5rem;
        min-height: 320px;
    }

    .product-card .input-group {
        margin-bottom: 0.5rem;
    }

    .product-card .btn-add-cart {
        margin-bottom: 0.25rem;
    }

    .product-actions {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
    }

    .product-actions .input-group {
        margin-bottom: 0;
    }

    @media (max-width: 767px) {

        .rounded-pill,
        .rounded-4 {
            border-radius: 1.5rem !important;
        }

        #searchProduct {
            font-size: 1rem;
        }

        #tagFilters {
            font-size: 0.95rem;
        }

        .product-img {
            height: 140px;
        }
    }

    #tagFilters {
        min-height: 44px;
        align-items: center;
        gap: 0.5rem;
    }

    #tagFilters .form-check {
        margin-bottom: 0;
    }

    .product-desc-html {
        font-size: 0.97rem;
        line-height: 1.5;
        min-height: 36px;
        max-height: 36px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-bottom: 0.5rem;
        flex-shrink: 0;
    }

    .product-desc-html p {
        margin-bottom: 0.5em;
    }

    .product-desc-html strong {
        font-weight: bold;
    }

    .product-desc-html em {
        font-style: italic;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Search & Filter Modal ---
        let allTags = [];
        let activeTags = [];
        // Ambil semua tag unik dari produk
        (function() {
            const tagSet = new Set();
            document.querySelectorAll('.product-item').forEach(item => {
                const tags = (item.getAttribute('data-tags') || '').split(',').map(t => t.trim()).filter(Boolean);
                tags.forEach(t => tagSet.add(t));
            });
            allTags = Array.from(tagSet).sort();
        })();
        const openFilterModalBtn = document.getElementById('openFilterModalBtn');
        const filterModal = new bootstrap.Modal(document.getElementById('filterModal'));
        const filterTagList = document.getElementById('filterTagList');
        const searchTagInput = document.getElementById('searchTagInput');
        const applyFilterBtn = document.getElementById('applyFilterBtn');
        const activeFilterSummary = document.getElementById('activeFilterSummary');

        function renderTagList(filter = '') {
            filterTagList.innerHTML = '';
            let filtered = allTags.filter(tag => tag.toLowerCase().includes(filter.toLowerCase()));
            if (filtered.length === 0) {
                filterTagList.innerHTML = '<span class="text-muted">Tag tidak ditemukan</span>';
                return;
            }
            filtered.forEach(tag => {
                const checked = activeTags.includes(tag) ? 'checked' : '';
                filterTagList.innerHTML += `<div class='form-check form-check-inline mb-2'>
                <input class='form-check-input' type='checkbox' value='${tag}' id='tagCheck_${tag}' ${checked}>
                <label class='form-check-label' for='tagCheck_${tag}'>${tag}</label>
            </div>`;
            });
        }
        openFilterModalBtn.onclick = function() {
            renderTagList();
            searchTagInput.value = '';
            filterModal.show();
        };
        searchTagInput.oninput = function() {
            renderTagList(this.value);
        };
        filterTagList.onclick = function(e) {
            if (e.target.classList.contains('form-check-input')) {
                const tag = e.target.value;
                if (e.target.checked) {
                    if (!activeTags.includes(tag)) activeTags.push(tag);
                } else {
                    activeTags = activeTags.filter(t => t !== tag);
                }
            }
        };
        applyFilterBtn.onclick = function() {
            filterModal.hide();
            filterProducts();
            updateActiveFilterSummary();
        };

        function updateActiveFilterSummary() {
            if (activeTags.length === 0) {
                activeFilterSummary.style.display = 'none';
                activeFilterSummary.innerHTML = '';
            } else {
                activeFilterSummary.style.display = '';
                activeFilterSummary.innerHTML = '<span class="me-2">Filter aktif:</span>' +
                    activeTags.map(tag => `<span class='badge bg-warning text-dark me-1 mb-1 filter-badge' data-tag='${tag}' style='cursor:pointer;'>${tag} <span class='ms-1' style='font-weight:bold;'>&times;</span></span>`).join('');
            }
        }
        // Remove tag from filter if badge clicked
        activeFilterSummary.onclick = function(e) {
            const badge = e.target.closest('.filter-badge');
            if (badge) {
                const tag = badge.getAttribute('data-tag');
                activeTags = activeTags.filter(t => t !== tag);
                filterProducts();
                updateActiveFilterSummary();
            }
        };

        function filterProducts() {
            const search = (document.getElementById('searchProduct').value || '').toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.getAttribute('data-name');
                const tags = (item.getAttribute('data-tags') || '').split(',').map(t => t.trim());
                let show = true;
                if (search && !name.includes(search)) show = false;
                if (activeTags.length && !activeTags.some(tag => tags.includes(tag))) show = false;
                item.style.display = show ? '' : 'none';
            });
        }
        document.getElementById('searchProduct').oninput = function() {
            filterProducts();
            updateActiveFilterSummary();
        };

        // --- Modal Detail Produk (galeri carousel + zoom) ---
        let selectedProduct = null;
        const detailModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
        const zoomModal = new bootstrap.Modal(document.getElementById('zoomImageModal'));
        document.querySelectorAll('.btn-detail').forEach(btn => {
            btn.addEventListener('click', function() {
                const prodDiv = btn.closest('.product-item');
                const prod = JSON.parse(prodDiv.getAttribute('data-product'));
                selectedProduct = prod;
                document.getElementById('detailTitle').innerText = prod.name;
                document.getElementById('detailDesc').innerText = prod.description || '-';
                document.getElementById('detailPrice').innerText = 'Rp ' + Number(prod.price).toLocaleString('id-ID');
                // Tags
                let tagHtml = '';
                if (prod.tags) {
                    prod.tags.split(',').forEach(tag => {
                        tagHtml += `<span class=\"badge bg-secondary me-1 mb-1\">${tag.trim()}</span>`;
                    });
                }
                document.getElementById('detailTags').innerHTML = tagHtml;
                // Gallery as carousel
                let gal = prod.gallery && prod.gallery.length ? prod.gallery : [prod.thumbnail];
                let carouselId = 'galleryCarousel';
                let carouselHtml = `<div id='${carouselId}' class='carousel slide' data-bs-ride='carousel'><div class='carousel-inner'>`;
                gal.forEach((img, idx) => {
                    carouselHtml += `<div class='carousel-item${idx===0?' active':''}'>
                    <img src='<?= base_url() ?>${img}' class='d-block w-100 gallery-img' style='max-height:320px;object-fit:contain;cursor:zoom-in;' data-img='<?= base_url() ?>${img}'>
                </div>`;
                });
                carouselHtml += `</div>`;
                if (gal.length > 1) {
                    carouselHtml += `<button class='carousel-control-prev' type='button' data-bs-target='#${carouselId}' data-bs-slide='prev'><span class='carousel-control-prev-icon' aria-hidden='true'></span><span class='visually-hidden'>Previous</span></button><button class='carousel-control-next' type='button' data-bs-target='#${carouselId}' data-bs-slide='next'><span class='carousel-control-next-icon' aria-hidden='true'></span><span class='visually-hidden'>Next</span></button>`;
                }
                carouselHtml += `</div>`;
                document.getElementById('detailGalleryCarouselWrapper').innerHTML = carouselHtml;
                // Zoom handler
                setTimeout(() => {
                    document.querySelectorAll('.gallery-img').forEach(img => {
                        img.onclick = function(e) {
                            document.getElementById('zoomedImage').src = img.getAttribute('data-img');
                            zoomModal.show();
                        };
                    });
                }, 100);
                detailModal.show();
            });
        });
        // Close zoom modal if click outside image
        const zoomModalEl = document.getElementById('zoomImageModal');
        zoomModalEl.addEventListener('click', function(e) {
            if (e.target === zoomModalEl || e.target.classList.contains('modal-content')) {
                zoomModal.hide();
            }
        });

        // --- Qty & Add to Cart di Card ---
        document.querySelectorAll('.product-card').forEach(card => {
            const minus = card.querySelector('.btn-qty-minus');
            const plus = card.querySelector('.btn-qty-plus');
            const qtyInput = card.querySelector('.qty-input');
            minus.onclick = () => {
                if (parseInt(qtyInput.value) > 1) qtyInput.value = parseInt(qtyInput.value) - 1;
            };
            plus.onclick = () => {
                qtyInput.value = parseInt(qtyInput.value) + 1;
            };
            card.querySelector('.btn-add-cart').onclick = function() {
                const prodDiv = card.closest('.product-item');
                const prod = JSON.parse(prodDiv.getAttribute('data-product'));
                const qty = parseInt(qtyInput.value);
                let cart = getCart();
                const idx = cart.findIndex(p => p.id === prod.id);
                if (idx > -1) {
                    cart[idx].qty += qty;
                } else {
                    cart.push({
                        id: prod.id,
                        name: prod.name,
                        price: prod.price,
                        thumbnail: prod.thumbnail,
                        qty
                    });
                }
                setCart(cart);
                Swal.fire({
                    icon: 'success',
                    title: 'Ditambahkan ke keranjang!',
                    timer: 1000,
                    showConfirmButton: false
                });
                qtyInput.value = 1;
            };
        });

        // --- Keranjang (localStorage) ---
        function getCart() {
            return JSON.parse(localStorage.getItem('cart') || '[]');
        }

        function setCart(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }

        function updateCartCount() {
            const cart = getCart();
            var badge = document.getElementById('cartCount');
            if (badge) {
                badge.innerText = cart.reduce((a, b) => a + b.qty, 0);
            }
        }
        updateCartCount();

        // --- Cart Button Floating ---
        var cartBtn = document.getElementById('cartBtn');

        function showCartModal() {
            const cart = getCart();
            let html = '';
            let total = 0;
            if (cart.length === 0) {
                html = '<p class="text-center">Keranjang kosong.</p>';
            } else {
                html = '<div class="table-responsive"><table class="table mb-0"><thead><tr><th></th><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr></thead><tbody>';
                cart.forEach((item, i) => {
                    const sub = item.price * item.qty;
                    total += sub;
                    html += `<tr>
            <td><img src=\"<?= base_url() ?>${item.thumbnail}\" style=\"width:50px;height:50px;object-fit:cover;\"></td>
            <td>${item.name}</td>
            <td>Rp ${item.price.toLocaleString('id-ID')}</td>
            <td>${item.qty}</td>
            <td>Rp ${(sub).toLocaleString('id-ID')}</td>
            <td><button class=\"btn btn-sm btn-danger btn-remove-cart\" data-idx=\"${i}\">Hapus</button></td>
        </tr>`;
                });
                html += '</tbody></table></div>';
            }
            document.getElementById('cartItems').innerHTML = html;
            document.getElementById('cartTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
            // WhatsApp order
            const wa = "<?= preg_replace('/[^0-9]/', '', $settings->contact_whatsapp) ?>";
            let waMsg = 'Halo, saya ingin memesan:%0A';
            cart.forEach(item => {
                waMsg += `- ${item.name} x${item.qty} (Rp ${item.price.toLocaleString('id-ID')})%0A`;
            });
            waMsg += `Total: Rp ${total.toLocaleString('id-ID')}`;
            document.getElementById('waOrderBtn').href = `https://wa.me/${wa}?text=${waMsg}`;
            new bootstrap.Modal(document.getElementById('cartModal')).show();
            // Hapus produk dari keranjang
            document.querySelectorAll('.btn-remove-cart').forEach(btn => {
                btn.onclick = function() {
                    const idx = parseInt(btn.getAttribute('data-idx'));
                    let cart = getCart();
                    cart.splice(idx, 1);
                    setCart(cart);
                    showCartModal();
                };
            });
        }
        if (cartBtn) {
            function updateCartIcon() {
                var cart = [];
                try {
                    cart = JSON.parse(localStorage.getItem('cart') || '[]');
                } catch (e) {}
                var iconEmpty = `<img src="<?= base_url('assets/icon/shopping-cart.png') ?>" alt="Keranjang Kosong" style="width:52px;height:52px;vertical-align:middle;">`;
                var iconFilled = `<img src="<?= base_url('assets/icon/shopping-cart-filled.png') ?>" alt="Keranjang Berisi" style="width:52px;height:52px;vertical-align:middle;">`;
                cartBtn.querySelector('span').innerHTML = (cart && cart.length > 0 ? iconFilled : iconEmpty) + '<span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' + (cart && cart.length ? cart.reduce((a, b) => a + b.qty, 0) : 0) + '</span>';
            }
            updateCartIcon();
            window.addEventListener('storage', updateCartIcon);
            setInterval(updateCartIcon, 1000);
            // Only assign the modal handler ONCE and do not overwrite
            cartBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showCartModal();
            });
        }

        document.querySelector('.navbar-toggler').addEventListener('click', function() {
            setTimeout(function() {
                var nav = document.getElementById('navbarNav');
                console.log('NavbarNav class:', nav.className, 'display:', getComputedStyle(nav).display);
            }, 400);
        });
    });
</script>

<?= $this->endSection() ?>