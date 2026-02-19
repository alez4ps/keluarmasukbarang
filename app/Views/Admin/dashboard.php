<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
/* Fade In */
.fade-in {
    animation: fadeInSmooth .7s ease forwards;
    opacity: 0;
}
@keyframes fadeInSmooth {
    from { opacity:0; transform:translateY(20px); }
    to { opacity:1; transform:none; }
}

/* CARD BASE */
/* CARD BASE */
.card {
    background: #ffffff;
    border-radius: 12px;
    border-left: 5px solid #1c6cc8; /* HIJAU JELAS */
    box-shadow:
        0 4px 14px rgba(0,0,0,.06),
        0 0 12px rgba(28, 94, 200, 0.35); /* GREEN GLOW */
    transition: all .3s ease;
}

/* HOVER */
.card:hover {
    transform: translateY(-4px);
    box-shadow:
        0 10px 25px rgba(0,0,0,.08),
        0 0 20px rgba(28, 123, 200, 0.55);
}


/* NEON BORDER */
.card::before {
    content:'';
    position:absolute;
    inset:0;
    border-radius:12px;
    padding:1px;
    background: linear-gradient(
        135deg,
        rgba(78,115,223,.6),
        rgba(0, 86, 190, 0.6)
    );
    -webkit-mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity:.25;
    transition:.3s ease;
}

/* HOVER EFFECT */
.card:hover::before {
    opacity:.6;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow:
        0 0 15px rgba(78,115,223,.25),
        0 12px 30px rgba(0,0,0,.08);
}

/* TITLE */
.card-title {
    font-size: .85rem;
    font-weight: 600;
    color:#6b7280;
    text-transform: uppercase;
    letter-spacing:.05em;
}

/* NUMBER */
.card h6 {
    font-size: 1.9rem;
    font-weight: 700;
    color:#111827;
}

/* ICON */
.card-icon {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #f9fafb;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.25rem;
    box-shadow: 0 0 10px rgba(0,0,0,.05);
}

/* SOFT COLOR ICON */
.bg-primary .card-icon { color:#4e73df; }
.bg-success .card-icon { color:#1cc88a; }
.bg-info .card-icon    { color:#36b9cc; }
.bg-warning .card-icon { color:#f6c23e; }
.bg-secondary .card-icon{color:#858796; }

/* REMOVE BG COLOR */
.bg-primary,
.bg-success,
.bg-info,
.bg-warning,
.bg-secondary {
    background: #ffffff !important;
    color: inherit !important;
}

/* CARD ANIMATION */
.card-container .card {
    opacity:0;
    animation: cardFade .6s ease forwards;
}
.card-container .card:nth-child(1){animation-delay:.1s}
.card-container .card:nth-child(2){animation-delay:.2s}
.card-container .card:nth-child(3){animation-delay:.3s}
.card-container .card:nth-child(4){animation-delay:.4s}
.card-container .card:nth-child(5){animation-delay:.5s}
.card-container .card:nth-child(6){animation-delay:.6s}

@keyframes cardFade {
    from { opacity:0; transform:translateY(15px); }
    to { opacity:1; transform:none; }
}

/* MOBILE */
@media(max-width:768px){
    .card { margin-bottom:18px; }
}
</style>


<div class="fade-in">
<h3 class="mb-4" style="font-weight:300; letter-spacing:1px; color: #003366;">
    <i class="bi bi-speedometer2" style="color: #003366;"></i> Dashboard Statistik
</h3>

    <!-- Card Statistik -->
    <div class="row g-4 card-container">
        <!-- Registrasi Masuk -->
        <div class="col-md-4">
            <div class="card bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Registrasi Masuk <span class="text-green-50 small">| All Registration</span></h5>
                    <div class="d-flex align-items-center mt-3">
                        <div class="card-icon bg-white text-primary me-3">
                            <i class="bi bi-box-arrow-in-down"></i>
                        </div>
                        <div>
                            <h6 class="mb-0"><?= $masukHariIni ?></h6>
                            <span class="text-green-50 small">Total: <?= $totalMasuk ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registrasi Keluar -->
        <div class="col-md-4">
            <div class="card bg-success">
                <div class="card-body">
                    <h5 class="card-title">Registrasi Keluar <span class="text-green-50 small">| All Registration</span></h5>
                    <div class="d-flex align-items-center mt-3">
                        <div class="card-icon bg-white text-success me-3">
                            <i class="bi bi-box-arrow-up"></i>
                        </div>
                        <div>
                            <h6 class="mb-0"><?= $keluarHariIni ?></h6>
                            <span class="text-green-50 small">Total: <?= $totalKeluar ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Aktivitas -->
<div class="col-md-4 mb-0">
    <div class="card bg-secondary">
        <div class="card-body">
            <h5 class="card-title">Total Aktivitas <span class="text-green-50 small">| All Logs</span></h5>
            <div class="d-flex align-items-center mt-3">
                <div class="card-icon bg-white text-secondary me-3">
                    <i class="bi bi-list-check"></i>
                </div>
                <div>
                    <h6 class="mb-0"><?= $totalAktivitas ?></h6>
                    <span class="text-green-50 small">Logs Recorded</span>
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
