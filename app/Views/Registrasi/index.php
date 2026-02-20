<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-3">Data Barang Keluar / Masuk & Laptop</h2>

<ul class="nav nav-tabs mb-3" id="mainTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="barang-tab" data-bs-toggle="tab" data-bs-target="#barang" type="button" role="tab" aria-controls="barang" aria-selected="true">
            <i class="bi bi-box"></i> Barang
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="laptop-tab" data-bs-toggle="tab" data-bs-target="#laptop" type="button" role="tab" aria-controls="laptop" aria-selected="false">
            <i class="bi bi-laptop"></i> Laptop
        </button>
    </li>
</ul>

<div class="tab-content" id="mainTabContent">
    <!-- ========== TAB BARANG ========== -->
    <div class="tab-pane fade show active" id="barang" role="tabpanel" aria-labelledby="barang-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registrasiModal">
                    <i class="bi bi-box-arrow-down"></i> Barang Masuk
                </button>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#keluarModal">
                    <i class="bi bi-box-arrow-up"></i> Barang Keluar
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
                        <td><?= esc($barang['tipe']) ?></td>
                        <td><?= esc($barang['is_partial']) ?></td>
                        <td><?= esc($barang['status']) ?></td>
                        <td>
                            <span class="badge bg-<?= $keterangan === 'Belum Kembali' ? 'primary' : ($keterangan === 'Tidak Kembali' ? 'warning' : 'info') ?>">
                                <?= $keterangan ?>
                            </span>
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
                                        <?php if ($keterangan === 'Tidak Kembali'): ?>
                                            <?php if ($isPartial && !$masukPenuh): ?>
                                                <button class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#MasukModal<?= $barang['id'] ?>">
                                                    <i class="bi bi-box-arrow-in-down"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
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
                                        <?php if ($keterangan === 'Tidak Kembali'): ?>
                                            <?php if ($isPartial && !$keluarPenuh): ?>
                                                <button class="btn btn-sm btn-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#KeluarModal<?= $barang['id'] ?>">
                                                    <i class="bi bi-box-arrow-up"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
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
    </div>

    <!-- ========== TAB LAPTOP ========== -->
    <div class="tab-pane fade" id="laptop" role="tabpanel" aria-labelledby="laptop-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#laptopModal">
                    <i class="bi bi-laptop"></i> Registrasi Laptop
                </button>
                <a href="/barang/laptop/export" class="btn btn-warning ms-2">
                    <i class="bi bi-file-earmark-excel"></i> Export
                </a>
            </div>

            <form class="d-flex gap-2" id="searchLaptopForm">
                <input type="text" id="searchKeyword" class="form-control" 
                       placeholder="Cari pengguna / merek / seri / no registrasi..."
                       value="<?= esc($keywordLaptop ?? '') ?>">
                <select id="searchStatus" class="form-select" style="width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="Masih Berlaku" <?= ($statusLaptop ?? '') == 'Masih Berlaku' ? 'selected' : '' ?>>Masih Berlaku</option>
                    <option value="Tidak Berlaku" <?= ($statusLaptop ?? '') == 'Tidak Berlaku' ? 'selected' : '' ?>>Tidak Berlaku</option>
                    <option value="Diperpanjang" <?= ($statusLaptop ?? '') == 'Diperpanjang' ? 'selected' : '' ?>>Diperpanjang</option>
                </select>
                <button type="submit" class="btn btn-outline-secondary" id="btnSearchLaptop">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

        <div id="laptopTableContainer">
            <!-- Tabel Laptop akan dimuat via AJAX -->
            <?= $this->include('laptop/table') ?>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL QR CODE -->
