<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$sisa = $barang['jumlah'] - $barang['jumlah_kembali'];
?>

<?php if ($sisa <= 0): ?>
    <div class="alert alert-success">
        Semua barang sudah masuk.
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

<h2>Barang Masuk</h2>

<form method="post" action="/registrasi/prosesMasuk/<?= $barang['id'] ?>">

    <!-- NAMA BARANG -->
    <div class="mb-3">
        <label class="form-label">Nama Barang</label>
        <input type="text"
               class="form-control"
               value="<?= esc($barang['nama_barang']) ?>"
               readonly>
    </div>

    <!-- JUMLAH MASUK -->
    <div class="mb-3">
        <label class="form-label">Jumlah Masuk</label>
        <input type="number"
               name="jumlah_masuk"
               class="form-control"
               min="1"
               max="<?= $sisa ?>"
               required>
        <small class="text-muted">
            Sisa barang yang bisa masuk: <?= $sisa ?>
        </small>
    </div>

    <!-- TOMBOL -->
    <button class="btn btn-primary">
        <i class="bi bi-box-arrow-in-down"></i> Proses Masuk
    </button>

    <a href="/registrasi" class="btn btn-secondary">
        Batal
    </a>
</form>

<?= $this->endSection() ?>
