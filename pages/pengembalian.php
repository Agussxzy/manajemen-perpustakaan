<?php
include 'config/database.php';

function get_anggota($conn) {
    $result = mysqli_query($conn, "SELECT * FROM anggota ORDER BY nama");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_peminjaman_anggota($conn, $id_anggota) {
    $query = "SELECT p.*, b.judul, a.nama, a.nomor_anggota
              FROM peminjaman p
              JOIN buku b ON p.id_buku = b.id_buku
              JOIN anggota a ON p.id_anggota = a.id_anggota
              WHERE p.status='dipinjam' AND p.id_anggota='$id_anggota'
              ORDER BY p.tanggal_pinjam DESC";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$anggota_list = get_anggota($conn);
$id_anggota_selected = $_POST['id_anggota'] ?? '';
$peminjaman_list = [];

if ($id_anggota_selected != '') {
    $peminjaman_list = get_peminjaman_anggota($conn, $id_anggota_selected);
}

$status = $_GET['status'] ?? '';
$DENDA_PER_HARI = 1000;
?>

<div class="page-header">
    <h2>Pengembalian</h2>
    <p>Proses pengembalian buku dan hitung denda</p>
</div>

<?php if ($status == 'success_kembali'): ?>
    <div class="alert alert-meta alert-meta-success mb-3">
        <i class="bi bi-check-circle me-2"></i> Pengembalian berhasil diproses.
    </div>
<?php elseif ($status == 'sudah_dikembalikan'): ?>
    <div class="alert alert-meta alert-meta-warning mb-3">
        <i class="bi bi-exclamation-triangle me-2"></i> Peminjaman ini sudah dikembalikan.
    </div>
<?php elseif ($status == 'tidak_ditemukan'): ?>
    <div class="alert alert-meta alert-meta-danger mb-3">
        <i class="bi bi-x-circle me-2"></i> Data peminjaman tidak ditemukan.
    </div>
<?php elseif ($status == 'error'): ?>
    <div class="alert alert-meta alert-meta-danger mb-3">
        <i class="bi bi-x-circle me-2"></i> Gagal memproses pengembalian.
    </div>
<?php endif; ?>

<!-- Search anggota -->
<div class="meta-card-compact mb-4">
    <form method="POST" class="row g-2 align-items-end">
        <div class="col">
            <label class="form-label">Pilih Anggota</label>
            <select name="id_anggota" class="form-select" required>
                <option value="">-- Pilih Anggota --</option>
                <?php foreach ($anggota_list as $anggota): ?>
                    <option value="<?= $anggota['id_anggota'] ?>" <?= ($id_anggota_selected == $anggota['id_anggota']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($anggota['nomor_anggota']) ?> — <?= htmlspecialchars($anggota['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-meta-primary">
                <i class="bi bi-search me-1"></i> Cari
            </button>
        </div>
    </form>
</div>

<!-- Loans list -->
<?php if ($peminjaman_list): ?>
    <div class="meta-card">
        <div class="meta-card-header">Peminjaman Aktif</div>
        <div class="table-responsive">
            <table class="meta-table">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($peminjaman_list as $row): ?>
                        <?php
                        $jatuh_tempo = strtotime($row['tanggal_jatuh_tempo']);
                        $hari_ini = strtotime(date('Y-m-d'));
                        $terlambat = ($hari_ini > $jatuh_tempo);
                        $hari_telat = $terlambat ? ceil(($hari_ini - $jatuh_tempo) / 86400) : 0;
                        $total_denda = $hari_telat * $DENDA_PER_HARI;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['judul']) ?></strong>
                                <br><span style="font-size:12px;color:var(--steel);"><?= htmlspecialchars($row['nama']) ?> (<?= htmlspecialchars($row['nomor_anggota']) ?>)</span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                            <td>
                                <?= date('d/m/Y', $jatuh_tempo) ?>
                                <?php if ($terlambat): ?>
                                    <br><span class="badge-meta badge-critical" style="margin-top:4px;">+<?= $hari_telat ?> hari</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge-meta badge-attention">Dipinjam</span></td>
                            <td>
                                <?php if ($total_denda > 0): ?>
                                    <span class="badge-meta badge-critical">Rp <?= number_format($total_denda, 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span class="badge-meta badge-success">Gratis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-meta-cobalt btn-meta-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalKonfirmasi"
                                        data-id="<?= $row['id_peminjaman'] ?>"
                                        data-judul="<?= addslashes($row['judul']) ?>"
                                        data-nama="<?= addslashes($row['nama']) ?>"
                                        data-tgl-pinjam="<?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?>"
                                        data-tgl-jatuh="<?= date('d/m/Y', $jatuh_tempo) ?>"
                                        data-hari-telat="<?= $hari_telat ?>"
                                        data-total-denda="<?= $total_denda ?>">
                                    Kembalikan
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($id_anggota_selected != ''): ?>
    <div class="alert alert-meta alert-meta-info">
        <i class="bi bi-info-circle me-2"></i> Anggota ini tidak memiliki peminjaman aktif.
    </div>
<?php else: ?>
    <div class="alert alert-meta alert-meta-warning" style="background:var(--surface-soft);color:var(--steel);">
        <i class="bi bi-hand-index me-2"></i> Pilih anggota untuk melihat data peminjaman.
    </div>
<?php endif; ?>

<!-- Confirmation Modal -->
<div class="modal fade" id="modalKonfirmasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="proses/proses_pengembalian.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Pengembalian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_peminjaman" id="modal_id_peminjaman">

                    <div class="mb-3">
                        <label class="form-label">Judul Buku</label>
                        <p style="font-size:24px;font-weight:500;letter-spacing:-0.24px;color:var(--ink-deep);margin:0;" id="modal_judul">-</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Peminjam</label>
                        <p style="margin:0;" id="modal_nama">-</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Tanggal Pinjam</label>
                            <p style="margin:0;" id="modal_tgl_pinjam">-</p>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jatuh Tempo</label>
                            <p style="margin:0;" id="modal_tgl_jatuh">-</p>
                        </div>
                    </div>

                    <hr style="border-color:var(--hairline-soft);">

                    <div class="mb-3">
                        <label class="form-label">Perhitungan Denda</label>
                        <table class="meta-table" style="margin-bottom:8px;">
                            <tr>
                                <td style="padding:8px 0;">Hari Terlambat</td>
                                <td style="padding:8px 0;text-align:right;" id="modal_hari_telat">0 hari</td>
                            </tr>
                            <tr>
                                <td style="padding:8px 0;">Tarif Denda</td>
                                <td style="padding:8px 0;text-align:right;">Rp <?= number_format($DENDA_PER_HARI, 0, ',', '.') ?>/hari</td>
                            </tr>
                            <tr style="background:rgba(228,30,63,0.06);">
                                <td style="padding:10px 12px;font-weight:700;">Total Denda</td>
                                <td style="padding:10px 12px;text-align:right;font-weight:700;color:var(--critical-strong);" id="modal_total_denda">Rp 0</td>
                            </tr>
                        </table>
                        <small style="color:var(--stone);">* Denda dibayarkan langsung di loket.</small>
                    </div>

                    <div class="alert alert-meta alert-meta-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Tanggal kembali: <strong><?= date('d/m/Y') ?></strong><br>
                        Stok buku akan otomatis bertambah setelah pengembalian.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-meta-ghost btn-meta-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-meta-cobalt btn-meta-sm">Proses Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalKonfirmasi');
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('modal_id_peminjaman').value = button.dataset.id;
        document.getElementById('modal_judul').textContent = button.dataset.judul;
        document.getElementById('modal_nama').textContent = button.dataset.nama;
        document.getElementById('modal_tgl_pinjam').textContent = button.dataset.tglPinjam;
        document.getElementById('modal_tgl_jatuh').textContent = button.dataset.tglJatuh;
        const hariTelat = parseInt(button.dataset.hariTelat);
        const totalDenda = parseInt(button.dataset.totalDenda);
        document.getElementById('modal_hari_telat').textContent = hariTelat + ' hari';
        document.getElementById('modal_total_denda').textContent = 'Rp ' + totalDenda.toLocaleString('id-ID');
    });
});
</script>