<!-- ============================================ -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">
                    <i class="bi bi-qr-code"></i> 
                    QR Code Laptop
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer" class="mb-3"></div>
                <p id="qrCodeText" class="text-muted small"></p>
                <button type="button" class="btn btn-sm btn-primary" onclick="downloadQRCode()">
                    <i class="bi bi-download"></i> Download QR
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL DETAIL LAPTOP (LOOP) -->
<!-- ============================================ -->
<?php foreach ($laptops as $laptop): ?>
<div class="modal fade" id="detailLaptopModal<?= $laptop['id'] ?>" tabindex="-1" aria-labelledby="detailLaptopModalLabel<?= $laptop['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailLaptopModalLabel<?= $laptop['id'] ?>">
                    <i class="bi bi-laptop"></i> 
                    Detail Laptop
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-info-circle"></i> Informasi Laptop</span>
                        <?php if (isset($laptop['jenis'])): ?>
                            <span class="badge bg-<?= $laptop['jenis'] == 'Pegawai' ? 'primary' : 'info' ?>">
                                <?= esc($laptop['jenis']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted">No. Registrasi</small>
                                <div class="fw-bold"><?= esc($laptop['no_registrasi'] ?? '-') ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Tanggal Registrasi</small>
                                <div><?= isset($laptop['created_at']) ? date('d/m/Y H:i', strtotime($laptop['created_at'])) : '-' ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Nama Pengguna</small>
                                <div class="fw-semibold"><?= esc($laptop['nama_pengguna'] ?? '') ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Nomor ID Card</small>
                                <div class="fw-semibold"><?= esc($laptop['nomor_id_card'] ?? '') ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Instansi/Divisi</small>
                                <div><?= esc($laptop['instansi_divisi'] ?? '') ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Merek & Tipe</small>
                                <div><?= esc($laptop['merek'] ?? '') ?> <?= esc($laptop['tipe_laptop'] ?? '') ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Nomor Seri</small>
                                <div class="fw-semibold"><?= esc($laptop['nomor_seri'] ?? '') ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Berlaku Sampai</small>
                                <div>
                                    <?= isset($laptop['berlaku_sampai']) ? date('d/m/Y', strtotime($laptop['berlaku_sampai'])) : '-' ?>
                                    <?php if (isset($laptop['berlaku_sampai']) && strtotime($laptop['berlaku_sampai']) < time()): ?>
                                        <span class="badge bg-danger ms-2">Expired</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">Spesifikasi Lain</small>
                                <div class="p-2 bg-light rounded">
                                    <?= !empty($laptop['spesifikasi_lain']) ? nl2br(esc($laptop['spesifikasi_lain'])) : '<span class="text-muted">-</span>' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-activity"></i> Status Laptop
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-2">Status Saat Ini</small>
                                <?php
                                $status = $laptop['status'] ?? 'Tidak Diketahui';
                                $badge = match($status) {
                                    'Masih Berlaku' => 'success',
                                    'Tidak Berlaku' => 'secondary',
                                    'Diperpanjang' => 'primary',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $badge ?> p-2" style="font-size: 1rem;">
                                    <?= $status ?>
                                </span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-2">Keterangan</small>
                                <div class="p-2 bg-light rounded">
                                    <?= !empty($laptop['keterangan']) ? esc($laptop['keterangan']) : '<span class="text-muted">-</span>' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history"></i> Riwayat Registrasi & Perpanjangan</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($laptop['logs'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $logSorted = $laptop['logs'];
                                        usort($logSorted, function($a, $b) {
                                            return strtotime($b['created_at']) - strtotime($a['created_at']);
                                        });
                                        
                                        foreach ($logSorted as $log): 
                                        ?>
                                            <tr>
                                                <td>
                                                    <small><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <?php
                                                    $badge = match ($log['aksi']) {
                                                        'Registrasi' => 'primary',
                                                        'Perpanjangan' => 'warning',
                                                        'Perubahan Data' => 'info',
                                                        'Nonaktif' => 'secondary',
                                                        default => 'secondary'
                                                    };
                                                    ?>
                                                    <span class="badge bg-<?= $badge ?>">
                                                        <?= esc($log['aksi']) ?>
                                                    </span>
                                                </td>
                                                <td><small><?= esc($log['keterangan']) ?></small></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-inbox display-6"></i><br>
                                <h6 class="mt-3">Belum ada riwayat perpanjangan</h6>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="printLaptop(<?= $laptop['id'] ?>)">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- ============================================ -->
<!-- MODAL REGISTRASI LAPTOP (DENGAN DROPDOWN JENIS) -->
<!-- ============================================ -->
<div class="modal fade" id="laptopModal" tabindex="-1" aria-labelledby="laptopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/barang/laptop/store">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="laptopModalLabel">
                        <i class="bi bi-laptop"></i> 
                        Registrasi Laptop Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="row g-3">
                        <!-- DROPDOWN JENIS -->
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pengguna <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select" required>
                                <option value="">-- Pilih Jenis Pengguna --</option>
                                <option value="Pegawai">Pegawai</option>
                                <option value="Non Pegawai">Non Pegawai</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengguna" class="form-control" 
                                   placeholder="Masukkan nama lengkap pengguna" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor ID Card <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_id_card" class="form-control" 
                                   placeholder="Masukkan Nomor ID Card Pengguna" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Instansi/Divisi <span class="text-danger">*</span></label>
                            <input type="text" name="instansi_divisi" class="form-control" 
                                   placeholder="Nama Sekolah/Divisi/Instansi" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Merek Laptop <span class="text-danger">*</span></label>
                            <select name="merek" class="form-select select2" required>
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
                            <small class="text-muted">Nomor seri harus unik</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Berlaku Sampai <span class="text-danger">*</span></label>
                            <input type="date" name="berlaku_sampai" class="form-control" 
                                   min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+1 year')) ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Spesifikasi Lain (RAM, Processor, Storage, dll)</label>
                            <textarea name="spesifikasi_lain" class="form-control" rows="3"
                                      placeholder="Contoh: Intel Core i7, RAM 16GB, SSD 512GB, Windows 11 Pro"></textarea>
                        </div>

                        <input type="hidden" name="status" value="Masih Berlaku">
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

<!-- ============================================ -->
<!-- MODAL PERPANJANG LAPTOP (SATU UNTUK SEMUA) -->
<!-- ============================================ -->
<div class="modal fade" id="perpanjangLaptopModal" tabindex="-1" aria-labelledby="perpanjangLaptopModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="/barang/laptop/perpanjang" id="formPerpanjangLaptop">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="perpanjangLaptopModalLabel">
                        <i class="bi bi-arrow-repeat"></i> 
                        Perpanjang Masa Berlaku Laptop
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="laptop_id" id="perpanjang_laptop_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control" id="perpanjang_nama" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nomor Seri</label>
                        <input type="text" class="form-control" id="perpanjang_seri" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">No. Registrasi Saat Ini</label>
                        <input type="text" class="form-control" id="perpanjang_no_registrasi" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Registrasi Ke</label>
                        <input type="text" class="form-control" id="perpanjang_registrasi_ke" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Berlaku Sampai Saat Ini</label>
                        <input type="text" class="form-control" id="perpanjang_berlaku_sampai_lama" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Berlaku Sampai Baru <span class="text-danger">*</span></label>
                        <input type="date" name="berlaku_sampai_baru" class="form-control" 
                               id="perpanjang_berlaku_sampai_baru"
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        <small class="text-muted">Tanggal harus setelah tanggal berlaku saat ini</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan Perpanjangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" 
                                  id="perpanjang_keterangan"
                                  placeholder="Contoh: Perpanjangan tahun ke-2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-repeat"></i> Perpanjang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL EDIT LAPTOP (DENGAN DROPDOWN JENIS) -->
<!-- ============================================ -->
<?php foreach ($laptops as $laptop): ?>
<div class="modal fade" id="editLaptopModal<?= $laptop['id'] ?>" tabindex="-1" aria-labelledby="editLaptopModalLabel<?= $laptop['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/barang/laptop/update/<?= $laptop['id'] ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="editLaptopModalLabel<?= $laptop['id'] ?>">
                        <i class="bi bi-pencil-square"></i> 
                        Edit Data Laptop
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Nomor Registrasi: <strong><?= esc($laptop['no_registrasi'] ?? 'Belum tersedia') ?></strong> (Tidak dapat diubah)
                    </div>
                    
                    <div class="row g-3">
                        <!-- DROPDOWN JENIS -->
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pengguna <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select" required>
                                <option value="">-- Pilih Jenis Pengguna --</option>
                                <option value="Pegawai" <?= (isset($laptop['jenis']) && $laptop['jenis'] == 'Pegawai') ? 'selected' : '' ?>>Pegawai</option>
                                <option value="Non Pegawai" <?= (isset($laptop['jenis']) && $laptop['jenis'] == 'Non Pegawai') ? 'selected' : '' ?>>Non Pegawai</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengguna" class="form-control" 
                                   value="<?= esc($laptop['nama_pengguna'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor ID Card <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_id_card" class="form-control" 
                                   value="<?= esc($laptop['nomor_id_card'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Instansi/Divisi <span class="text-danger">*</span></label>
                            <input type="text" name="instansi_divisi" class="form-control" 
                                   value="<?= esc($laptop['instansi_divisi'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Merek Laptop <span class="text-danger">*</span></label>
                            <select name="merek" class="form-select" required>
                                <option value="">-- Pilih Merek --</option>
                                <?php
                                $mereks = ['Dell', 'HP', 'Lenovo', 'Asus', 'Acer', 'Apple', 'Toshiba', 'Fujitsu', 'Lainnya'];
                                foreach ($mereks as $m): ?>
                                    <option value="<?= $m ?>" <?= (isset($laptop['merek']) && $laptop['merek'] == $m) ? 'selected' : '' ?>><?= $m ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipe Laptop</label>
                            <input type="text" name="tipe_laptop" class="form-control" 
                                   value="<?= esc($laptop['tipe_laptop'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor Seri <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_seri" class="form-control" 
                                   value="<?= esc($laptop['nomor_seri'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Berlaku Sampai <span class="text-danger">*</span></label>
                            <input type="date" name="berlaku_sampai" class="form-control" 
                                   value="<?= $laptop['berlaku_sampai'] ?? date('Y-m-d', strtotime('+1 year')) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Masih Berlaku" <?= (isset($laptop['status']) && $laptop['status'] == 'Masih Berlaku') ? 'selected' : '' ?>>Masih Berlaku</option>
                                <option value="Tidak Berlaku" <?= (isset($laptop['status']) && $laptop['status'] == 'Tidak Berlaku') ? 'selected' : '' ?>>Tidak Berlaku</option>
                                <option value="Diperpanjang" <?= (isset($laptop['status']) && $laptop['status'] == 'Diperpanjang') ? 'selected' : '' ?>>Diperpanjang</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Spesifikasi Lain</label>
                            <textarea name="spesifikasi_lain" class="form-control" rows="3"><?= esc($laptop['spesifikasi_lain'] ?? '') ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"><?= esc($laptop['keterangan'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL HAPUS LAPTOP -->
<!-- ============================================ -->
<div class="modal fade" id="deleteLaptopModal<?= $laptop['id'] ?>" tabindex="-1" aria-labelledby="deleteLaptopModalLabel<?= $laptop['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLaptopModalLabel<?= $laptop['id'] ?>">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus data laptop:</p>
                <p><strong><?= esc($laptop['nama_pengguna'] ?? '') ?></strong><br>
                <small><?= esc($laptop['merek'] ?? '') ?> <?= esc($laptop['tipe_laptop'] ?? '') ?> (<?= esc($laptop['nomor_seri'] ?? '') ?>)</small></p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="/barang/laptop/delete/<?= $laptop['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus permanen?')">
                    <i class="bi bi-trash"></i> Ya, Hapus
                </a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- ============================================ -->
<!-- MODAL EDIT BARANG -->
<!-- ============================================ -->
<?php foreach ($barangs as $barang): ?>
<div class="modal fade" id="EditModal<?= $barang['id'] ?>" tabindex="-1" aria-labelledby="EditModalLabel<?= $barang['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/registrasi/update/<?= $barang['id'] ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="EditModalLabel<?= $barang['id'] ?>">Edit Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">No Agenda</label>
                            <input type="text" value="<?= esc($barang['no_agenda']) ?>" class="form-control" readonly>
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
                            <input type="text" name="no_spb" class="form-control" value="<?= esc($barang['no_spb']) ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" value="<?= esc($barang['nama_barang']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Total</label>
                            <input type="number" name="jumlah" class="form-control" value="<?= esc($barang['jumlah']) ?>" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="<?= esc($barang['satuan']) ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Asal</label>
                            <input type="text" name="asal" class="form-control" value="<?= esc($barang['asal']) ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" class="form-control" value="<?= esc($barang['tujuan']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="akan_kembali" id="akanKembali<?= $barang['id'] ?>" value="Ya" <?= ($barang['akan_kembali'] ?? 'Tidak') === 'Ya' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="akanKembali<?= $barang['id'] ?>">
                                    Barang Akan Kembali
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6" id="estimasiKembaliWrapper<?= $barang['id'] ?>" style="<?= ($barang['akan_kembali'] ?? 'Tidak') === 'Ya' ? '' : 'display: none;' ?>">
                            <label class="form-label">Estimasi Kembali</label>
                            <input type="date" name="estimasi_kembali" class="form-control" value="<?= esc($barang['estimasi_kembali'] ?? '') ?>">
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

<!-- ============================================ -->
<!-- MODAL DETAIL BARANG -->
<!-- ============================================ -->
<div class="modal fade" id="DetailModal<?= $barang['id'] ?>" tabindex="-1" aria-labelledby="DetailModalLabel<?= $barang['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DetailModalLabel<?= $barang['id'] ?>">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                                        <?php 
                                        $historySorted = $barang['history'];
                                        usort($historySorted, function($a, $b) {
                                            return strtotime($a['created_at']) - strtotime($b['created_at']);
                                        });
                                        
                                        foreach ($historySorted as $hist): 
                                        ?>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL KELUAR BARANG -->
<!-- ============================================ -->
<div class="modal fade" id="KeluarModal<?= $barang['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="/registrasi/prosesKeluar/<?= $barang['id'] ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-box-arrow-up"></i> Proses Barang Keluar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Barang</label>
                        <input type="text" class="form-control" value="<?= esc($barang['nama_barang']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Total</label>
                        <input type="text" class="form-control" value="<?= number_format($barang['jumlah']) ?> <?= esc($barang['satuan']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sudah Keluar</label>
                        <input type="text" class="form-control" value="<?= number_format($barang['jumlah_keluar']) ?> <?= esc($barang['satuan']) ?>" readonly>
                    </div>
                    <?php $sisaKeluar = (int)$barang['jumlah'] - (int)$barang['jumlah_keluar']; ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Keluar Sekarang</label>
                        <input type="number" name="jumlah_keluar" class="form-control" min="1" max="<?= $sisaKeluar ?>" value="<?= $sisaKeluar > 0 ? $sisaKeluar : 1 ?>" required>
                        <small class="text-muted">Sisa barang yang dapat dikeluarkan: <strong><?= $sisaKeluar ?> <?= esc($barang['satuan']) ?></strong></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-box-arrow-up"></i> Proses Keluar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL MASUK BARANG -->
<!-- ============================================ -->
<div class="modal fade" id="MasukModal<?= $barang['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="/registrasi/prosesMasuk/<?= $barang['id'] ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-box-arrow-in-down"></i> Proses Barang Masuk
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Barang</label>
                        <input type="text" class="form-control" value="<?= esc($barang['nama_barang']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Total</label>
                        <input type="text" class="form-control" value="<?= number_format($barang['jumlah']) ?> <?= esc($barang['satuan']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sudah Masuk</label>
                        <input type="text" class="form-control" value="<?= number_format($barang['jumlah_kembali']) ?> <?= esc($barang['satuan']) ?>" readonly>
                    </div>
                    <?php $sisaMasuk = (int)$barang['jumlah'] - (int)$barang['jumlah_kembali']; ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah Masuk Sekarang</label>
                        <input type="number" name="jumlah_masuk" class="form-control" min="1" max="<?= $sisaMasuk ?>" value="<?= $sisaMasuk > 0 ? $sisaMasuk : 1 ?>" required>
                        <small class="text-muted">Sisa barang yang dapat dimasukkan: <strong><?= $sisaMasuk ?> <?= esc($barang['satuan']) ?></strong></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-box-arrow-in-down"></i> Proses Masuk
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- ============================================ -->
<!-- MODAL REGISTRASI BARANG MASUK -->
<!-- ============================================ -->
<div class="modal fade" id="registrasiModal" tabindex="-1" aria-labelledby="registrasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/registrasi/store">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="registrasiModalLabel">Registrasi Barang Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <input type="hidden" name="tanggal" value="<?= esc($tanggal ?? date('Y-m-d H:i:s')) ?>">

                        <div class="col-md-6">
                            <label class="form-label">No Agenda</label>
                            <input type="text" name="no_agenda" value="<?= esc($noAgenda ?? 'M-0000') ?>" class="form-control" readonly>
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
                            <input type="number" name="jumlah" class="form-control jumlah-total" id="totalJumlahMasuk" value="1" min="1" required>
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
                            <label class="form-label">Tujuan Barang <span class="text-danger">*</span></label>
                            <select name="tujuan" class="form-select" required>
                                <option value="">-- Pilih Divisi --</option>
                                <optgroup label="Divisi Produksi">
                                    <option value="Divisi Senjata">Divisi Senjata</option>
                                    <option value="Divisi Kendaraan Khusus">Divisi Kendaraan Khusus</option>
                                    <option value="Divisi Munisi">Divisi Munisi</option>
                                    <option value="Divisi Tempa & Cor">Divisi Tempa & Cor</option>
                                    <option value="Divisi Produk Industri & Jasa">Divisi Produk Industri & Jasa</option>
                                </optgroup>
                                <optgroup label="Divisi Pendukung">
                                    <option value="Divisi Litbang (R&D)">Divisi Litbang (R&D)</option>
                                    <option value="Divisi Quality Assurance (QA)">Divisi Quality Assurance (QA)</option>
                                    <option value="Divisi Maintenance, Repair & Overhaul (MRO)">Divisi Maintenance, Repair & Overhaul (MRO)</option>
                                    <option value="Divisi SDM & Pengembangan Organisasi">Divisi SDM & Pengembangan Organisasi</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <h6 class="border-bottom pb-2">Mode Pengiriman</h6>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="partialCheckMasuk" name="is_partial">
                                <label class="form-check-label" for="partialCheckMasuk">
                                    <strong>Partial (Bertahap)</strong>
                                </label>
                                <div class="form-text">Barang masuk sebagian demi sebagian</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="kembaliCheckMasuk" name="akan_kembali" value="Ya">
                                <label class="form-check-label" for="kembaliCheckMasuk">
                                    <strong>Barang Akan Kembali</strong>
                                </label>
                                <div class="form-text">Barang akan dikembalikan setelah digunakan</div>
                            </div>
                        </div>

                        <div class="col-md-6 d-none" id="jumlahWrapperMasuk">
                            <label class="form-label">Jumlah Masuk Pertama</label>
                            <input type="number" name="jumlah_masuk" class="form-control jumlah-masuk" id="jumlahInputMasuk" min="1" value="1">
                            <div class="form-text">Jumlah yang masuk pertama kali</div>
                        </div>

                        <div class="col-md-6 d-none" id="sisaWrapperMasuk">
                            <label class="form-label">Sisa yang Belum Masuk</label>
                            <input type="text" class="form-control" id="sisaInputMasuk" readonly>
                            <div class="form-text">Akan otomatis terhitung</div>
                        </div>

                        <div class="col-md-6 d-none" id="estimasiWrapperMasuk">
                            <label class="form-label">Estimasi Tanggal Kembali</label>
                            <input type="date" name="estimasi_kembali" class="form-control" id="estimasiDateMasuk">
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

<!-- ============================================ -->
<!-- MODAL REGISTRASI BARANG KELUAR -->
<!-- ============================================ -->
<div class="modal fade" id="keluarModal" tabindex="-1" aria-labelledby="keluarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="/registrasi/store">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="keluarModalLabel">Registrasi Barang Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">No Agenda</label>
                            <input type="text" name="no_agenda" value="<?= esc($noAgendaKeluar ?? 'K-0000') ?>" class="form-control" readonly>
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

                        <input type="hidden" name="tanggal" value="<?= date('Y-m-d H:i:s') ?>">

                        <div class="col-md-12">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Total</label>
                            <input type="number" name="jumlah" class="form-control jumlah-total" id="totalJumlahKeluar" value="1" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Asal (Perusahaan)</label>
                            <input type="text" name="asal" class="form-control" value="PT PINDAD" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" class="form-control" required>
                        </div>

                        <div class="col-12 mt-3">
                            <h6 class="border-bottom pb-2">Mode Pengiriman</h6>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="partialCheckKeluar" name="is_partial">
                                <label class="form-check-label" for="partialCheckKeluar">
                                    <strong>Partial (Bertahap)</strong>
                                </label>
                                <div class="form-text">Barang keluar sebagian demi sebagian</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="kembaliCheckKeluar" name="akan_kembali" value="Ya">
                                <label class="form-check-label" for="kembaliCheckKeluar">
                                    <strong>Barang Akan Kembali</strong>
                                </label>
                                <div class="form-text">Barang akan dikembalikan setelah digunakan</div>
                            </div>
                        </div>

                        <div class="col-md-6 d-none" id="jumlahWrapperKeluar">
                            <label class="form-label">Jumlah Keluar Pertama</label>
                            <input type="number" name="jumlah_masuk" class="form-control jumlah-masuk" id="jumlahInputKeluar" min="1" value="1">
                            <div class="form-text">Jumlah yang keluar pertama kali</div>
                        </div>

                        <div class="col-md-6 d-none" id="sisaWrapperKeluar">
                            <label class="form-label">Sisa yang Belum Keluar</label>
                            <input type="text" class="form-control" id="sisaInputKeluar" readonly>
                            <div class="form-text">Akan otomatis terhitung</div>
                        </div>

                        <div class="col-md-6 d-none" id="estimasiWrapperKeluar">
                            <label class="form-label">Estimasi Tanggal Kembali</label>
                            <input type="date" name="estimasi_kembali" class="form-control" id="estimasiDateKeluar">
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

<!-- ============================================ -->
<!-- SCRIPT PERBAIKAN MODAL DAN FUNGSI -->
<!-- ============================================ -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// ============================================
// PERBAIKAN MODAL - LENGKAP
// ============================================

// Fungsi untuk menutup semua modal secara paksa
function forceCloseAllModals() {
    document.querySelectorAll('.modal.show, .modal[style*="display: block"]').forEach(modal => {
        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
        modal.removeAttribute('style');
    });
    
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.remove();
    });
    
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
}

