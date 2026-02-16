<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$sisa = $barang['jumlah'] - $barang['jumlah_kembali'];
?>

<?php if ($sisa <= 0): ?>
    <div class="alert alert-success">
        Semua barang sudah kembali.
    </div>
    <a href="/registrasi" class="btn btn-primary">Kembali</a>
    <?= $this->endSection() ?>
    <?php return; ?>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<h2>Barang Kembali</h2>

<form method="post" action="/registrasi/prosesKembali/<?= $barang['id'] ?>">

    <!-- NAMA BARANG -->
    <div class="mb-3">
        <label class="form-label">Nama Barang</label>
        <input type="text"
               class="form-control"
               value="<?= esc($barang['nama_barang']) ?>"
               readonly>
    </div>

    <!-- JUMLAH KEMBALI -->
    <div class="mb-3">
        <label class="form-label">Jumlah Kembali</label>
        <input type="number"
               name="jumlah_kembali"
               id="jumlah_kembali"
               class="form-control"
               min="0"
               max="<?= $sisa ?>"
               value="0">
        <small class="text-muted">
            Sisa barang yang bisa kembali: <?= $sisa ?>
        </small>
    </div>

    <!-- TIDAK KEMBALI -->
    <div class="form-check mb-3">
        <input class="form-check-input"
               type="checkbox"
               name="tidak_kembali"
               value="1"
               id="tidak_kembali">
        <label class="form-check-label" for="tidak_kembali">
            Tandai Tidak Kembali
        </label>
    </div>

    <!-- TOMBOL -->
    <button class="btn btn-primary">
        <i class="bi bi-box-arrow-in-down"></i> Proses Kembali
    </button>

    <a href="/registrasi" class="btn btn-secondary">
        Batal
    </a>

</form>

<script>
document.getElementById('tidak_kembali').addEventListener('change', function () {
    const input = document.getElementById('jumlah_kembali');
    if (this.checked) {
        input.value = 0;
        input.disabled = true;
    } else {
        input.disabled = false;
    }
});
</script>

<?= $this->endSection() ?>
