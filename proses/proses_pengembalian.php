<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_peminjaman'])) {
    header("Location: ../index.php?page=pengembalian");
    exit;
}

$id_peminjaman = (int)$_POST['id_peminjaman'];
$tanggal_kembali = date('Y-m-d');
$DENDA_PER_HARI = 1000;

$query = "SELECT p.*, b.id_buku, b.stok
          FROM peminjaman p
          JOIN buku b ON p.id_buku = b.id_buku
          WHERE p.id_peminjaman = $id_peminjaman";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: ../index.php?page=pengembalian&status=tidak_ditemukan");
    exit;
}

if ($data['status'] == 'dikembalikan') {
    header("Location: ../index.php?page=pengembalian&status=sudah_dikembalikan");
    exit;
}

$jatuh_tempo = strtotime($data['tanggal_jatuh_tempo']);
$hari_ini    = strtotime($tanggal_kembali);
$hari_telat  = ($hari_ini > $jatuh_tempo) ? ceil(($hari_ini - $jatuh_tempo) / 86400) : 0;
$total_denda = $hari_telat * $DENDA_PER_HARI;

mysqli_begin_transaction($conn);

try {
    // Update status
    $query_update = "UPDATE peminjaman 
                     SET tanggal_kembali = '$tanggal_kembali',
                         status = 'dikembalikan'
                     WHERE id_peminjaman = $id_peminjaman";
    mysqli_query($conn, $query_update);
    
    $query_stok = "UPDATE buku 
                   SET stok = stok + 1 
                   WHERE id_buku = " . $data['id_buku'];
    mysqli_query($conn, $query_stok);
    mysqli_commit($conn);
    
    header("Location: ../index.php?page=pengembalian&status=success_kembali&denda=" . $total_denda);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    error_log("Error pengembalian: " . $e->getMessage());
    header("Location: ../index.php?page=pengembalian&status=error");
}
exit;
?>