// Inisialisasi ulang semua modal
function initModals() {
    document.querySelectorAll('.modal').forEach(modalElement => {
        try {
            const existingModal = bootstrap.Modal.getInstance(modalElement);
            if (existingModal) {
                existingModal.dispose();
            }
        } catch (e) {
            console.log('Error disposing modal:', e);
        }
        
        try {
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: true,
                focus: true
            });
            
            modalElement.addEventListener('hide.bs.modal', function() {
                setTimeout(() => {
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        backdrop.remove();
                    });
                }, 100);
            });
            
            modalElement.addEventListener('hidden.bs.modal', function() {
                forceCloseAllModals();
                
                const form = this.querySelector('form');
                if (form) {
                    form.reset();
                }
                
                this.querySelectorAll('[id^="jumlahWrapper"], [id^="sisaWrapper"], [id^="estimasiWrapper"]').forEach(wrapper => {
                    wrapper.classList.add('d-none');
                });
            });
            
            modalElement.addEventListener('show.bs.modal', function() {
                forceCloseAllModals();
            });
            
        } catch (e) {
            console.log('Error creating modal:', e);
        }
    });
}

// Reinisialisasi semua tombol yang membuka modal
function reinitModalTriggers() {
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        const newButton = button.cloneNode(true);
        if (button.parentNode) {
            button.parentNode.replaceChild(newButton, button);
        }
        
        newButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const targetId = this.getAttribute('data-bs-target');
            if (!targetId) return;
            
            const modalElement = document.querySelector(targetId);
            if (!modalElement) return;
            
            forceCloseAllModals();
            
            setTimeout(() => {
                try {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.show();
                    } else {
                        const newModal = new bootstrap.Modal(modalElement, {
                            backdrop: 'static',
                            keyboard: true
                        });
                        newModal.show();
                    }
                } catch (error) {
                    console.error('Error showing modal:', error);
                    modalElement.classList.add('show');
                    modalElement.style.display = 'block';
                    modalElement.setAttribute('aria-hidden', 'false');
                    modalElement.setAttribute('aria-modal', 'true');
                    
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                    document.body.classList.add('modal-open');
                }
            }, 50);
        });
    });
}

