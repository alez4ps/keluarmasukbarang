<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>PT PINDAD</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?= base_url('assets/img/logo2.png') ?>" rel="icon">
  <link href="<?= base_url('assets/img/logo.jpg') ?>" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  <!-- Vendor CSS Files -->
  <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/boxicons/css/boxicons.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/quill/quill.snow.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/quill/quill.bubble.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/remixicon/remixicon.css') ?>" rel="stylesheet">
  <link href="<?= base_url('assets/vendor/simple-datatables/style.css') ?>" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>

<body>
<style>
.select2-container .select2-selection--single {
    height: 38px;
    padding: 5px 10px;
}

.select2-selection__arrow {
    height: 38px;
}

:root{
  --green:#1f7a4d;
  --green-soft:#e6f4ee;
  --green-dark:#155d3a;
}

/* HEADER */
.header {
  background-color: #ffffff !important;
  border-bottom: 1px solid #e5e7eb;
}

.logo span {
  color: var(--green) !important;
  font-weight: 700;
}

/* SIDEBAR */
.sidebar {
  background-color: #ffffff;
  border-right: 1px solid #e5e7eb;
}

.sidebar-nav .nav-link {
  color: #374151;
  border-radius: 8px;
  margin: 4px 10px;
}

.sidebar-nav .nav-link i {
  color: var(--green);
}

/* HOVER */
.sidebar-nav .nav-link:hover {
  background-color: var(--green-soft);
  color: var(--green);
}

/* ACTIVE MENU */
.sidebar-nav .nav-link.active {
  background-color: var(--green);
  color: #ffffff !important;
  box-shadow: 0 6px 18px rgba(31,122,77,.35);
}

.sidebar-nav .nav-link.active i {
  color: #ffffff;
}

/* MAIN CONTENT */
.main {
  background-color: #f9fafb;
}

/* CARD */
.card {
  border: none;
  border-radius: 14px;
  box-shadow: 0 10px 25px rgba(0,0,0,.06);
}

/* BUTTON */
.btn-primary {
  background-color: var(--green);
  border-color: var(--green);
}

.btn-primary:hover {
  background-color: var(--green-dark);
  border-color: var(--green-dark);
}

/* FOOTER */
.footer {
  background-color: transparent;
  border-top: 1px solid #e5e7eb;
  color: #6b7280; /* abu soft */
  font-size: 0.85rem;
}

.footer strong span {
  color: #1f7a4d; /* hijau corporate */
  font-weight: 600;
}

.footer a {
  color: #1f7a4d;
  text-decoration: none;
  font-weight: 500;
}

.footer a:hover {
  text-decoration: underline;
}

</style>


<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="<?= base_url('dashboard') ?>" class="logo d-flex align-items-center">
      <span class="d-none d-lg-block">PINDAD</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>

  <div class="ms-auto pe-4">
    <form action="<?= base_url('logout') ?>" onclick="return confirm('Yakin Ingin Logout?')" method="post">
      <?= csrf_field() ?>
      <button type="submit" class="btn btn-danger btn-sm">
        <i class="bi bi-box-arrow-right"></i> Logout
      </button>
    </form>
  </div>

</header>

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('dashboard') ?>">
        <i class="bi bi-house-door"></i> <span>Dashboard</span>
      </a>
    </li>

    <?php if(session()->get('role') == 'admin'): ?>
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('users') ?>">
        <i class="bi bi-people"></i> <span>Data Pengguna</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- <?php if(session()->get('role') == 'admin' || session()->get('role') == 'petugas' ): ?>
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#dn" data-bs-toggle="collapse" href="#">
        <i class="bi bi-menu-app"></i><span>Data Barang</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="dn" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?= base_url('kategori') ?>">
            <i class="bi bi-journal-text"></i><span>Data Kategori Barang</span>
          </a>
        </li>
      </ul>
      <ul id="dn" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?= base_url('barang') ?>">
            <i class="bi bi-journal-text"></i><span>Data Barang</span>
          </a>
        </li>
      </ul>
    </li>
    <?php endif; ?> -->

    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('registrasi') ?>">
        <i class="bi bi-envelope"></i> <span>Data Registrasi</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('logs') ?>">
        <i class="bi bi-clock-history"></i> <span>Riwayat Aktivitas</span>
      </a>
    </li>

    </ul>
</aside><!-- End Sidebar-->

<main id="main" class="main">

  <!-- Flash Message -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <?= $this->renderSection('content') ?>

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
  <div class="copyright">
    &copy; <?= date('Y') ?> <strong><span>Pindad</span></strong>. All Rights Reserved
  </div>
  <div class="credits">
    Designed by <a href="https://pindad.com/">PT Pindad</a>
  </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center">
  <i class="bi bi-arrow-up-short"></i>
</a>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Vendor JS Files -->
<script src="<?= base_url('assets/vendor/apexcharts/apexcharts.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/chart.js/chart.umd.js') ?>"></script>
<script src="<?= base_url('assets/vendor/echarts/echarts.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/quill/quill.js') ?>"></script>
<script src="<?= base_url('assets/vendor/simple-datatables/simple-datatables.js') ?>"></script>
<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/php-email-form/validate.js') ?>"></script>

<!-- Template Main JS File -->
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
