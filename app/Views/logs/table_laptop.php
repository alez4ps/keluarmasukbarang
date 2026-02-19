<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Aksi</th>
                <th>Nama Pengguna</th>
                <th>Merek</th>
                <th>Tipe</th>
                <th>Nomor Seri</th>
                <th>Berlaku Sampai</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2">Tidak ada data log laptop</p>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($logs as $l): ?>
                <tr>
                    <td><?= esc($l['created_at'] ?? '-') ?></td>
                    <td>
                        <?php
                        $aksi = $l['aksi'] ?? '-';
                        $badge = match ($aksi) {
                            'Registrasi' => 'success',
                            'Perpanjangan' => 'warning',
                            default => 'primary'
                        };
                        ?>
                        <span class="badge bg-<?= $badge ?>">
                            <?= esc($aksi) ?>
                        </span>
                    </td>
                    <td><?= esc($l['nama_pengguna'] ?? '-') ?></td>
                    <td><?= esc($l['merek'] ?? '-') ?></td>
                    <td><?= esc($l['tipe_laptop'] ?? '-') ?></td>
                    <td><?= esc($l['nomor_seri'] ?? '-') ?></td>
                    <td>
                        <?php if (!empty($l['berlaku_sampai'])): ?>
                            <?= date('d/m/Y', strtotime($l['berlaku_sampai'])) ?>
                            <?php if (strtotime($l['berlaku_sampai']) < time()): ?>
                                <span class="badge bg-danger">Expired</span>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= esc($l['log_keterangan'] ?? '-') ?></td>
                    <td>
                        <button type="button" 
                                class="btn btn-sm btn-outline-info" 
                                data-bs-toggle="modal" 
                                data-bs-target="#LaptopDetailModal<?= $l['log_id'] ?? $l['id'] ?? '' ?>">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>