// Perbaikan khusus untuk tombol close
function fixCloseButtons() {
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        const newButton = button.cloneNode(true);
        if (button.parentNode) {
            button.parentNode.replaceChild(newButton, button);
        }
        
        newButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const modal = this.closest('.modal');
            if (!modal) return;
            
            try {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    forceCloseAllModals();
                }
            } catch (error) {
                forceCloseAllModals();
            }
        });
    });
}

// Inisialisasi Select2
function initSelect2() {
    $('.select2').select2({
        width: '100%',
        placeholder: '-- Pilih --',
        allowClear: true
    });
}

function initSelect2InModal(modal) {
    $(modal).find('select.select2').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });

    $(modal).find('.select2').select2({
        width: '100%',
        placeholder: '-- Pilih --',
        allowClear: true,
        dropdownParent: $(modal)
    });
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    initModals();
    reinitModalTriggers();
    fixCloseButtons();
    initSelect2();
    
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            initSelect2InModal(this);
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                e.preventDefault();
                try {
                    const modalInstance = bootstrap.Modal.getInstance(openModal);
                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        forceCloseAllModals();
                    }
                } catch (error) {
                    forceCloseAllModals();
                }
            }
        }
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            const modal = e.target;
            try {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    forceCloseAllModals();
                }
            } catch (error) {
                forceCloseAllModals();
            }
        }
    });
    
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

    setupModalRegistrasi('registrasiModal', 'Masuk');
    setupModalRegistrasi('keluarModal', 'Keluar');
});

