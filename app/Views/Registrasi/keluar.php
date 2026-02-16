<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-4">
    <i class="bi bi-box-arrow-up"></i> Proses Barang Keluar
</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif ?>

<?php
$sisa = (int)$barang['jumlah'] - (int)$barang['jumlah_keluar'];
?>

<div class="card shadow-sm">
    <div class="card-body">

        <form method="post" action="/registrasi/prosesKeluar/<?= $barang['id'] ?>">

            <!-- NAMA BARANG -->
            <div class="mb-3">
                <label class="form-label fw-bold">Nama Barang</label>
                <input type="text"
                       class="form-control"
                       value="<?= esc($barang['nama_barang']) ?>"
                       readonly>
            </div>

            <!-- JUMLAH TOTAL -->
            <div class="mb-3">
                <label class="form-label fw-bold">Jumlah Total</label>
                <input type="text"
                       class="form-control"
                       value="<?= $barang['jumlah'] ?>"
                       readonly>
            </div>

            <!-- JUMLAH SUDAH KELUAR -->
            <div class="mb-3">
                <label class="form-label fw-bold">Sudah Keluar</label>
                <input type="text"
                       class="form-control"
                       value="<?= $barang['jumlah_keluar'] ?>"
                       readonly>
            </div>

            <!-- JUMLAH KELUAR -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    Jumlah Keluar Sekarang
                </label>

                <input type="number"
                       name="jumlah_keluar"
                       class="form-control"
                       min="1"
                       max="<?= $sisa ?>"
                       required
                       placeholder="Maksimal <?= $sisa ?>">

                <small class="text-muted">
                    Sisa barang yang dapat dikeluarkan:
                    <strong><?= $sisa ?></strong>
                </small>
            </div>

            <!-- BUTTON -->
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-warning">
                    <i class="bi bi-box-arrow-up"></i>
                    Proses Keluar
                </button>

                <a href="/registrasi" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Batal
                </a>
            </div>

        </form>

    </div>
</div>

<?= $this->endSection() ?>
