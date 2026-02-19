<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Nama Pengguna</th>
                <th>ID Card</th>
                <th>Instansi/Divisi</th>
                <th>Merek</th>
                <th>Tipe</th>
                <th>Nomor Seri</th>
                <th>Berlaku Sampai</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($laptops)): ?>
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> Tidak ada data laptop
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($laptops as $index => $laptop): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($laptop['nama_pengguna']) ?></td>
                    <td><?= esc($laptop['nomor_id_card']) ?></td>
                    <td><?= esc($laptop['instansi_divisi']) ?></td>
                    <td><?= esc($laptop['merek']) ?></td>
                    <td><?= esc($laptop['tipe_laptop']) ?></td>
                    <td><?= esc($laptop['nomor_seri']) ?></td>
                    <td>
                        <?= date('d/m/Y', strtotime($laptop['berlaku_sampai'])) ?>
                        <?php if (strtotime($laptop['berlaku_sampai']) < time()): ?>
                            <span class="badge bg-danger">Expired</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $badge = match($laptop['status']) {
                            'Masih Berlaku' => 'success',
                            'Tidak Berlaku' => 'secondary',
                            default => 'primary'
                        };
                        ?>
                        <span class="badge bg-<?= $badge ?>"><?= $laptop['status'] ?></span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailLaptopModal<?= $laptop['id'] ?>">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-secondary" 
                                    onclick="printLaptop(<?= $laptop['id'] ?>)">
                                <i class="bi bi-printer"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editLaptopModal<?= $laptop['id'] ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteLaptopModal<?= $laptop['id'] ?>">
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