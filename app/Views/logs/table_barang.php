<?php if (empty($logs)): ?>
<div class="alert alert-info text-center py-4">
    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
    <h5>Tidak ada data aktivitas barang</h5>
    <p class="text-muted mb-0">Belum ada aktivitas barang masuk atau keluar</p>
</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Agenda</th>
                <th>Nama Barang</th>
                <th>Aksi</th>
                <th>Jumlah</th>
                <th>Sisa</th>
                <th>Satuan</th>
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
                    <span class="fw-bold"><?= esc($log['no_agenda'] ?? '-') ?></span>
                </td>
                <td><?= esc($log['nama_barang'] ?? '-') ?></td>
                <td>
                    <?php
                    $badge = match($log['aksi']) {
                        'Registrasi' => 'primary',
                        'Masuk' => 'success',
                        'Keluar' => 'warning',
                        'Kembali' => 'info',
                        'Selesai' => 'dark',
                        'Update' => 'secondary',
                        default => 'secondary'
                    };
                    
                    $icon = match($log['aksi']) {
                        'Registrasi' => 'bi-plus-circle',
                        'Masuk' => 'bi-box-arrow-in-down',
                        'Keluar' => 'bi-box-arrow-up',
                        'Kembali' => 'bi-arrow-return-left',
                        'Selesai' => 'bi-check2-circle',
                        'Update' => 'bi-pencil',
                        default => 'bi-info-circle'
                    };
                    ?>
                    <span class="badge bg-<?= $badge ?>">
                        <i class="bi <?= $icon ?>"></i> <?= esc($log['aksi']) ?>
                    </span>
                </td>
                <td class="text-end fw-bold"><?= number_format($log['jumlah'] ?? 0) ?></td>
                <td class="text-end"><?= number_format($log['sisa'] ?? 0) ?></td>
                <td><?= esc($log['barang_satuan'] ?? '-') ?></td>
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
    <?= $pager->links('barang', 'bootstrap_pagination') ?>
</div>
<?php endif; ?>
<?php endif; ?>