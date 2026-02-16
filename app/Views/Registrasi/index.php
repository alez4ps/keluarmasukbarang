<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-3">Data Barang Keluar / Masuk</h2>

<div class="d-flex justify-content-between align-items-center mb-3">
    <!-- ACTION GROUP -->
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registrasiModal">
            <i class="bi bi-box-arrow-down"></i> Barang Masuk
        </button>

        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#keluarModal">
            <i class="bi bi-box-arrow-up"></i> Barang Keluar
        </button>
        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#laptopModal">
            <i class="bi bi-box-arrow-down"></i> Laptop
        </button>
    </div>

    <form action="/registrasi" method="get" class="d-flex gap-2">
        <input type="text" name="keyword" class="form-control"
               placeholder="Cari barang / no agenda..."
               value="<?= esc($keyword ?? '') ?>">
        <button class="btn btn-outline-secondary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>
</div>

<!-- TABLE -->
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>No Agenda</th>
                <th>Dasar</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Asal → Tujuan</th>
                <th>Tipe</th>
                <th>Partial</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Estimasi Kembali</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($barangs)): ?>
                <tr>
                    <td colspan="12" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> Tidak ada data barang
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($barangs as $index => $barang): ?>
            <?php
            $isMasuk        = str_starts_with($barang['no_agenda'], 'M-');
            $isKeluar       = str_starts_with($barang['no_agenda'], 'K-');
            $isPartial      = $barang['is_partial'] === 'Ya';
            $statusSelesai  = $barang['status'] === 'Selesai';
            $keterangan     = $barang['keterangan'];
            $akanKembali    = ($barang['akan_kembali'] ?? 'Tidak') === 'Ya';

            $jumlah         = (int) $barang['jumlah'];
            $jumlahMasuk    = (int) $barang['jumlah_kembali'];
            $jumlahKeluar   = (int) $barang['jumlah_keluar'];

            $masukPenuh     = $jumlahMasuk >= $jumlah;
            $keluarPenuh    = $jumlahKeluar >= $jumlah;
            ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= esc($barang['no_agenda']) ?></td>
                <td><?= esc($barang['no_spb']) ?></td>
                <td>
                    <div class="fw-semibold"><?= esc($barang['nama_barang']) ?></div>
                </td>
                <td>
                    <?= number_format($barang['jumlah']) ?>
                                        <small class="text-muted"><?= esc($barang['satuan']) ?></small>
                    <div class="small text-muted">
                        M:<?= $jumlahMasuk ?>/<?= $jumlah ?> | 
                        K:<?= $jumlahKeluar ?>/<?= $jumlah ?>
                    </div>
                </td>
                <td>
                    <small><?= esc($barang['asal']) ?></small><br>
                    <small class="text-muted">→ <?= esc($barang['tujuan']) ?></small>
                </td>
                <td>
                    <?= esc($barang['tipe']) ?>
                </td>
                <td>
<?= esc($barang['is_partial']) ?>
                </td>
                <td>
<?= esc($barang['status']) ?>
                </td>
                <td>
                    <span class="badge bg-<?= $keterangan === 'Belum Kembali' ? 'primary' : ($keterangan === 'Tidak Kembali' ? 'danger' : 'info') ?>">
                        <?= $keterangan ?>
                </td>
