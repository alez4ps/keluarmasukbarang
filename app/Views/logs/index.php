<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4 class="mb-3">Riwayat Aktivitas</h4>

<!-- Nav tabs untuk memilih jenis log -->
<ul class="nav nav-tabs mb-4" id="logTypeTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= ($logType ?? 'barang') == 'barang' ? 'active' : '' ?>" 
                id="barang-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#barang" 
                type="button" 
                role="tab"
                onclick="document.getElementById('logTypeInput').value='barang'">
            <i class="bi bi-box-seam"></i> Riwayat Barang
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= ($logType ?? '') == 'laptop' ? 'active' : '' ?>" 
                id="laptop-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#laptop" 
                type="button" 
                role="tab"
                onclick="document.getElementById('logTypeInput').value='laptop'">
            <i class="bi bi-laptop"></i> Riwayat Laptop
        </button>
    </li>
</ul>

<div class="tab-content" id="logTypeTabContent">
    <!-- ==================== TAB RIWAYAT BARANG ==================== -->
    <div class="tab-pane fade <?= ($logType ?? 'barang') == 'barang' ? 'show active' : '' ?>" 
         id="barang" 
         role="tabpanel">
         
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form action="/logs" method="get" class="row g-3">
                    <input type="hidden" name="tab" id="activeTabInput" value="<?= $activeTab ?? 'semua' ?>">
                    <input type="hidden" name="log_type" id="logTypeInput" value="<?= $logType ?? 'barang' ?>">

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

                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                            <a href="/logs?log_type=barang" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="/logs/export?log_type=barang&<?= http_build_query($_GET) ?>" 
                           class="btn btn-success w-100">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($totalRowsBarang) && $totalRowsBarang > 0): ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
            <div>
                <i class="bi bi-info-circle"></i>
                Menampilkan <strong><?= number_format($totalRowsBarang) ?></strong> aktivitas barang
                <?php if ($startDate || $endDate): ?>
                    dari <?= $startDate ? date('d/m/Y', strtotime($startDate)) : 'awal' ?>
                    sampai <?= $endDate ? date('d/m/Y', strtotime($endDate)) : 'sekarang' ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tab navigasi untuk barang -->
        <ul class="nav nav-tabs mb-3" id="logTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($activeTab ?? 'semua') == 'semua' ? 'active' : '' ?>" 
                        id="semua-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#semua" 
                        type="button" 
                        role="tab"
                        onclick="document.getElementById('activeTabInput').value='semua'">
                    <i class="bi bi-list-ul"></i> Semua Aktivitas 
                    <span class="badge bg-secondary"><?= number_format($totalAllBarang ?? 0) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($activeTab ?? '') == 'masuk' ? 'active' : '' ?>" 
                        id="masuk-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#masuk" 
                        type="button" 
                        role="tab"
                        onclick="document.getElementById('activeTabInput').value='masuk'">
                    <i class="bi bi-box-arrow-in-down text-success"></i> Barang Masuk 
                    <span class="badge bg-success"><?= number_format($totalMasukBarang ?? 0) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($activeTab ?? '') == 'keluar' ? 'active' : '' ?>" 
                        id="keluar-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#keluar" 
                        type="button" 
                        role="tab"
                        onclick="document.getElementById('activeTabInput').value='keluar'">
                    <i class="bi bi-box-arrow-up text-warning"></i> Barang Keluar 
                    <span class="badge bg-warning text-dark"><?= number_format($totalKeluarBarang ?? 0) ?></span>
                </button>
            </li>
        </ul>

        <!-- Konten tab barang -->
        <div class="tab-content" id="logTabContent">
            <div class="tab-pane fade <?= ($activeTab ?? 'semua') == 'semua' ? 'show active' : '' ?>" 
                 id="semua" 
                 role="tabpanel">
                <?= view('logs/table_barang', ['logs' => $logsSemuaBarang ?? [], 'type' => 'semua']) ?>
            </div>
            
            <div class="tab-pane fade <?= ($activeTab ?? '') == 'masuk' ? 'show active' : '' ?>" 
                 id="masuk" 
                 role="tabpanel">
                <?= view('logs/table_barang', ['logs' => $logsMasukBarang ?? [], 'type' => 'masuk']) ?>
            </div>
            
            <div class="tab-pane fade <?= ($activeTab ?? '') == 'keluar' ? 'show active' : '' ?>" 
                 id="keluar" 
                 role="tabpanel">
                <?= view('logs/table_barang', ['logs' => $logsKeluarBarang ?? [], 'type' => 'keluar']) ?>
            </div>
        </div>
    </div>

    <!-- ==================== TAB RIWAYAT LAPTOP ==================== -->
    <div class="tab-pane fade <?= ($logType ?? '') == 'laptop' ? 'show active' : '' ?>" 
         id="laptop" 
         role="tabpanel">
         
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form action="/logs" method="get" class="row g-3">
                    <input type="hidden" name="log_type" value="laptop">
                    <input type="hidden" name="laptop_tab" id="laptopActiveTabInput" value="<?= $laptopActiveTab ?? 'semua' ?>">

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

                    <div class="col-md-3">
                        <label class="form-label">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control"
                                   placeholder="Cari nama / merek / seri / no registrasi..."
                                   value="<?= esc($keyword ?? '') ?>">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                            <a href="/logs?log_type=laptop" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="/logs/export?log_type=laptop&<?= http_build_query($_GET) ?>" 
                           class="btn btn-success w-100">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($totalRowsLaptop) && $totalRowsLaptop > 0): ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
            <div>
                <i class="bi bi-info-circle"></i>
                Menampilkan <strong><?= number_format($totalRowsLaptop) ?></strong> aktivitas laptop
                <?php if ($startDate || $endDate): ?>
                    dari <?= $startDate ? date('d/m/Y', strtotime($startDate)) : 'awal' ?>
                    sampai <?= $endDate ? date('d/m/Y', strtotime($endDate)) : 'sekarang' ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tab navigasi untuk laptop -->
        <ul class="nav nav-tabs mb-3" id="laptopLogTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($laptopActiveTab ?? 'semua') == 'semua' ? 'active' : '' ?>" 
                        id="laptop-semua-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#laptop-semua" 
                        type="button" 
                        role="tab"
                        onclick="document.getElementById('laptopActiveTabInput').value='semua'">
                    <i class="bi bi-list-ul"></i> Semua Aktivitas 
                    <span class="badge bg-secondary"><?= number_format($totalAllLaptop ?? 0) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($laptopActiveTab ?? '') == 'registrasi' ? 'active' : '' ?>" 
                        id="laptop-registrasi-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#laptop-registrasi" 
                        type="button" 
                        role="tab"
                        onclick="document.getElementById('laptopActiveTabInput').value='registrasi'">
                    <i class="bi bi-plus-circle text-success"></i> Registrasi 
                    <span class="badge bg-success"><?= number_format($totalRegistrasiLaptop ?? 0) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($laptopActiveTab ?? '') == 'perpanjangan' ? 'active' : '' ?>" 
                        id="laptop-perpanjangan-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#laptop-perpanjangan" 
                        type="button" 
                        role="tab"
                        onclick="document.getElementById('laptopActiveTabInput').value='perpanjangan'">
                    <i class="bi bi-arrow-repeat text-warning"></i> Perpanjangan 
                    <span class="badge bg-warning text-dark"><?= number_format($totalPerpanjanganLaptop ?? 0) ?></span>
                </button>
            </li>
        </ul>

        <!-- Konten tab laptop -->
        <div class="tab-content" id="laptopLogTabContent">
            <div class="tab-pane fade <?= ($laptopActiveTab ?? 'semua') == 'semua' ? 'show active' : '' ?>" 
                 id="laptop-semua" 
                 role="tabpanel">
                <?= view('logs/table_laptop', ['logs' => $logsSemuaLaptop ?? [], 'type' => 'semua']) ?>
            </div>
            
            <div class="tab-pane fade <?= ($laptopActiveTab ?? '') == 'registrasi' ? 'show active' : '' ?>" 
                 id="laptop-registrasi" 
                 role="tabpanel">
                <?= view('logs/table_laptop', ['logs' => $logsRegistrasiLaptop ?? [], 'type' => 'registrasi']) ?>
            </div>
            
            <div class="tab-pane fade <?= ($laptopActiveTab ?? '') == 'perpanjangan' ? 'show active' : '' ?>" 
                 id="laptop-perpanjangan" 
                 role="tabpanel">
                <?= view('logs/table_laptop', ['logs' => $logsPerpanjanganLaptop ?? [], 'type' => 'perpanjangan']) ?>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk mengupdate nilai tab saat berpindah
