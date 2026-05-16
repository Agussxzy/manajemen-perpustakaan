<?php
include 'config/database.php';

$query_buku = "SELECT * FROM buku WHERE stok > 0 ORDER BY judul";
$buku_list = mysqli_query($conn, $query_buku);

$query_anggota = "SELECT * FROM anggota ORDER BY nama";
$anggota_list = mysqli_query($conn, $query_anggota);

$query_pinjam = "SELECT p.*, b.judul, a.nama, a.nomor_anggota
                 FROM peminjaman p
                 JOIN buku b ON p.id_buku = b.id_buku
                 JOIN anggota a ON p.id_anggota = a.id_anggota
                 WHERE p.status = 'dipinjam'
                 ORDER BY p.tanggal_pinjam DESC";
$pinjam_list = mysqli_query($conn, $query_pinjam);

$status = $_GET['status'] ?? '';
?>

<div class="page-header">
    <h2>Peminjaman</h2>
    <p>Pinjamkan buku kepada anggota</p>
</div>

<?php if ($status == 'stok_habis'): ?>
    <div class="alert alert-meta alert-meta-danger mb-3">
        <i class="bi bi-exclamation-triangle me-2"></i> Stok buku habis.
    </div>
<?php elseif ($status == 'success'): ?>
    <div class="alert alert-meta alert-meta-success mb-3">
        <i class="bi bi-check-circle me-2"></i> Peminjaman berhasil disimpan.
    </div>
<?php elseif ($status == 'error'): ?>
    <div class="alert alert-meta alert-meta-danger mb-3">
        <i class="bi bi-exclamation-triangle me-2"></i> Gagal memproses peminjaman.
    </div>
<?php endif; ?>

<!-- Form -->
<div class="meta-card mb-4">
    <div class="meta-card-header">Form Peminjaman</div>
    <form action="proses/simpan_peminjaman.php" method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Pilih Buku</label>
                <select name="id_buku" class="form-select" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php while ($buku = mysqli_fetch_assoc($buku_list)): ?>
                        <option value="<?= $buku['id_buku'] ?>">
                            <?= htmlspecialchars($buku['judul']) ?> (Stok: <?= $buku['stok'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Pilih Anggota</label>
                <select name="id_anggota" class="form-select" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php while ($anggota = mysqli_fetch_assoc($anggota_list)): ?>
                        <option value="<?= $anggota['id_anggota'] ?>">
                            <?= htmlspecialchars($anggota['nomor_anggota']) ?> — <?= htmlspecialchars($anggota['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Pinjam</label>
                <input type="date" name="tanggal_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Jatuh Tempo (7 hari)</label>
                <input type="date" name="tanggal_jatuh_tempo" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-meta-primary mt-3">
            <i class="bi bi-check-lg me-1"></i> Simpan Peminjaman
        </button>
    </form>
</div>

<!-- Active loans -->
<div class="meta-card">
    <div class="meta-card-header">Peminjaman Aktif</div>
    <div class="table-responsive">
        <table class="meta-table">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Buku</th>
                    <th>Anggota</th>
                    <th>Tgl Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($pinjam_list) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($pinjam_list)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td style="font-weight:700;"><?= htmlspecialchars($row['judul']) ?></td>
                            <td>
                                <?= htmlspecialchars($row['nama']) ?>
                                <br><span style="font-size:12px;color:var(--steel);"><?= htmlspecialchars($row['nomor_anggota']) ?></span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                            <td>
                                <?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?>
                                <?php if (strtotime($row['tanggal_jatuh_tempo']) < time()): ?>
                                    <br><span class="badge-meta badge-critical" style="margin-top:4px;">Terlambat</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge-meta badge-attention">Dipinjam</span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px 16px;color:var(--stone);">Tidak ada peminjaman aktif</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