<td><center><?= !empty($barang['estimasi_kembali']) ? esc($barang['estimasi_kembali']) : '----------' ?></center></td>

                <td>
                    <div class="d-flex flex-wrap gap-1">
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#EditModal<?= $barang['id'] ?>">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <?php if (!$statusSelesai): ?>

                            <?php if ($isMasuk): ?>

                                <!-- ===== MASUK ===== -->

                                <?php if ($keterangan === 'Tidak Kembali'): ?>

                                    <?php if ($isPartial && !$masukPenuh): ?>
                                        <button class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#MasukModal<?= $barang['id'] ?>">
                                            <i class="bi bi-box-arrow-in-down"></i>
                                        </button>
                                    <?php endif; ?>

                                <?php else: /* BELUM KEMBALI */ ?>

                                    <!-- MASUK (HANYA JIKA BELUM PENUH) -->
                                    <?php if (!$masukPenuh): ?>
                                        <?php if ($isPartial): ?>
                                            <button class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#MasukModal<?= $barang['id'] ?>">
                                                <i class="bi bi-box-arrow-in-down"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="/registrasi/masukLangsung/<?= $barang['id'] ?>"
                                               class="btn btn-sm btn-primary"
                                               onclick="return confirm('Masukkan semua barang?')">
                                                <i class="bi bi-box-arrow-in-down"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- KELUAR (MUNCUL SETELAH MASUK PENUH) -->
                                    <?php if ($masukPenuh && !$keluarPenuh): ?>
                                        <?php if ($isPartial): ?>
                                            <button class="btn btn-sm btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#KeluarModal<?= $barang['id'] ?>">
                                                <i class="bi bi-box-arrow-up"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="/registrasi/prosesKeluarLangsung/<?= $barang['id'] ?>"
                                               class="btn btn-sm btn-success"
                                               onclick="return confirm('Keluarkan semua barang?')">
                                                <i class="bi bi-box-arrow-up"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                <?php endif; ?>

                            <?php else: ?>

                                <!-- ===== KELUAR ===== -->

                                <?php if ($keterangan === 'Tidak Kembali'): ?>

                                    <?php if ($isPartial && !$keluarPenuh): ?>
                                        <button class="btn btn-sm btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#KeluarModal<?= $barang['id'] ?>">
                                            <i class="bi bi-box-arrow-up"></i>
                                        </button>
                                    <?php endif; ?>

                                <?php else: /* BELUM KEMBALI */ ?>

                                    <!-- KELUAR -->
                                    <?php if (!$keluarPenuh): ?>
                                        <?php if ($isPartial): ?>
                                            <button class="btn btn-sm btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#KeluarModal<?= $barang['id'] ?>">
                                                <i class="bi bi-box-arrow-up"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="/registrasi/prosesKeluarLangsung/<?= $barang['id'] ?>"
                                               class="btn btn-sm btn-success"
                                               onclick="return confirm('Keluarkan semua barang?')">
                                                <i class="bi bi-box-arrow-up"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- MASUK (SETELAH KELUAR PENUH) -->
                                    <?php if ($keluarPenuh && !$masukPenuh): ?>
                                        <?php if ($isPartial): ?>
                                            <button class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#MasukModal<?= $barang['id'] ?>">
                                                <i class="bi bi-box-arrow-in-down"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="/registrasi/masukLangsung/<?= $barang['id'] ?>"
                                               class="btn btn-sm btn-primary"
                                               onclick="return confirm('Masukkan semua barang?')">
                                                <i class="bi bi-box-arrow-in-down"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                <?php endif; ?>

                            <?php endif; ?>

                            <!-- ===== SELESAI ===== -->
                            <?php if (
                                $keterangan === 'Tidak Kembali' ||
                                ($isMasuk && $masukPenuh && $keluarPenuh) ||
                                ($isKeluar && $keluarPenuh && $masukPenuh)
                            ): ?>
                                <a href="/registrasi/selesai/<?= $barang['id'] ?>"
                                   class="btn btn-sm btn-dark"
                                   onclick="return confirm('Tandai sebagai selesai?')">
                                    <i class="bi bi-check2-circle"></i>
                                </a>
                            <?php endif; ?>

                        <?php endif; ?>

                        <!-- DETAIL -->
                        <button class="btn btn-sm btn-info"
                                data-bs-toggle="modal"
                                data-bs-target="#DetailModal<?= $barang['id'] ?>">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<!-- MODAL EDIT UNTUK SETIAP BARANG -->
