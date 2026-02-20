<?php if (empty($logs)): ?>
<div class="alert alert-info text-center py-4">
    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
    <h5>Tidak ada data aktivitas laptop</h5>
    <p class="text-muted mb-0">Belum ada aktivitas registrasi atau perpanjangan laptop</p>
</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Registrasi</th>
                <th>Jenis</th>
                <th>Nama Pengguna</th>
                <th>Merek</th>
                <th>Tipe</th>
                <th>Nomor Seri</th>
                <th>Aksi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $index => $log): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td>
                    <small><?= date('d/m/Y', strtotime($log['created_at'])) ?></small>
                    <br>
                    <small class="text-muted"><?= date('H:i:s', strtotime($log['created_at'])) ?></small>
                </td>
                <td>
                    <span class="fw-bold"><?= esc($log['no_registrasi'] ?? '-') ?></span>
                </td>
                <td>
                    <?php if (isset($log['jenis']) && $log['jenis'] == 'Pegawai'): ?>
                        <span class="badge bg-primary">Pegawai</span>
                    <?php elseif (isset($log['jenis']) && $log['jenis'] == 'Non Pegawai'): ?>
                        <span class="badge bg-info">Non Pegawai</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">-</span>
                    <?php endif; ?>
                </td>
                <td><?= esc($log['nama_pengguna'] ?? '-') ?></td>
                <td><?= esc($log['merek'] ?? '-') ?></td>
                <td><?= esc($log['tipe_laptop'] ?? '-') ?></td>
                <td><?= esc($log['nomor_seri'] ?? '-') ?></td>
                <td>
                    <?php
                    $badge = match($log['aksi']) {
                        'Registrasi' => 'success',
                        'Perpanjangan' => 'warning',
                        'Update' => 'info',
                        'Hapus' => 'danger',
                        default => 'secondary'
                    };
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= esc($log['aksi']) ?></span>
                </td>
                <td>
                    <small><?= esc($log['keterangan'] ?? '-') ?></small>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if (isset($pager) && $pager): ?>
<div class="d-flex justify-content-center mt-4">
    <?= $pager->links('laptop', 'bootstrap_pagination') ?>
</div>
<?php endif; ?>
<?php endif; ?>