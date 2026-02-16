<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php if (session()->get('errors')) : ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session()->get('errors') as $error) : ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<h2>Tambah User</h2>

<form method="post" action="/users/store">
    <div class="mb-3">
        <label>Nama Petugas</label>
        <input class="form-control" type="text" name="nama_petugas" placeholder="Masukkan Nama Petugas..." required>
    </div>

    <div class="mb-3">
        <label>Username</label>
        <input class="form-control" type="text" name="username" placeholder="Masukkan Username..." required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input class="form-control" type="text" name="password" placeholder="Masukkan Password..." min="8" required>
    </div>

<div class="mb-3">
    <label for="role">Role</label>
    <select class="form-control" name="role" id="role" required>
        <option value="">-- Pilih Role --</option>
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
    </select>
</div>

    <button class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
    <a href="/users" class="btn btn-secondary"><i class="bi bi-skip-backward"></i> Kembali</a>
</form>

<?= $this->endSection() ?>