document.addEventListener('DOMContentLoaded', function() {
    // Set nilai tab aktif saat form disubmit
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const logType = document.getElementById('logTypeInput')?.value;
            if (logType === 'laptop') {
                const activeTab = document.querySelector('#laptopLogTab .nav-link.active');
                if (activeTab) {
                    const onclickAttr = activeTab.getAttribute('onclick');
                    const match = onclickAttr ? onclickAttr.match(/value='([^']+)'/) : null;
                    document.getElementById('laptopActiveTabInput').value = match ? match[1] : 'semua';
                }
            } else {
                const activeTab = document.querySelector('#logTab .nav-link.active');
                if (activeTab) {
                    const onclickAttr = activeTab.getAttribute('onclick');
                    const match = onclickAttr ? onclickAttr.match(/value='([^']+)'/) : null;
                    document.getElementById('activeTabInput').value = match ? match[1] : 'semua';
                }
            }
        });
    });

    // Handle tab switching untuk menyimpan state
    const logTypeTabs = document.querySelectorAll('#logTypeTab .nav-link');
    logTypeTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const logType = this.id === 'barang-tab' ? 'barang' : 'laptop';
            document.getElementById('logTypeInput').value = logType;
        });
    });

    // Load konten tab yang sesuai berdasarkan URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const logType = urlParams.get('log_type');
    if (logType === 'laptop') {
        document.getElementById('laptop-tab').click();
    }
});
</script>

<?= $this->endSection() ?>