<?php foreach ($barangs as $barang): ?>
<div class="modal fade" id="EditModal<?= $barang['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/registrasi/update/<?= $barang['id'] ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">No Agenda</label>
                            <input type="text" value="<?= esc($barang['no_agenda']) ?>" 
                                   class="form-control" readonly>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tipe</label>
                            <select name="tipe" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="Komersil" <?= $barang['tipe'] == 'Komersil' ? 'selected' : '' ?>>Komersil</option>
                                <option value="Militer" <?= $barang['tipe'] == 'Militer' ? 'selected' : '' ?>>Militer</option>
                                <option value="Jasa" <?= $barang['tipe'] == 'Jasa' ? 'selected' : '' ?>>Jasa</option>
                                <option value="Non_core" <?= $barang['tipe'] == 'Non_core' ? 'selected' : '' ?>>Non Core</option>
                                <option value="Perbaikan" <?= $barang['tipe'] == 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                                <option value="Petty_cash" <?= $barang['tipe'] == 'Petty_cash' ? 'selected' : '' ?>>Petty Cash</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">No SPB</label>
                            <input type="text" name="no_spb" class="form-control" 
                                   value="<?= esc($barang['no_spb']) ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" 
                                   value="<?= esc($barang['nama_barang']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Total</label>
                            <input type="number" name="jumlah" class="form-control" 
                                   value="<?= esc($barang['jumlah']) ?>" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" 
                                   value="<?= esc($barang['satuan']) ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Asal</label>
                            <input type="text" name="asal" class="form-control" 
                                   value="<?= esc($barang['asal']) ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" class="form-control" 
                                   value="<?= esc($barang['tujuan']) ?>" required>
                        </div>

                        <!-- BARANG AKAN KEMBALI -->
                        <div class="col-md-6">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" 
                                       name="akan_kembali" id="akanKembali<?= $barang['id'] ?>" 
                                       value="Ya" <?= ($barang['akan_kembali'] ?? 'Tidak') === 'Ya' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="akanKembali<?= $barang['id'] ?>">
                                    Barang Akan Kembali
                                </label>
                            </div>
                        </div>

                        <!-- ESTIMASI KEMBALI -->
                        <div class="col-md-6" id="estimasiKembaliWrapper<?= $barang['id'] ?>" 
                             style="<?= ($barang['akan_kembali'] ?? 'Tidak') === 'Ya' ? '' : 'display: none;' ?>">
                            <label class="form-label">Estimasi Kembali</label>
                            <input type="date" name="estimasi_kembali" class="form-control" 
                                   value="<?= esc($barang['estimasi_kembali'] ?? '') ?>">
                        </div>
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

