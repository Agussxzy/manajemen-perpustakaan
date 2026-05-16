<?php
include 'config/database.php';

$query_buku = "SELECT COUNT(*) as total FROM buku";
$result_buku = mysqli_query($conn, $query_buku);
$total_buku = mysqli_fetch_assoc($result_buku)['total'];

$query_anggota = "SELECT COUNT(*) as total FROM anggota";
$result_anggota = mysqli_query($conn, $query_anggota);
$total_anggota = mysqli_fetch_assoc($result_anggota)['total'];

$query_pinjam = "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'";
$result_pinjam = mysqli_query($conn, $query_pinjam);
$total_pinjam = mysqli_fetch_assoc($result_pinjam)['total'];

$query_kembali = "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dikembalikan'";
$result_kembali = mysqli_query($conn, $query_kembali);
$total_kembali = mysqli_fetch_assoc($result_kembali)['total'];
?>

<div class="page-header">
    <h2>Dashboard</h2>
    <p>Ringkasan aktivitas perpustakaan</p>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="meta-card-compact">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="width:40px;height:40px;background:var(--surface-soft);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-book" style="font-size:1.1rem;color:var(--ink-deep);"></i>
                </div>
            </div>
            <div style="font-size:12px;font-weight:700;color:var(--steel);letter-spacing:-0.12px;text-transform:uppercase;margin-bottom:4px;">Total Buku</div>
            <div style="font-size:36px;font-weight:500;letter-spacing:-0.36px;color:var(--ink-deep);line-height:1.1;"><?= $total_buku ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="meta-card-compact">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="width:40px;height:40px;background:var(--surface-soft);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-people" style="font-size:1.1rem;color:var(--ink-deep);"></i>
                </div>
            </div>
            <div style="font-size:12px;font-weight:700;color:var(--steel);letter-spacing:-0.12px;text-transform:uppercase;margin-bottom:4px;">Total Anggota</div>
            <div style="font-size:36px;font-weight:500;letter-spacing:-0.36px;color:var(--ink-deep);line-height:1.1;"><?= $total_anggota ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="meta-card-compact">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="width:40px;height:40px;background:rgba(234,179,8,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-arrow-right-circle" style="font-size:1.1rem;color:var(--attention);"></i>
                </div>
            </div>
            <div style="font-size:12px;font-weight:700;color:var(--steel);letter-spacing:-0.12px;text-transform:uppercase;margin-bottom:4px;">Sedang Dipinjam</div>
            <div style="font-size:36px;font-weight:500;letter-spacing:-0.36px;color:var(--ink-deep);line-height:1.1;"><?= $total_pinjam ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="meta-card-compact">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="width:40px;height:40px;background:rgba(34,197,94,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-check-circle" style="font-size:1.1rem;color:var(--success);"></i>
                </div>
            </div>
            <div style="font-size:12px;font-weight:700;color:var(--steel);letter-spacing:-0.12px;text-transform:uppercase;margin-bottom:4px;">Dikembalikan</div>
            <div style="font-size:36px;font-weight:500;letter-spacing:-0.36px;color:var(--ink-deep);line-height:1.1;"><?= $total_kembali ?></div>
        </div>
    </div>
</div>

<!-- Recent loans -->
<div class="meta-card">
    <div class="meta-card-header">Peminjaman Terbaru</div>
    <div class="table-responsive">
        <table class="meta-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Anggota</th>
                    <th>Tanggal Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT p.*, b.judul, a.nama
                         FROM peminjaman p
                         JOIN buku b ON p.id_buku = b.id_buku
                         JOIN anggota a ON p.id_anggota = a.id_anggota
                         ORDER BY p.tanggal_pinjam DESC LIMIT 5";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td style="font-weight:700;"><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?></td>
                        <td>
                            <?php if ($row['status'] == 'dipinjam'): ?>
                                <span class="badge-meta badge-attention">Dipinjam</span>
                            <?php else: ?>
                                <span class="badge-meta badge-success">Dikembalikan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px 16px;color:var(--stone);">
                            Belum ada data peminjaman
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
