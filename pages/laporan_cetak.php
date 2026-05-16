<?php
include '../config/auth.php';
include '../config/database.php';

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

$query = "SELECT p.*, b.judul, a.nama, a.nomor_anggota
          FROM peminjaman p
          JOIN buku b ON p.id_buku = b.id_buku
          JOIN anggota a ON p.id_anggota = a.id_anggota
          WHERE MONTH(p.tanggal_pinjam) = $bulan AND YEAR(p.tanggal_pinjam) = $tahun
          ORDER BY p.tanggal_pinjam DESC";

$result = mysqli_query($conn, $query);
$total_data = mysqli_num_rows($result);

$nama_bulan = [
    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni',
    7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
];

$periode = $nama_bulan[$bulan] . ' ' . $tahun;
$tgl_cetak = date('d/m/Y');

$total_dipinjam = 0;
$total_dikembalikan = 0;
if ($total_data > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['status'] == 'dipinjam') $total_dipinjam++;
        else $total_dikembalikan++;
    }
    mysqli_data_seek($result, 0);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - <?= $periode ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px;
            color: #1c1e21;
        }

        .print-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            padding: 40px 48px;
        }

        .print-header {
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0a1317;
        }

        .print-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #0a1317;
            letter-spacing: -0.22px;
            margin-bottom: 4px;
        }

        .print-header p {
            font-size: 13px;
            color: #5d6c7b;
            margin: 0;
        }

        .print-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13px;
            color: #444950;
        }

        .print-info strong {
            color: #0a1317;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 24px;
        }

        thead th {
            background: #0a1317;
            color: #fff;
            font-weight: 600;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        tbody td {
            padding: 8px 8px;
            border-bottom: 1px solid #dee3e9;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        tbody tr:hover {
            background: #f1f4f7;
        }

        .status-dipinjam {
            display: inline-block;
            padding: 2px 8px;
            background: #fff3cd;
            color: #856404;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-dikembalikan {
            display: inline-block;
            padding: 2px 8px;
            background: #d4edda;
            color: #155724;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .print-footer {
            display: flex;
            justify-content: space-between;
            align-items: end;
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #dee3e9;
            font-size: 13px;
            color: #444950;
        }

        .print-footer .total-info {
            font-weight: 600;
            color: #0a1317;
        }

        .print-footer .ttd {
            text-align: center;
        }

        .print-footer .ttd .line {
            margin-top: 48px;
            width: 200px;
            border-top: 1px solid #0a1317;
            padding-top: 6px;
            font-size: 12px;
        }

        .print-actions {
            text-align: center;
            margin-top: 32px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .btn-print {
            background: #0a1317;
            color: #fff;
            border: none;
            padding: 10px 32px;
            border-radius: 100px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-print:hover {
            background: #444950;
        }

        .btn-back {
            background: transparent;
            color: #0a1317;
            border: 2px solid rgba(10, 19, 23, 0.12);
            padding: 8px 28px;
            border-radius: 100px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-back:hover {
            border-color: #0a1317;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .print-wrapper {
                max-width: 100%;
                box-shadow: none;
                border-radius: 0;
                padding: 32px 40px;
            }

            .print-actions {
                display: none;
            }

            tbody tr:nth-child(even) {
                background: #f8f9fa;
            }

            thead th {
                background: #0a1317 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .status-dipinjam, .status-dikembalikan {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-footer {
                page-break-inside: avoid;
            }
        }

        @page {
            margin: 20mm 15mm;
        }
    </style>
</head>

<body>
    <div class="print-wrapper">
        <div class="print-header">
            <h1>Laporan Peminjaman Perpustakaan</h1>
            <p>Periode: <?= $periode ?></p>
        </div>

        <div class="print-info">
            <div><strong>Tanggal Cetak:</strong> <?= $tgl_cetak ?></div>
        </div>

        <?php if ($total_data > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;">No</th>
                        <th style="width:50px;">ID</th>
                        <th>Judul Buku</th>
                        <th>Anggota</th>
                        <th style="width:90px;">Tgl Pinjam</th>
                        <th style="width:90px;">Jatuh Tempo</th>
                        <th style="width:90px;">Tgl Kembali</th>
                        <th style="width:100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td style="text-align:center;"><?= $no++ ?></td>
                            <td><?= $row['id_peminjaman'] ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($row['judul']) ?></td>
                            <td>
                                <?= htmlspecialchars($row['nama']) ?>
                                <br><small style="color:#6b7280;"><?= htmlspecialchars($row['nomor_anggota']) ?></small>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?></td>
                            <td>
                                <?= !empty($row['tanggal_kembali']) ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-' ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'dipinjam'): ?>
                                    <span class="status-dipinjam">Dipinjam</span>
                                <?php else: ?>
                                    <span class="status-dikembalikan">Dikembalikan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align:center;padding:60px 0;color:#8595a4;font-size:14px;">
                Tidak ada data peminjaman untuk periode <?= $periode ?>.
            </div>
        <?php endif; ?>

        <div class="print-footer">
            <div>
                <span class="total-info">Total: <?= $total_data ?> data</span>
                <?php if ($total_data > 0): ?>
                    <br><span style="font-size:12px;color:#6b7280;">
                        Dipinjam: <?= $total_dipinjam ?> &middot; Dikembalikan: <?= $total_dikembalikan ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="ttd">
                <div class="line">Kepala Perpustakaan</div>
            </div>
        </div>
    </div>

    <div class="print-actions">
        <a href="../index.php?page=laporan&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-back">
            &larr; Kembali
        </a>
        <button class="btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak
        </button>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };

        window.onafterprint = function() {
            window.location.href = '../index.php?page=laporan&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>';
        };
    </script>
</body>

</html>
