<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-3">Data Users</h2>
<div class="d-flex justify-content-between align-items-center mb-3">


    <div class="d-flex gap-2">
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#userModal">
            <i class="bi bi-person-fill-add"></i> Tambah User
        </button>
<a href="/users/print" target="_blank" class="btn btn-secondary mb-3">
    <i class="bi bi-printer"></i> Cetak Laporan
</a>
    </div>

<form action="/users" method="get" class="row g-2 mb-3">
    <div class="d-flex gap-2">
<input type="text" name="keyword" class="form-control"
               placeholder="Cari Data User..."
               value="<?= esc($keyword ?? '') ?>">
        <button class="btn btn-outline-secondary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>

</div>
<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Nama Petugas</th>
        <th>Username</th>
        <th>Role</th>
        <th>Tanggal Buat</th>
        <th>Aksi</th>
    </tr>

    <?php $no=1; foreach ($users as $u): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= $u['nama_petugas'] ?></td>
        <td><?= $u['username'] ?></td>
        <td><?= $u['role'] ?></td>
        <td><?= $u['created_at'] ?></td>
        <td>
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#EditModal<?= $u['id'] ?>">
                <i class="bi bi-pencil-square"></i>
            </button>
            
            <a href="/users/delete/<?= $u['id'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Hapus data?')"> <i class="bi bi-trash3"></i></a>
        </td>
    </tr>
    <?php endforeach ?>

    <!-- MODAL Tambah User -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/users/store">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- MODAL EDIT UNTUK SETIAP BARANG -->
<?php foreach ($users as $u): ?>
<div class="modal fade" id="EditModal<?= $u['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/users/update/<?= $u['id'] ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                        <div class="mb-3">
        <label>Nama Petugas</label>
        <input class="form-control" type="text" name="nama_petugas" value="<?=$u['nama_petugas'] ?>" placeholder="Masukkan Nama Petugas..." required >
    </div>

    <div class="mb-3">
        <label>Username</label>
        <input class="form-control" type="text" name="username" value="<?=$u['username'] ?>" placeholder="Masukkan Username..." required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input min="8" class="form-control" type="text" name="password" placeholder="Kosongkan Jika Tidak Di Edit...">
    </div>

<div class="mb-3">
    <label for="role">Role</label>
    <select class="form-control" name="role" id="role"  required>
        <option value="">-- Pilih Role --</option>
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
    </select>
</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach ?>
</table>

<?= $this->endSection() ?>
