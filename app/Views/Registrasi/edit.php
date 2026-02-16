<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow">
<div class="card-body">

<h4>Edit Registrasi Barang</h4>

<form method="post" action="/registrasi/update/<?= $barang['id'] ?>">

<div class="row g-3">

<div class="col-md-6">
    <label>No Agenda</label>
    <input type="text" name="no_agenda"
           value="<?= esc($barang['no_agenda']) ?>"
           class="form-control" readonly>
</div>

<div class="col-md-6">
    <label>Tipe</label>
    <select name="tipe" class="form-select">
        <?php
        $tipe = ['Komersil','Militer','Jasa','Non_core','Perbaikan','Petty_cash'];
        foreach ($tipe as $t):
        ?>
        <option value="<?= $t ?>" <?= $barang['tipe']==$t?'selected':'' ?>>
            <?= $t ?>
        </option>
        <?php endforeach ?>
    </select>
</div>

<input type="hidden" name="tanggal" value="<?= esc($barang['tanggal']) ?>">

<div class="col-md-12">
    <label>No SPB</label>
    <input type="text" name="no_spb"
           value="<?= esc($barang['no_spb']) ?>"
           class="form-control">
</div>

<div class="col-md-12">
    <label>Nama Barang</label>
    <input type="text" name="nama_barang"
           value="<?= esc($barang['nama_barang']) ?>"
           class="form-control">
</div>

<div class="col-md-6">
    <label>Jumlah</label>
    <input type="number" name="jumlah"
           value="<?= esc($barang['jumlah']) ?>"
           class="form-control">
</div>

<div class="col-md-6">
    <label>Satuan</label>
    <input type="text" name="satuan"
           value="<?= esc($barang['satuan']) ?>"
           class="form-control">
</div>

<div class="col-md-12">
    <label>Asal</label>
    <input type="text" name="asal"
           value="<?= esc($barang['asal']) ?>"
           class="form-control">
</div>

<div class="col-md-12">
    <label>Tujuan</label>
    <input type="text" name="tujuan"
           value="<?= esc($barang['tujuan']) ?>"
           class="form-control">
</div>

<div class="col-md-12">
    <label>Status Barang</label>

    <div class="form-check">
        <input class="form-check-input" type="checkbox"
               name="is_partial" value="1"
               <?= $barang['is_partial'] ? 'checked' : '' ?>>
        <label class="form-check-label">Partial</label>
    </div>

    <div class="form-check">
        <input class="form-check-input" type="checkbox"
               name="barang_kembali" value="1"
               <?= $barang['keterangan']=='Barang Kembali'?'checked':'' ?>>
        <label class="form-check-label">Kembali</label>
    </div>
</div>

</div>

<div class="mt-4">
    <button class="btn btn-success">Update</button>
    <a href="/registrasi" class="btn btn-secondary">Batal</a>
</div>

</form>

</div>
</div>

<?= $this->endSection() ?>
