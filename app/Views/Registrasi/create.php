<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.fullscreen-form {
    min-height: calc(100vh - 110px);
    display: flex;
    align-items: center;
}
.form-card {
    width: 100%;
    max-width: 1000px;
    margin: auto;
}
.form-label {
    font-size: 0.95rem;
    font-weight: 500;
}
.form-control, .form-select {
    font-size: 0.95rem;
    padding: 0.45rem 0.6rem;
}
</style>

<div class="fullscreen-form">
<div class="card shadow-lg form-card">
<div class="card-body px-4 py-3">

<h4 class="text-center mb-3">REGISTRASI KELUAR MASUK BARANG / JASA</h4>

<form method="post" action="/registrasi/store">
<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">No Agenda</label>
        <input type="text" name="no_agenda" value="<?= esc($noAgenda) ?>"readonly class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Tipe</label>
        <select name="tipe" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="Komersil">Komersil</option>
            <option value="Militer">Militer</option>
            <option value="Jasa">Jasa</option>
            <option value="Non_core">Non Core</option>
            <option value="Perbaikan">Perbaikan</option>
            <option value="Petty_cash">Petty Cash</option>
        </select>
    </div>

            <input type="hidden"
           name="tanggal"
           value="<?= esc($tanggal) ?>">

    <div class="col-md-12">
        <label class="form-label">No SPB</label>
        <input type="text" name="no_spb" class="form-control" required>
    </div>

    <div class="col-md-12">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama_barang" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Quantity</label>
        <input type="number" name="jumlah" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Satuan</label>
        <input type="text" name="satuan" class="form-control" required>
    </div>

    <div class="col-md-12">
        <label class="form-label">Asal</label>
        <input type="text" name="asal" class="form-control" required>
    </div>

    <div class="col-md-12">
        <label class="form-label">Tujuan</label>
        <input type="text" name="tujuan" class="form-control" required>
    </div>

    <div class="col-md-12">
        <label class="form-label d-block">Partial</label>

        <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   name="is_partial"
                   value="1"
                   required>
            <label class="form-check-label">
                Partial / Barang Partial
            </label>
        </div>
    </div>
    <div class="col-md-12">
        <label class="form-label d-block">Kembali</label>
        <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   name="is_partial"
                   value="0"
                   required>
            <label class="form-check-label">
                Kembali / Barang Kembali
            </label>
        </div>
    </div>
</div>

<div class="text-center mt-4">
    <button class="btn btn-success px-4">
        <i class="bi bi-save"></i> Simpan
    </button>
    <a href="/registrasi" class="btn btn-secondary px-4">
        Kembali
    </a>
</div>

</form>
</div>
</div>
</div>

<?= $this->endSection() ?>