<!-- MODAL DETAIL UNTUK SETIAP BARANG -->
<?php foreach ($barangs as $barang): ?>
<div class="modal fade" id="DetailModal<?= $barang['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- INFO UTAMA -->
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-box-seam"></i> Informasi Barang
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted">No Agenda</small>
                                <div class="fw-semibold"><?= esc($barang['no_agenda']) ?></div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Tanggal</small>
                                <div><?= date('d/m/Y H:i', strtotime($barang['tanggal'])) ?></div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">No SPB</small>
                                <div><?= esc($barang['no_spb']) ?></div>
                            </div>
                            <div class="col-md-12">
                                <small class="text-muted">Nama Barang</small>
                                <div class="fw-semibold"><?= esc($barang['nama_barang']) ?></div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Jumlah Total</small>
                                <div><?= number_format($barang['jumlah']) ?> <?= esc($barang['satuan']) ?></div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Asal</small>
                                <div><?= esc($barang['asal']) ?></div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Tujuan</small>
                                <div><?= esc($barang['tujuan']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STATUS -->
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-info-circle"></i> Status & Mode
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Status Proses</small>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-secondary"><?= esc($barang['tipe']) ?></span>
                                        <?= $barang['is_partial'] === 'Ya'
                                            ? '<span class="badge bg-success">Partial</span>'
                                            : '<span class="badge bg-danger">Non Partial</span>' ?>
                                        <?= $barang['status'] === 'Selesai'
                                            ? '<span class="badge bg-dark">Selesai</span>'
                                            : '<span class="badge bg-warning text-dark">Belum Selesai</span>' ?>
                                        <?php if (($barang['akan_kembali'] ?? 'Tidak') === 'Ya'): ?>
                                            <span class="badge bg-primary">Akan Kembali</span>
                                            <?php if ($barang['estimasi_kembali']): ?>
                                                <span class="badge bg-info">
                                                    Estimasi: <?= date('d/m/Y', strtotime($barang['estimasi_kembali'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak Kembali</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Progress</small>
                                    <?php
                                    $total = (int)$barang['jumlah'];
                                    $kembali = (int)$barang['jumlah_kembali'];
                                    $keluar = (int)$barang['jumlah_keluar'];
                                    $kembaliPercent = $total > 0 ? ($kembali / $total * 100) : 0;
                                    $keluarPercent = $total > 0 ? ($keluar / $total * 100) : 0;
                                    ?>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: <?= $kembaliPercent ?>%">
                                            <?= $kembali ?> Masuk
                                        </div>
                                        <div class="progress-bar bg-warning" style="width: <?= $keluarPercent ?>%">
                                            <?= $keluar ?> Keluar
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>Masuk: <?= $kembali ?>/<?= $total ?></span>
                                        <span>Keluar: <?= $keluar ?>/<?= $total ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIWAYAT -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history"></i> Riwayat Aktivitas</span>
                        <span class="badge bg-secondary"><?= count($barang['history'] ?? []) ?> aktivitas</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($barang['history'])): ?>
                            <div class="table-responsive" style="max-height:300px">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Aksi</th>
                                            <th>Jumlah</th>
                                            <th>Sisa</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_reverse($barang['history']) as $hist): ?>
                                            <tr>
                                                <td>
                                                    <small><?= date('d/m/Y', strtotime($hist['created_at'])) ?></small><br>
                                                    <small class="text-muted"><?= date('H:i:s', strtotime($hist['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <?php
                                                    $badge = match ($hist['aksi']) {
                                                        'Registrasi' => 'primary',
                                                        'Masuk' => 'success',
                                                        'Keluar' => 'warning',
                                                        'Kembali' => 'info',
                                                        'Selesai' => 'dark',
                                                        default => 'secondary'
                                                    };
                                                    ?>
                                                    <span class="badge bg-<?= $badge ?>">
                                                        <?= esc($hist['aksi']) ?>
                                                    </span>
                                                </td>
                                                <td><?= esc($hist['jumlah']) ?> <?= esc($barang['satuan']) ?></td>
                                                <td><?= esc($hist['sisa']) ?> <?= esc($barang['satuan']) ?></td>
                                                <td><small><?= esc($hist['keterangan']) ?></small></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-inbox display-6"></i><br>
                                <h6 class="mt-3">Belum ada riwayat aktivitas</h6>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach ?>

<!-- MODAL BARANG KELUAR UNTUK SETIAP BARANG -->
<?php foreach ($barangs as $barang): ?>
<?php
$sisa = (int)$barang['jumlah'] - (int)$barang['jumlah_keluar'];
?>
<div class="modal fade"
     id="KeluarModal<?= $barang['id'] ?>"
     tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="post"
                  action="/registrasi/prosesKeluar/<?= $barang['id'] ?>">
                <?= csrf_field() ?>

                <!-- HEADER -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-box-arrow-up"></i>
                        Proses Barang Keluar
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- NAMA BARANG -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Barang</label>
                        <input type="text"
                               class="form-control"
                               value="<?= esc($barang['nama_barang']) ?>"
                               readonly>
                    </div>

                    <!-- JUMLAH TOTAL -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Total</label>
                        <input type="text"
                               class="form-control"
                               value="<?= number_format($barang['jumlah']) ?> <?= esc($barang['satuan']) ?>"
                               readonly>
                    </div>

                    <!-- SUDAH KELUAR -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sudah Keluar</label>
                        <input type="text"
                               class="form-control"
                               value="<?= number_format($barang['jumlah_keluar']) ?> <?= esc($barang['satuan']) ?>"
                               readonly>
                    </div>

                    <!-- JUMLAH KELUAR -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Jumlah Keluar Sekarang
                        </label>

                        <input type="number"
                               name="jumlah_keluar"
                               class="form-control"
                               min="1"
                               max="<?= $sisa ?>"
                               value="<?= $sisa > 0 ? $sisa : 1 ?>"
                               required>

                        <small class="text-muted">
                            Sisa barang yang dapat dikeluarkan:
                            <strong><?= $sisa ?> <?= esc($barang['satuan']) ?></strong>
                        </small>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="submit"
                            class="btn btn-success">
                        <i class="bi bi-box-arrow-up"></i>
                        Proses Keluar
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php endforeach ?>

<!-- MODAL BARANG MASUK UNTUK SETIAP BARANG -->
<?php foreach ($barangs as $barang): ?>
<?php
$sisaMasuk = (int)$barang['jumlah'] - (int)$barang['jumlah_kembali'];
?>
<div class="modal fade"
     id="MasukModal<?= $barang['id'] ?>"
     tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="post"
                  action="/registrasi/prosesMasuk/<?= $barang['id'] ?>">
                <?= csrf_field() ?>

                <!-- HEADER -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-box-arrow-in-down"></i>
                        Proses Barang Masuk
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- NAMA BARANG -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Barang</label>
                        <input type="text"
                               class="form-control"
                               value="<?= esc($barang['nama_barang']) ?>"
                               readonly>
                    </div>

                    <!-- JUMLAH TOTAL -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Total</label>
                        <input type="text"
                               class="form-control"
                               value="<?= number_format($barang['jumlah']) ?> <?= esc($barang['satuan']) ?>"
                               readonly>
                    </div>

                    <!-- SUDAH MASUK -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sudah Masuk</label>
                        <input type="text"
                               class="form-control"
                               value="<?= number_format($barang['jumlah_kembali']) ?> <?= esc($barang['satuan']) ?>"
                               readonly>
                    </div>

                    <!-- JUMLAH MASUK -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Jumlah Masuk Sekarang
                        </label>

                        <input type="number"
                               name="jumlah_masuk"
                               class="form-control"
                               min="1"
                               max="<?= $sisaMasuk ?>"
                               value="<?= $sisaMasuk > 0 ? $sisaMasuk : 1 ?>"
                               required>

                        <small class="text-muted">
                            Sisa barang yang dapat dimasukkan:
                            <strong><?= $sisaMasuk ?> <?= esc($barang['satuan']) ?></strong>
                        </small>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="submit"
                            class="btn btn-success">
                        <i class="bi bi-box-arrow-in-down"></i>
                        Proses Masuk
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php endforeach ?>

<!-- MODAL SELESAI UNTUK SETIAP BARANG -->
<?php foreach ($barangs as $barang): ?>
<?php if ($barang['status'] !== 'Selesai'): ?>
<div class="modal fade"
     id="SelesaiModal<?= $barang['id'] ?>"
     tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="post"
                  action="/registrasi/selesai/<?= $barang['id'] ?>">
                <?= csrf_field() ?>

                <!-- HEADER -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-check2-circle"></i>
                        Tandai Sebagai Selesai
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Anda akan menandai barang ini sebagai <strong>Selesai</strong>.
                        Pastikan semua proses sudah selesai.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Barang</label>
                        <input type="text"
                               class="form-control"
                               value="<?= esc($barang['nama_barang']) ?>"
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan Tambahan (Opsional)</label>
                        <textarea name="catatan_selesai" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="Tambahkan catatan jika perlu..."></textarea>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="submit"
                            class="btn btn-dark">
                        <i class="bi bi-check2-circle"></i>
                        Ya, Tandai Selesai
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php endif; ?>
<?php endforeach ?>

<!-- MODAL REGISTRASI BARANG MASUK -->
<div class="modal fade" id="registrasiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/registrasi/store">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Registrasi Barang Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <input type="hidden" name="tanggal" value="<?= esc($tanggal ?? date('Y-m-d H:i:s')) ?>">

                        <div class="col-md-6">
                            <label class="form-label">No Agenda</label>
                            <input type="text" name="no_agenda" value="<?= esc($noAgenda ?? 'M-0000') ?>" 
                                   class="form-control" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipe Barang</label>
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

                        <div class="col-md-12">
                            <label class="form-label">No SPB</label>
                            <input type="text" name="no_spb" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Total</label>
                            <input type="number" name="jumlah" class="form-control jumlah-total" 
                                   id="totalJumlahMasuk" value="1" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Asal Barang</label>
                            <input type="text" name="asal" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tujuan Barang</label>
                            <input type="text" name="tujuan" class="form-control" required>
                        </div>

                        <!-- MODE SECTION -->
                        <div class="col-12 mt-3">
                            <h6 class="border-bottom pb-2">Mode Pengiriman</h6>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="partialCheckMasuk" name="is_partial">
                                <label class="form-check-label" for="partialCheckMasuk">
                                    <strong>Partial (Bertahap)</strong>
                                </label>
                                <div class="form-text">Barang masuk sebagian demi sebagian</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="kembaliCheckMasuk" name="akan_kembali" value="Ya">
                                <label class="form-check-label" for="kembaliCheckMasuk">
                                    <strong>Barang Akan Kembali</strong>
                                </label>
                                <div class="form-text">Barang akan dikembalikan setelah digunakan</div>
                            </div>
                        </div>

                        <!-- PARTIAL FIELDS -->
                        <div class="col-md-6 d-none" id="jumlahWrapperMasuk">
                            <label class="form-label">Jumlah Masuk Pertama</label>
                            <input type="number" name="jumlah_masuk" class="form-control jumlah-masuk" 
                                   id="jumlahInputMasuk" min="1" value="1">
                            <div class="form-text">Jumlah yang masuk pertama kali</div>
                        </div>

                        <div class="col-md-6 d-none" id="sisaWrapperMasuk">
                            <label class="form-label">Sisa yang Belum Masuk</label>
                            <input type="text" class="form-control" id="sisaInputMasuk" readonly>
                            <div class="form-text">Akan otomatis terhitung</div>
                        </div>

                        <!-- ESTIMASI KEMBALI -->
                        <div class="col-md-6 d-none" id="estimasiWrapperMasuk">
                            <label class="form-label">Estimasi Tanggal Kembali</label>
                            <input type="date" name="estimasi_kembali" class="form-control" 
                                   id="estimasiDateMasuk">
                            <div class="form-text">Perkiraan tanggal barang akan kembali</div>
                        </div>
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

<!-- MODAL REGISTRASI LAPTOP -->
<div class="modal fade" id="laptopModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/laptop/store">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-laptop"></i> 
                        Registrasi Laptop Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengguna" class="form-control" 
                                   placeholder="Masukkan nama pengguna" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor ID Card <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_id_card" class="form-control" 
                                   placeholder="Contoh: PEG-2024-001" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Instansi/Divisi <span class="text-danger">*</span></label>
                            <input type="text" name="instansi_divisi" class="form-control" 
                                   placeholder="Nama Sekolah/Divisi" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Merek Laptop <span class="text-danger">*</span></label>
                            <select name="merek" class="form-select" required>
                                <option value="">-- Pilih Merek --</option>
                                <option value="Dell">Dell</option>
                                <option value="HP">HP</option>
                                <option value="Lenovo">Lenovo</option>
                                <option value="Asus">Asus</option>
                                <option value="Acer">Acer</option>
                                <option value="Apple">Apple</option>
                                <option value="Toshiba">Toshiba</option>
                                <option value="Fujitsu">Fujitsu</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipe Laptop</label>
                            <input type="text" name="tipe_laptop" class="form-control" 
                                   placeholder="Contoh: Latitude 5420, ThinkPad X1">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Seri <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_seri" class="form-control" 
                                   placeholder="Nomor seri laptop" required>
                            <small class="text-muted">Nomor seri harus sesuai</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Berlaku Sampai Dengan <span class="text-danger">*</span></label>
                            <input type="date" name="berlaku_sampai" class="form-control" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Spesifikasi Lain (RAM, Processor, Storage, dll)</label>
                            <textarea name="spesifikasi_lain" class="form-control" rows="3"
                                      placeholder="Contoh: Intel Core i7, RAM 16GB, SSD 512GB, Windows 11 Pro"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Data Laptop
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL REGISTRASI BARANG KELUAR -->
<div class="modal fade" id="keluarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/registrasi/store">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Registrasi Laptop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">No Registrasi</label>
                            <input type="text" name="no_registrasi" value="<?= esc($noAgendaKeluar ?? 'K-0000') ?>" 
                                   class="form-control" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="no_registrasi" value="<?= esc($noAgendaKeluar ?? 'K-0000') ?>" 
                                   class="form-control" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No Ser</label>
                            <input type="text" name="no_agenda" value="<?= esc($noAgendaKeluar ?? 'K-0000') ?>" 
                                   class="form-control" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipe Barang</label>
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

                        <div class="col-md-12">
                            <label class="form-label">No SPB</label>
                            <input type="text" name="no_spb" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Total</label>
                            <input type="number" name="jumlah" class="form-control jumlah-total" 
                                   id="totalJumlahKeluar" value="1" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Asal (Perusahaan)</label>
                            <input type="text" name="asal" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" class="form-control" required>
                        </div>

                        <!-- MODE SECTION -->
                        <div class="col-12 mt-3">
                            <h6 class="border-bottom pb-2">Mode Pengiriman</h6>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="partialCheckKeluar" name="is_partial">
                                <label class="form-check-label" for="partialCheckKeluar">
                                    <strong>Partial (Bertahap)</strong>
                                </label>
                                <div class="form-text">Barang keluar sebagian demi sebagian</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="kembaliCheckKeluar" name="akan_kembali" value="Ya">
                                <label class="form-check-label" for="kembaliCheckKeluar">
                                    <strong>Barang Akan Kembali</strong>
                                </label>
                                <div class="form-text">Barang akan dikembalikan setelah digunakan</div>
                            </div>
                        </div>

                        <!-- PARTIAL FIELDS -->
                        <div class="col-md-6 d-none" id="jumlahWrapperKeluar">
                            <label class="form-label">Jumlah Keluar Pertama</label>
                            <input type="number" name="jumlah_masuk" class="form-control jumlah-masuk" 
                                   id="jumlahInputKeluar" min="1" value="1">
                            <div class="form-text">Jumlah yang keluar pertama kali</div>
                        </div>

                        <div class="col-md-6 d-none" id="sisaWrapperKeluar">
                            <label class="form-label">Sisa yang Belum Keluar</label>
                            <input type="text" class="form-control" id="sisaInputKeluar" readonly>
                            <div class="form-text">Akan otomatis terhitung</div>
                        </div>

                        <!-- ESTIMASI KEMBALI -->
                        <div class="col-md-6 d-none" id="estimasiWrapperKeluar">
                            <label class="form-label">Estimasi Tanggal Kembali</label>
                            <input type="date" name="estimasi_kembali" class="form-control" 
                                   id="estimasiDateKeluar">
                            <div class="form-text">Perkiraan tanggal barang akan kembali</div>
                        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup untuk modal edit barang
    <?php foreach ($barangs as $barang): ?>
    const akanKembaliCheckbox<?= $barang['id'] ?> = document.getElementById('akanKembali<?= $barang['id'] ?>');
    const estimasiWrapper<?= $barang['id'] ?> = document.getElementById('estimasiKembaliWrapper<?= $barang['id'] ?>');
    
    if (akanKembaliCheckbox<?= $barang['id'] ?> && estimasiWrapper<?= $barang['id'] ?>) {
        akanKembaliCheckbox<?= $barang['id'] ?>.addEventListener('change', function() {
            if (this.checked) {
                estimasiWrapper<?= $barang['id'] ?>.style.display = 'block';
                const estimasiInput = estimasiWrapper<?= $barang['id'] ?>.querySelector('input[name="estimasi_kembali"]');
                if (estimasiInput && !estimasiInput.value) {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    estimasiInput.value = tomorrow.toISOString().split('T')[0];
                    estimasiInput.min = new Date().toISOString().split('T')[0];
                }
            } else {
                estimasiWrapper<?= $barang['id'] ?>.style.display = 'none';
            }
        });
    }
    <?php endforeach; ?>

    // Setup modal registrasi barang masuk
    setupModalRegistrasi('registrasiModal', 'Masuk');
    
    // Setup modal registrasi barang keluar
    setupModalRegistrasi('keluarModal', 'Keluar');

    function setupModalRegistrasi(modalId, type) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const partialCheck = modal.querySelector(`#partialCheck${type}`);
        const jumlahWrapper = modal.querySelector(`#jumlahWrapper${type}`);
        const jumlahInput = modal.querySelector(`#jumlahInput${type}`);
        const sisaWrapper = modal.querySelector(`#sisaWrapper${type}`);
        const sisaInput = modal.querySelector(`#sisaInput${type}`);
        const totalJumlahInput = modal.querySelector(`#totalJumlah${type}`);
        
        const kembaliCheck = modal.querySelector(`#kembaliCheck${type}`);
        const estimasiWrapper = modal.querySelector(`#estimasiWrapper${type}`);
        const estimasiDate = modal.querySelector(`#estimasiDate${type}`);

        // PARTIAL CHECKBOX
        if (partialCheck) {
            partialCheck.addEventListener('change', function() {
                if (this.checked) {
                    jumlahWrapper.classList.remove('d-none');
                    sisaWrapper.classList.remove('d-none');
                    
                    const total = Math.max(1, parseInt(totalJumlahInput.value) || 1);
                    if (jumlahInput) {
                        jumlahInput.max = total;
                        jumlahInput.value = total;
                        sisaInput.value = 0;
                    }
                } else {
                    jumlahWrapper.classList.add('d-none');
                    sisaWrapper.classList.add('d-none');
                    if (jumlahInput) jumlahInput.value = 1;
                    if (sisaInput) sisaInput.value = '';
                }
            });

            // Hitung sisa
            if (jumlahInput && sisaInput && totalJumlahInput) {
                jumlahInput.addEventListener('input', calculateSisa);
                totalJumlahInput.addEventListener('input', calculateSisa);
                
                function calculateSisa() {
                    const total = Math.max(1, parseInt(totalJumlahInput.value) || 1);
                    const masuk = Math.max(1, parseInt(jumlahInput.value) || 1);
                    
                    jumlahInput.max = total;
                    
                    if (masuk > total) {
                        jumlahInput.value = total;
                        sisaInput.value = 0;
                    } else {
                        sisaInput.value = total - masuk;
                    }
                }
                
                calculateSisa();
            }
        }

        // KEMBALI CHECKBOX
        if (kembaliCheck) {
            kembaliCheck.addEventListener('change', function() {
                if (this.checked) {
                    estimasiWrapper.classList.remove('d-none');
                    
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    if (estimasiDate) {
                        estimasiDate.value = estimasiDate.value || tomorrow.toISOString().split('T')[0];
                        estimasiDate.min = new Date().toISOString().split('T')[0];
                    }
                } else {
                    estimasiWrapper.classList.add('d-none');
                }
            });
        }

        // Set default date
        if (estimasiDate && !estimasiDate.value) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            estimasiDate.value = tomorrow.toISOString().split('T')[0];
        }
    }

    // Handle jumlah total changes
    document.querySelectorAll('.jumlah-total').forEach(input => {
        input.addEventListener('input', function() {
            const value = parseInt(this.value) || 1;
            if (value < 1) this.value = 1;
            
            const modal = this.closest('.modal-content');
            if (modal) {
                const partialInput = modal.querySelector('.jumlah-masuk');
                const sisaInput = modal.querySelector('[id^="sisaInput"]');
                if (partialInput && sisaInput) {
                    const total = Math.max(1, value);
                    const masuk = Math.max(1, parseInt(partialInput.value) || 1);
                    
                    partialInput.max = total;
                    
                    if (masuk > total) {
                        partialInput.value = total;
                        sisaInput.value = 0;
                    } else {
                        sisaInput.value = total - masuk;
                    }
                }
            }
        });
    });

    // Validasi form
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const partialCheck = this.querySelector('[id^="partialCheck"]:checked');
            const jumlahInput = this.querySelector('.jumlah-masuk');
            const totalJumlahInput = this.querySelector('.jumlah-total');
            
            if (partialCheck && jumlahInput) {
                const total = Math.max(1, parseInt(totalJumlahInput.value) || 1);
                const partial = Math.max(1, parseInt(jumlahInput.value) || 1);
                
                if (partial <= 0) {
                    e.preventDefault();
                    alert('Jumlah masuk harus lebih dari 0 untuk partial');
                    jumlahInput.focus();
                    return false;
                }
                
                if (partial > total) {
                    e.preventDefault();
                    alert('Jumlah masuk tidak boleh melebihi total jumlah');
                    jumlahInput.focus();
                    return false;
                }
            }
            
            return true;
        });
    });
});
</script>

<?= $this->endSection() ?>