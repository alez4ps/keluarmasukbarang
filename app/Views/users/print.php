<!DOCTYPE html>
<html>
<head>
    
  <link href="<?= base_url('assets/img/logo2.png') ?>" rel="icon">
    <title>Laporan Data User</title>
    <style>
        body { font-family: Arial; font-size: 12px }
        h2 { text-align: center; }
        table { width:100%; border-collapse: collapse }
        th, td { border:1px solid #000; padding:6px; text-align:center }
        .header { text-align:center; margin-bottom:20px }
        .print-btn { margin-bottom:20px; }
        @media print { .print-btn { display:none } }
    </style>
</head>
<body>

<div class="header">
    <h2>LAPORAN DATA USER</h2>
    <p>PT PINDAD (Persero)</p>
    <hr>
</div>

<button onclick="window.print()" class="print-btn">🖨 Cetak</button>

<table>
    <tr>
        <th>No</th>
        <th>Nama Petugas</th>
        <th>Username</th>
        <th>Role</th>
        <th>Created At</th>
    </tr>
    <?php $no=1; foreach($users as $p): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= $p['nama_petugas'] ?></td>
        <td><?= $p['username'] ?></td>
        <td><?= $p['role'] ?></td>
        <td><?= $p['created_at'] ?></td>
    </tr>
    <?php endforeach ?>
</table>

</body>
</html>
