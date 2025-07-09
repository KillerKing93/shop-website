<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row g-5">
        <div class="col-md-6">
            <!-- Gambar Utama -->
            <img src="<?= base_url($product->thumbnail ?: 'https://placehold.co/600x400/cccccc/ffffff?text=Gambar+Produk') ?>" class="img-fluid rounded shadow-sm mb-3" alt="<?= esc($product->name) ?>">

            <!-- Galeri -->
            <?php $gallery = json_decode($product->gallery); ?>
            <?php if (!empty($gallery) && is_array($gallery)): ?>
                <div class="row g-2">
                    <?php foreach ($gallery as $image): ?>
                        <div class="col-3">
                            <a href="<?= base_url($image) ?>" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-imgsrc="<?= base_url($image) ?>">
                                <img src="<?= base_url($image) ?>" class="img-fluid rounded" alt="Galeri produk">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h1 class="fw-bold"><?= esc($product->name) ?></h1>
            <h3 class="text-primary fw-light mb-3">Rp <?= number_format($product->price, 0, ',', '.') ?></h3>

            <div class="mb-4">
                <strong>Tags:</strong>
                <?php $tags = explode(',', $product->tags); ?>
                <?php foreach ($tags as $tag): ?>
                    <span class="badge bg-secondary"><?= esc(trim($tag)) ?></span>
                <?php endforeach; ?>
            </div>

            <div class="mb-4">
                <strong>Deskripsi:</strong>
                <div><?= $product->description ?></div>
            </div>

            <hr>

            <div class="mt-4">
                <h4>Pesan Sekarang</h4>
                <div class="input-group mb-3" style="max-width: 200px;">
                    <span class="input-group-text">Jumlah</span>
                    <input type="number" class="form-control" id="quantity" value="1" min="1">
                </div>
                <button id="order-btn" class="btn btn-success btn-lg">
                    <i class="bi bi-whatsapp"></i> Pesan via WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Galeri -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img src="" class="img-fluid" id="modalImage" alt="Gambar Produk">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script untuk modal galeri
        const imageModal = document.getElementById('imageModal');
        imageModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const imgSrc = button.getAttribute('data-bs-imgsrc');
            const modalImage = imageModal.querySelector('#modalImage');
            modalImage.src = imgSrc;
        });

        // Script untuk tombol pesan WhatsApp
        const orderBtn = document.getElementById('order-btn');
        orderBtn.addEventListener('click', function() {
            const quantity = document.getElementById('quantity').value;
            const productName = "<?= esc($product->name, 'js') ?>";
            const productPrice = "<?= number_format($product->price, 0, ',', '.') ?>";
            const sellerWhatsapp = "<?= esc($product->seller_whatsapp, 'js') ?>";

            if (quantity < 1) {
                Swal.fire('Oops!', 'Jumlah pesanan minimal 1.', 'error');
                return;
            }

            const message = `Halo, saya tertarik untuk memesan produk berikut:
- Nama Produk: *${productName}*
- Harga: *Rp ${productPrice}*
- Jumlah: *${quantity}*

Mohon informasinya. Terima kasih.`;

            const whatsappUrl = `https://wa.me/${sellerWhatsapp}?text=${encodeURIComponent(message)}`;

            window.open(whatsappUrl, '_blank');
        });
    });
</script>
<?= $this->endSection() ?>