// Fungsi setup modal registrasi
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

    if (estimasiDate && !estimasiDate.value) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        estimasiDate.value = tomorrow.toISOString().split('T')[0];
    }
}

// Event untuk input jumlah total
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

// ============================================
// FUNGSI QR CODE
// ============================================

let currentQRData = '';

function showQRCode(data) {
    currentQRData = data;
    const qrContainer = document.getElementById('qrCodeContainer');
    const qrText = document.getElementById('qrCodeText');
    
    qrContainer.innerHTML = '';
    qrText.textContent = `Nomor Registrasi: ${data}`;
    
    QRCode.toCanvas(document.createElement('canvas'), data, {
        width: 200,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#ffffff'
        }
    }, function(err, canvas) {
        if (err) {
            console.error(err);
            qrContainer.innerHTML = '<p class="text-danger">Gagal generate QR Code</p>';
            return;
        }
        canvas.style.width = '200px';
        canvas.style.height = '200px';
        qrContainer.appendChild(canvas);
    });
}

function downloadQRCode() {
    if (!currentQRData) return;
    
    const canvas = document.querySelector('#qrCodeContainer canvas');
    if (!canvas) return;
    
    const link = document.createElement('a');
    link.download = `QR-${currentQRData}.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();
}
// ============================================
// FUNGSI PERPANJANG LAPTOP (DIPERBAIKI)
// ============================================

// ============================================
// FUNGSI PERPANJANG LAPTOP
// ============================================

function perpanjangLaptop(id) {
    // Cari data laptop berdasarkan ID
    <?php foreach ($laptops as $laptop): ?>
    if (id === <?= $laptop['id'] ?>) {
        // Isi data ke modal
        document.getElementById('perpanjang_laptop_id').value = <?= $laptop['id'] ?>;
        document.getElementById('perpanjang_nama').value = '<?= esc($laptop['nama_pengguna'] ?? '') ?>';
        document.getElementById('perpanjang_seri').value = '<?= esc($laptop['nomor_seri'] ?? '') ?>';
        document.getElementById('perpanjang_no_registrasi').value = '<?= esc($laptop['no_registrasi'] ?? '') ?>';
        
        // Registrasi ke (sudah dihitung di controller)
        document.getElementById('perpanjang_registrasi_ke').value = '<?= ($laptop['registrasi_selanjutnya'] ?? 1) ?>';
        document.getElementById('info_registrasi_ke').textContent = '<?= ($laptop['registrasi_selanjutnya'] ?? 1) ?>';
        
        // Tanggal berlaku saat ini
        <?php 
        $tanggalLama = date('d-m-Y', strtotime($laptop['berlaku_sampai']));
        $minTanggal = date('Y-m-d', strtotime($laptop['berlaku_sampai'] . ' +1 day'));
        ?>
        document.getElementById('perpanjang_berlaku_sampai_lama').value = '<?= $tanggalLama ?>';
        
        // Set minimal tanggal baru (H+1 dari tanggal lama)
        document.getElementById('perpanjang_berlaku_sampai_baru').min = '<?= $minTanggal ?>';
        document.getElementById('perpanjang_berlaku_sampai_baru').value = '';
        
        // Kosongkan keterangan
        document.getElementById('perpanjang_keterangan').value = '';
        
        // Buka modal
        const modal = new bootstrap.Modal(document.getElementById('perpanjangLaptopModal'));
        modal.show();
        return;
    }
    <?php endforeach; ?>
    
    // Jika tidak ditemukan
    alert('Data laptop tidak ditemukan');
}
// ============================================
// FUNGSI PRINT LAPTOP
// ============================================

function printLaptop(id) {
    <?php foreach ($laptops as $laptop): ?>
    if (id === <?= $laptop['id'] ?>) {
        var data = <?= json_encode($laptop) ?>;
        var w = window.open('', '_blank');

        w.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8">
        <title>Surat Izin Membawa Laptop</title>
        <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        html, body{
            margin:0;
            padding:0;
            font-family: Arial, sans-serif;
            font-size:12px;
        }
        .page{
            display:flex;
            width:100%;
        }
        .left-side{
            flex:0 0 55%;
            padding-right:15px;
            box-sizing:border-box;
        }
        .right-side{
            flex:0 0 45%;
            padding:8mm 6mm 6mm 6mm;
            box-sizing:border-box;
        }
        .top-header{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom:6px;
        }
        .logo-left{
            width:110px;
            height:auto;
        }
        .logo-right{
            width:95px;
            height:auto;
            margin-bottom:4px;
        }
        .noreg-box{
            border:1px solid #000;
            padding:4px 8px;
            width:160px;
            font-size:11px;
            margin-top:3px;
        }
        .center-line{
            text-align:center;
            margin-top:3px;
        }
        .pt{
            font-weight:bold;
            font-size:14px;
        }
        .title-main{
            font-weight:bold;
            font-size:15px;
        }
        .subtitle{
            font-size:11px;
        }
        .dasar-row{
            display:flex;
            justify-content:center;
            gap:30px;
            margin-top:8px;
            font-size:11px;
        }
        .form-row{
            display:flex;
            align-items:center;
            margin-top:5px;
            font-size:11px;
        }
        .label{
            width:140px;
        }
        .colon{
            width:15px;
            text-align:center;
        }
        .line-fill{
            flex:1;
            border-bottom:1px solid #000;
            padding-left:5px;
            min-height:16px;
        }
        table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }
        table, th, td{
            border:1px solid #000;
        }
        th{
            font-size:9px;
            padding:2px;
            text-align:center;
        }
        td{
            font-size:8px;
            padding:1px;
            text-align:center;
            height:16px;
        }
        .signature-section{
            display:flex;
            justify-content:space-between;
            margin-top:25px;
            font-size:11px;
        }
        .sign-box{
            width:45%;
            text-align:center;
        }
        .sign-line{
            width:140px;
            margin:35px auto 0 auto;
            border-top:1px solid #000;
        }
        @media print{
            .print-buttons{
                display:none;
            }
        }
        .print-buttons{
            position:fixed;
            bottom:15px;
            right:15px;
        }
        .print-buttons button{
            padding:6px 12px;
            font-size:12px;
            cursor:pointer;
        }
        </style>
        </head>
        <body>
        <div class="page">
            <div class="left-side">
                <div class="top-header">
                    <div class="left-logo">
                        <img src="/assets/img/logodi.png" class="logo-left">
                    </div>
                    <div class="right-area">
                        <img src="/assets/img/logop.png" class="logo-right">
                        <div class="noreg-box">
                            No Reg : __________
                        </div>
                    </div>
                </div>
                <div class="center-line pt">
                    PT PINDAD
                </div>
                <div class="center-line">
                    DIVISI PENGAMANAN
                </div>
                <div class="center-line title-main">
                    SURAT IZIN MEMBAWA LAPTOP
                </div>
                <div class="center-line subtitle">
                    PERMISSION LETTER TO BRING LAPTOP
                </div>
                <div class="dasar-row">
                    <div><strong>Dasar :</strong> ____________________________</div>
                    <div><strong>Tgl :</strong> ____________________________</div>
                </div>
                <div class="form-row">
                    <span class="label">Nama</span>
                    <span class="colon">:</span>
                    <span class="line-fill">${data.nama_pengguna || ''}</span>
                </div>
                <div class="form-row">
                    <span class="label">Nomor ID</span>
                    <span class="colon">:</span>
                    <span class="line-fill">${data.nomor_id_card || ''}</span>
                </div>
                <div class="form-row">
                    <span class="label">Instansi</span>
                    <span class="colon">:</span>
                    <span class="line-fill">${data.instansi_divisi || ''}</span>
                </div>
                <div class="form-row">
                    <span class="label">Merek</span>
                    <span class="colon">:</span>
                    <span class="line-fill">${data.merek || ''}</span>
                </div>
                <div class="form-row">
                    <span class="label">Tipe</span>
                    <span class="colon">:</span>
                    <span class="line-fill">${data.tipe_laptop || ''}</span>
                </div>
                <div class="form-row">
                    <span class="label">No Seri</span>
                    <span class="colon">:</span>
                    <span class="line-fill">${data.nomor_seri || ''}</span>
                </div>
                <div class="form-row">
                    <span class="label">Berlaku Sampai</span>
                    <span class="colon">:</span>
                    <span class="line-fill">${data.berlaku_sampai || ''}</span>
                </div>
                <div style="margin-top:20px;">
                    Demikian surat izin ini dibuat untuk dipergunakan sebagaimana mestinya.
                </div>
                <div class="signature-section">
                    <div class="sign-box">
                        Mengetahui<br>
                        <strong>Petugas Div PAM</strong><br>
                        Seccurity Officer
                        <div class="sign-line"></div>
                    </div>
                    <div class="sign-box">
                        Bandung,<br>
                        <strong>Yang Membawa</strong><br>
                        The Beorer
                        <div class="sign-line"></div>
                    </div>
                </div>
            </div>
            <div class="right-side">
                <table>
                    <tr>
                        <th colspan="2">MASUK</th>
                        <th colspan="2">KELUAR</th>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <th>Paraf</th>
                        <th>Tanggal</th>
                        <th>Paraf</th>
                    </tr>
                    ${'<tr><td></td><td></td><td></td><td></td></tr>'.repeat(28)}
                </table>
            </div>
        </div>
        <div class="print-buttons">
            <button onclick="window.print()">Print</button>
            <button onclick="window.close()">Kembali</button>
        </div>
        </body>
        </html>
        `);

        w.document.close();
        return;
    }
    <?php endforeach; ?>
    alert("Data tidak ditemukan");
}

