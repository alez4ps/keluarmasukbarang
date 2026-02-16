<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Riwayat Aktivitas Barang</h4>

<!-- FILTER SECTION -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form action="/logs" method="get" class="row g-3">

            <!-- FILTER TANGGAL -->
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" 
                       value="<?= esc($startDate ?? '') ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" 
                       value="<?= esc($endDate ?? '') ?>">
            </div>

            <!-- SEARCH -->
            <div class="col-md-3">
                <label class="form-label">Pencarian</label>
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control"
                           placeholder="Cari barang / agenda / SPB..."
                           value="<?= esc($keyword ?? '') ?>">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="/logs" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- SUMMARY INFO -->
<?php if (isset($totalRows)): ?>
<div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
    <div>
        <i class="bi bi-info-circle"></i>
        Menampilkan <strong><?= number_format($totalRows) ?></strong> aktivitas
        <?php if ($startDate || $endDate): ?>
            dari <?= $startDate ? date('d/m/Y', strtotime($startDate)) : 'awal' ?>
            sampai <?= $endDate ? date('d/m/Y', strtotime($endDate)) : 'sekarang' ?>
        <?php endif; ?>
    </div>
    <div>
        <a href="/logs/export?<?= http_build_query($_GET) ?>" 
           class="btn btn-sm btn-success">
            <i class="bi bi-file-excel"></i> Export Excel
        </a>
    </div>
</div>
<?php endif; ?>

<!-- TABLE -->
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
                        <h5 class="mt-3">Tidak ada data log</h5>
                        <p class="mb-0">Coba ubah filter atau tanggal pencarian</p>
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($logs as $index => $l): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td>
                    <div class="small"><?= date('d/m/Y', strtotime($l['created_at'])) ?></div>
                    <div class="text-muted smaller"><?= date('H:i:s', strtotime($l['created_at'])) ?></div>
                </td>
                <td>
                    <?php
                    $badge = match (true) {
                        str_starts_with($l['no_agenda'], 'M-') => 'primary',
                        str_starts_with($l['no_agenda'], 'K-') => 'warning',
                        default => 'secondary'
                    };
                    ?>
                    <span class="badge bg-<?= $badge ?>">
                        <?= esc($l['no_agenda']) ?>
                    </span>
                </td>
                <td>
                    <small><?= esc($l['no_spb']) ?></small>
                </td>
                <td>
                    <div class="fw-semibold"><?= esc($l['nama_barang']) ?></div>
                    <small class="text-muted">
                        <?= $l['tipe'] ?? '-' ?> • 
                        <?= ($l['is_partial'] ?? '') === 'Ya' ? 'Partial' : 'Non-Partial' ?>
                    </small>
                </td>
                <td>
                    <?php
                    $badge = match ($l['aksi']) {
                        'Registrasi' => 'primary',
                        'Masuk' => 'success',
                        'Keluar' => 'warning text-dark',
                        'Kembali' => 'info text-dark',
                        'Selesai' => 'dark',
                        default => 'secondary'
                    };
                    $icon = match ($l['aksi']) {
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
                        <?= esc($l['aksi']) ?>
                    </span>
                </td>
                <td>
                    <div class="fw-bold"><?= number_format($l['jumlah']) ?></div>
                    <small class="text-muted"><?= esc($l['satuan']) ?></small>
                </td>
                <td>
                    <?= number_format($l['sisa']) ?>
                    <div class="small text-muted">
                        Total: <?= number_format($l['barang_jumlah'] ?? 0) ?>
                    </div>
                </td>
                <td>
                    <div><?= esc($l['asal']) ?></div>
                    <small class="text-muted">→</small>
                    <div><?= esc($l['tujuan']) ?></div>
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
                    <small><?= esc($l['log_keterangan'] ?? '') ?></small>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary"
                                data-bs-toggle="modal" 
                                data-bs-target="#DetailModal<?= $l['log_id'] ?>"
                                title="Detail Log">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<!-- MODAL DETAIL UNTUK SETIAP LOG -->
<?php foreach ($logs as $l): ?>
<div class="modal fade" id="DetailModal<?= $l['log_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- INFO LOG -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <i class="bi bi-clock-history"></i> Informasi Aktivitas
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Waktu</small>
                                    <div class="fw-semibold"><?= esc($l['created_at']) ?></div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Aksi</small>
                                    <div>
                                        <?php
                                        $badge = match ($l['aksi']) {
                                            'Registrasi' => 'primary',
                                            'Masuk' => 'success',
                                            'Keluar' => 'warning',
                                            'Kembali' => 'info',
                                            'Selesai' => 'dark',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?>">
                                            <?= esc($l['aksi']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Jumlah</small>
                                    <div class="fw-bold">
                                        <?= number_format($l['jumlah']) ?> <?= esc($l['satuan']) ?>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Sisa</small>
                                    <div>
                                        <?= number_format($l['sisa']) ?> <?= esc($l['satuan']) ?>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">Keterangan</small>
                                    <div><?= esc($l['log_keterangan'] ?? '-') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INFO BARANG -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <i class="bi bi-box-seam"></i> Informasi Barang
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">No Agenda</small>
                                    <div class="fw-semibold"><?= esc($l['no_agenda']) ?></div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">No SPB</small>
                                    <div><?= esc($l['no_spb']) ?></div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Nama Barang</small>
                                    <div class="fw-semibold"><?= esc($l['nama_barang']) ?></div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Tipe</small>
                                    <div>
                                        <span class="badge bg-secondary">
                                            <?= esc($l['tipe'] ?? '-') ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Asal → Tujuan</small>
                                    <div><?= esc($l['asal']) ?> → <?= esc($l['tujuan']) ?></div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Status</small>
                                    <div>
                                        <?php if (($l['status'] ?? '') === 'Selesai'): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Selesai
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-hourglass-split"></i> Belum Selesai
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">Progress</small>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 10px;">
                                            <?php
                                            $total = $l['barang_jumlah'] ?? 1;
                                            $kembaliPercent = isset($l['jumlah_kembali']) ? ($l['jumlah_kembali'] / $total * 100) : 0;
                                            $keluarPercent = isset($l['jumlah_keluar']) ? ($l['jumlah_keluar'] / $total * 100) : 0;
                                            ?>
                                            <div class="progress-bar bg-success" 
                                                 style="width: <?= $kembaliPercent ?>%"
                                                 title="Masuk: <?= $l['jumlah_kembali'] ?? 0 ?>">
                                            </div>
                                            <div class="progress-bar bg-warning" 
                                                 style="width: <?= $keluarPercent ?>%"
                                                 title="Keluar: <?= $l['jumlah_keluar'] ?? 0 ?>">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= ($l['jumlah_kembali'] ?? 0) ?>/<?= $total ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="/registrasi" class="btn btn-primary">
                    <i class="bi bi-box-arrow-up-right"></i> Lihat Barang
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach ?>

<?= $this->endSection() ?>