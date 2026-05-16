<?php
include 'config/database.php';

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

$query = "SELECT p.*, b.judul, a.nama, a.nomor_anggota
          FROM peminjaman p
          JOIN buku b ON p.id_buku = b.id_buku
          JOIN anggota a ON p.id_anggota = a.id_anggota
          WHERE MONTH(p.tanggal_pinjam) = $bulan AND YEAR(p.tanggal_pinjam) = $tahun
          ORDER BY p.tanggal_pinjam DESC";

$result = mysqli_query($conn, $query);

$nama_bulan = [
    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni',
    7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
];
?>

<div class="page-header">
    <h2>Laporan</h2>
    <p>Riwayat peminjaman berdasarkan periode</p>
</div>

<!-- Filter -->
<div class="meta-card-compact mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <input type="hidden" name="page" value="laporan">
        <div class="col">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-select">
                <?php foreach ($nama_bulan as $key => $val): ?>
                    <option value="<?= $key ?>" <?= ($key == $bulan) ? 'selected' : '' ?>><?= $val ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-select">
                <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                    <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-meta-primary">
                <i class="bi bi-funnel me-1"></i> Tampilkan
            </button>
        </div>
    </form>
</div>

<!-- Results -->
    <div class="meta-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="meta-card-header mb-0"><?= $nama_bulan[$bulan] ?> <?= $tahun ?></div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge-meta" style="background:var(--surface-soft);color:var(--steel);"><?= mysqli_num_rows($result) ?> Data</span>
                <a href="pages/laporan_cetak.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-meta-ghost btn-meta-sm" target="_blank">
                    <i class="bi bi-printer"></i> Cetak
                </a>
            </div>
        </div>
    <div class="table-responsive">
        <table class="meta-table">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th style="width:60px">ID</th>
                    <th>Judul Buku</th>
                    <th>Anggota</th>
                    <th>Tgl Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td style="text-align:center;"><?= $no++ ?></td>
                            <td style="color:var(--steel);"><?= $row['id_peminjaman'] ?></td>
                            <td style="font-weight:700;"><?= htmlspecialchars($row['judul']) ?></td>
                            <td>
                                <?= htmlspecialchars($row['nama']) ?>
                                <br><span style="font-size:12px;color:var(--steel);"><?= htmlspecialchars($row['nomor_anggota']) ?></span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?></td>
                            <td>
                                <?= !empty($row['tanggal_kembali']) ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '<span style="color:var(--stone);">-</span>' ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'dipinjam'): ?>
                                    <span class="badge-meta badge-attention">Dipinjam</span>
                                <?php else: ?>
                                    <span class="badge-meta badge-success">Dikembalikan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px 16px;color:var(--stone);">
                            Tidak ada data peminjaman untuk periode yang dipilih.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