// ============================================
// PERBAIKAN UNTUK SEARCH LAPTOP (AJAX)
// ============================================

if (document.getElementById('searchLaptopForm')) {
    const searchForm = document.getElementById('searchLaptopForm');
    const keywordInput = document.getElementById('searchKeyword');
    const statusSelect = document.getElementById('searchStatus');
    const tableContainer = document.getElementById('laptopTableContainer');
    const btnSearch = document.getElementById('btnSearchLaptop');
    
    if (window.location.hash === '#laptop-tab') {
        document.getElementById('laptop-tab').click();
    }
    
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            history.pushState(null, null, e.target.hash);
        });
    });
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const keyword = keywordInput.value;
            const status = statusSelect.value;
            
            btnSearch.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btnSearch.disabled = true;
            
            fetch(`/barang/searchLaptop?keyword=${encodeURIComponent(keyword)}&status=${encodeURIComponent(status)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                tableContainer.innerHTML = html;
                
                setTimeout(() => {
                    initModals();
                    reinitModalTriggers();
                    fixCloseButtons();
                    initSelect2();
                }, 100);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data: ' + error.message);
            })
            .finally(() => {
                btnSearch.innerHTML = '<i class="bi bi-search"></i>';
                btnSearch.disabled = false;
            });
        });
    }
    
    let timeout = null;
    if (keywordInput) {
        keywordInput.addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                searchForm.dispatchEvent(new Event('submit'));
            }, 500);
        });
    }
    
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            searchForm.dispatchEvent(new Event('submit'));
        });
    }
}

window.addEventListener('error', function() {
    forceCloseAllModals();
});

setInterval(function() {
    const openModals = document.querySelectorAll('.modal.show');
    const backdrops = document.querySelectorAll('.modal-backdrop');
    
    if (openModals.length === 0 && backdrops.length > 0) {
        backdrops.forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
    }
    
    if (openModals.length > 0 && backdrops.length === 0) {
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }
}, 500);
</script>

<?= $this->endSection() ?>