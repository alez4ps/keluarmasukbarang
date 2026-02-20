<table class="table table-bordered table-hover table-striped">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>No. Registrasi</th>
            <th>Jenis</th>
            <th>Nama Pengguna</th>
            <th>Nomor ID</th>
            <th>Instansi/Divisi</th>
            <th>Merek</th>
            <th>Tipe</th>
            <th>Pengguna</th>
            <th>Nomor Seri</th>
            <th>Berlaku Sampai</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($laptops)): ?>
        <tr>
            <td colspan="12" class="text-center text-muted py-4">
                <i class="bi bi-inbox"></i> Tidak ada data laptop
            </td>
        </tr>
        <?php endif; ?>

        <?php foreach ($laptops as $index => $laptop): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td>
                <span class="fw-bold"><?= esc($laptop['no_registrasi'] ?? '-') ?></span>
                <br>
                <small class="text-muted"><?= isset($laptop['created_at']) ? date('d/m/Y H:i', strtotime($laptop['created_at'])) : '-' ?></small>
            </td>
            <td>
                <?php if (isset($laptop['jenis']) && $laptop['jenis'] == 'Pegawai'): ?>
                    <span class="badge bg-primary">Pegawai</span>
                <?php elseif (isset($laptop['jenis']) && $laptop['jenis'] == 'Non Pegawai'): ?>
                    <span class="badge bg-info">Non Pegawai</span>
                <?php else: ?>
                    <span class="badge bg-secondary">-</span>
                <?php endif; ?>
            </td>
            <td><?= esc($laptop['nama_pengguna'] ?? '') ?></td>
            <td><?= esc($laptop['nomor_id_card'] ?? '') ?></td>
            <td><?= esc($laptop['instansi_divisi'] ?? '') ?></td>
            <td><?= esc($laptop['merek'] ?? '') ?></td>
            <td><?= esc($laptop['tipe_laptop'] ?? '') ?></td>
            <td><?= esc($laptop['jenis'] ?? '') ?></td>
            <td><?= esc($laptop['nomor_seri'] ?? '') ?></td>
            <td>
                <?= isset($laptop['berlaku_sampai']) ? date('d/m/Y', strtotime($laptop['berlaku_sampai'])) : '-' ?>
                <?php if (isset($laptop['berlaku_sampai']) && strtotime($laptop['berlaku_sampai']) < time()): ?>
                    <span class="badge bg-danger">Expired</span>
                <?php endif; ?>
            </td>
            <td>
                <?php
                $status = $laptop['status'] ?? 'Tidak Diketahui';
                $badge = match($status) {
                    'Masih Berlaku' => 'success',
                    'Tidak Berlaku' => 'secondary',
                    'Diperpanjang' => 'primary',
                    default => 'secondary'
                };
                ?>
                <span class="badge bg-<?= $badge ?>"><?= $status ?></span>
            </td>
            <!-- Di bagian kolom aksi, tambahkan tombol perpanjang -->
<td>
    <div class="btn-group btn-group-sm" role="group">
        <!-- Tombol QR Code -->
        <button type="button" class="btn btn-outline-dark" 
                onclick="showQRCode('<?= esc($laptop['no_registrasi'] ?? 'LAP-'.$laptop['nomor_seri']) ?>')"
                data-bs-toggle="modal" data-bs-target="#qrModal">
            <i class="bi bi-qr-code"></i>
        </button>
        
        <!-- Tombol Detail -->
        <button type="button" class="btn btn-info" 
                data-bs-toggle="modal" 
                data-bs-target="#detailLaptopModal<?= $laptop['id'] ?>">
            <i class="bi bi-eye"></i>
        </button>
        
        <!-- Tombol Perpanjang (LANGSUNG DI TABEL) -->
        <button type="button" class="btn btn-warning" 
                onclick="perpanjangLaptop(<?= $laptop['id'] ?>)"
                data-bs-toggle="modal" 
                data-bs-target="#perpanjangLaptopModal"
                title="Perpanjang masa berlaku laptop">
            <i class="bi bi-arrow-repeat"></i>
        </button>
        
        <!-- Tombol Edit -->
        <button type="button" class="btn btn-secondary" 
                data-bs-toggle="modal" 
                data-bs-target="#editLaptopModal<?= $laptop['id'] ?>">
            <i class="bi bi-pencil"></i>
        </button>
        
        <!-- Tombol Hapus -->
        <button type="button" class="btn btn-danger" 
                data-bs-toggle="modal" 
                data-bs-target="#deleteLaptopModal<?= $laptop['id'] ?>">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>