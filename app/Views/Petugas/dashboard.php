<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>

.fade-in {
    animation: fadeInSmooth 1s ease-out forwards;
    opacity: 0;
}
@keyframes fadeInSmooth {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease-in-out;
}
.card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

/* Icon Circle */
.card-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1.4rem;
}

/* Gradients */
.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff;}
.bg-success { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color:#fff;}
.bg-info    { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color:#fff;}
.bg-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color:#fff;}
.bg-secondary{background: linear-gradient(135deg,#fa709a 0%,#fee140 100%); color:#fff;}

.card-container .card { opacity: 0; animation: cardFadeIn 0.8s ease-out forwards; }
.card-container .card:nth-child(1){ animation-delay:0.1s; }
.card-container .card:nth-child(2){ animation-delay:0.2s; }
.card-container .card:nth-child(3){ animation-delay:0.3s; }
.card-container .card:nth-child(4){ animation-delay:0.4s; }
.card-container .card:nth-child(5){ animation-delay:0.5s; }

@keyframes cardFadeIn {
    0% { opacity:0; transform: translateY(20px) scale(0.95); }
    100% { opacity:1; transform: translateY(0) scale(1); }
}

/* Responsive */
@media (max-width:768px){
    .card-container .col-md-4 { margin-bottom:20px; }
}
</style>

<div class="fade-in">
    <h3 class="mb-4 text-primary" style="font-weight:300; letter-spacing:1px;">
        <i class="bi bi-speedometer2"></i> Dashboard Statistik
    </h3>

    <!-- Card Statistik -->
    <div class="row g-4 card-container">
        <!-- Registrasi Masuk -->
        <div class="col-md-4">
            <div class="card bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Registrasi Masuk <span class="text-white-50 small">| All Registration</span></h5>
                    <div class="d-flex align-items-center mt-3">
                        <div class="card-icon bg-white text-primary me-3">
                            <i class="bi bi-box-arrow-in-down"></i>
                        </div>
                        <div>
                            <h6 class="mb-0"><?= $masukHariIni ?></h6>
                            <span class="text-white-50 small">Total: <?= $totalMasuk ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registrasi Keluar -->
        <div class="col-md-4">
            <div class="card bg-success">
                <div class="card-body">
                    <h5 class="card-title">Registrasi Keluar <span class="text-white-50 small">| All Registration</span></h5>
                    <div class="d-flex align-items-center mt-3">
                        <div class="card-icon bg-white text-success me-3">
                            <i class="bi bi-box-arrow-up"></i>
                        </div>
                        <div>
                            <h6 class="mb-0"><?= $keluarHariIni ?></h6>
                            <span class="text-white-50 small">Total: <?= $totalKeluar ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User -->
        <div class="col-md-4">
            <div class="card bg-info">
                <div class="card-body">
                    <h5 class="card-title">Jumlah User <span class="text-white-50 small">| Active</span></h5>
                    <div class="d-flex align-items-center mt-3">
                        <div class="card-icon bg-white text-info me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h6 class="mb-0"><?= $totalUsers ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Masuk Hari Ini -->
        <div class="col-md-4">
            <div class="card bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Masuk Hari Ini <span class="text-white-50 small">| Today</span></h5>
                    <div class="d-flex align-items-center mt-3">
                        <div class="card-icon bg-white text-warning me-3">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <h6 class="mb-0"><?= $masukHariIni ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keluar Hari Ini -->
        <div class="col-md-4">
            <div class="card bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Keluar Hari Ini <span class="text-white-50 small">| Today</span></h5>
                    <div class="d-flex align-items-center mt-3">
                        <div class="card-icon bg-white text-secondary me-3">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div>
                            <h6 class="mb-0"><?= $keluarHariIni ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Aktivitas -->
<div class="col-md-4 mb-0">
    <div class="card bg-secondary">
        <div class="card-body">
            <h5 class="card-title">Total Aktivitas <span class="text-white-50 small">| All Logs</span></h5>
            <div class="d-flex align-items-center mt-3">
                <div class="card-icon bg-white text-secondary me-3">
                    <i class="bi bi-list-check"></i>
                </div>
                <div>
                    <h6 class="mb-0"><?= $totalAktivitas ?></h6>
                    <span class="text-white-50 small">Logs Recorded</span>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Counter Animation -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/counterup2@2.0.7/dist/index.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    $('.card h6').each(function(){
        new CounterUp(this, { duration: 1000, delay: 10 }).start();
    });
});
</script>

<?= $this->endSection() ?>
