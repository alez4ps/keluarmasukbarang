<?php $type = $type ?? 'semua'; ?>
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Waktu</th>
                <th>No Agenda</th>
                <th>Dasar</th>
                <th>Barang</th>
                <th>Aksi</th>
                <th>Jumlah</th>
                <th>Sisa</th>
                <th>Asal → Tujuan</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="12" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-6"></i><br>
                        <h5 class="mt-3">Tidak ada data log <?= $type ?></h5>
                        <p class="mb-0">Coba ubah filter atau tanggal pencarian</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $index => $l): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <div class="small"><?= date('d/m/Y', strtotime($l['created_at'] ?? date('Y-m-d'))) ?></div>
                        <div class="text-muted smaller"><?= date('H:i:s', strtotime($l['created_at'] ?? date('Y-m-d'))) ?></div>
                    </td>
                    <td>
                        <?php
                        $noAgenda = $l['no_agenda'] ?? '-';
                        $badge = match (true) {
                            str_starts_with($noAgenda, 'M-') => 'primary',
                            str_starts_with($noAgenda, 'K-') => 'warning',
                            default => 'secondary'
                        };
                        ?>
                        <span class="badge bg-<?= $badge ?>">
                            <?= esc($noAgenda) ?>
                        </span>
                    </td>
                    <td>
                        <small><?= esc($l['no_spb'] ?? '-') ?></small>
                    </td>
                    <td>
                        <div class="fw-semibold"><?= esc($l['nama_barang'] ?? '-') ?></div>
                        <small class="text-muted">
                            <?= $l['tipe'] ?? '-' ?> • 
                            <?= ($l['is_partial'] ?? '') === 'Ya' ? 'Partial' : 'Non-Partial' ?>
                        </small>
                    </td>
                    <td>
                        <?php
                        $aksi = $l['aksi'] ?? '-';
                        $badge = match ($aksi) {
                            'Registrasi' => 'primary',
                            'Masuk' => 'success',
                            'Keluar' => 'warning text-dark',
                            'Kembali' => 'info text-dark',
                            'Selesai' => 'dark',
                            default => 'secondary'
                        };
                        $icon = match ($aksi) {
                            'Registrasi' => 'plus-circle',
                            'Masuk' => 'box-arrow-in-down',
                            'Keluar' => 'box-arrow-up',
                            'Kembali' => 'arrow-counterclockwise',
                            'Selesai' => 'check-circle',
                            default => 'circle'
                        };
                        ?>
                        <span class="badge bg-<?= $badge ?>">
                            <i class="bi bi-<?= $icon ?>"></i>
                            <?= esc($aksi) ?>
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold"><?= number_format($l['jumlah'] ?? 0) ?></div>
                        <small class="text-muted"><?= esc($l['satuan'] ?? '-') ?></small>
                    </td>
                    <td>
                        <?= number_format($l['sisa'] ?? 0) ?>
                        <div class="small text-muted">
                            Total: <?= number_format($l['barang_jumlah'] ?? 0) ?>
                        </div>
                    </td>
                    <td>
                        <div><?= esc($l['asal'] ?? '-') ?></div>
                        <small class="text-muted">→</small>
                        <div><?= esc($l['tujuan'] ?? '-') ?></div>
                    </td>
                    <td>
                        <?php if (($l['status'] ?? '') === 'Selesai'): ?>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Selesai
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger">
                                <i class="bi bi-hourglass-split"></i> Belum
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small><?= esc($l['log_keterangan'] ?? '-') ?></small>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-primary"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#DetailModal<?= $l['log_id'] ?? $l['id'] ?>"
                                    title="Detail Log">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-danger"
                                    onclick="deleteBarangLog(<?= $l['log_id'] ?? $l['id'] ?>)"
                                    title="Hapus Log">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function deleteBarangLog(logId) {
    if (confirm('Apakah Anda yakin ingin menghapus log ini?')) {
        window.location.href = '/logs/delete-barang/' + logId;
    }
}
</script>