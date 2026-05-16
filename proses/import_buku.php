<?php
include '../config/database.php';
require '../vendor/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;

if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../index.php?page=buku&status=error&msg=Gagal+upload+file');
    exit;
}

$ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
if ($ext !== 'xlsx') {
    header('Location: ../index.php?page=buku&status=error&msg=Hanya+file+.xlsx+yang+didukung');
    exit;
}

$xlsx = SimpleXLSX::parse($_FILES['file_excel']['tmp_name']);
if (!$xlsx) {
    header('Location: ../index.php?page=buku&status=error&msg=Gagal+membaca+file+Excel');
    exit;
}

$rows = $xlsx->rows();
$inserted = 0;
$skipped = 0;
$errors = [];

$start = 0;
if (count($rows) > 0) {
    $first = $rows[0];
    $headerKeywords = ['judul', 'pengarang', 'penerbit', 'tahun', 'stok', 'buku'];
    $isHeader = false;
    foreach ($first as $cell) {
        $cellLower = strtolower(trim((string)$cell));
        foreach ($headerKeywords as $kw) {
            if (strpos($cellLower, $kw) !== false) {
                $isHeader = true;
                break 2;
            }
        }
    }
    if ($isHeader) {
        $start = 1;
    }
}

for ($i = $start; $i < count($rows); $i++) {
    $row = $rows[$i];
    $judul = trim($row[0] ?? '');
    $pengarang = trim($row[1] ?? '');
    $penerbit = trim($row[2] ?? '');
    $tahun_terbit = trim($row[3] ?? '');
    $stok = trim($row[4] ?? '');

    if (empty($judul) || empty($pengarang)) {
        $skipped++;
        continue;
    }

    $judul = mysqli_real_escape_string($conn, $judul);
    $pengarang = mysqli_real_escape_string($conn, $pengarang);
    $penerbit = mysqli_real_escape_string($conn, $penerbit);
    $tahun_terbit = is_numeric($tahun_terbit) ? (int)$tahun_terbit : 'NULL';
    $stok = is_numeric($stok) ? (int)$stok : 1;

    $sql = "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, stok) VALUES ('$judul', '$pengarang', '$penerbit', " . ($tahun_terbit === 'NULL' ? 'NULL' : $tahun_terbit) . ", $stok)";
    if (mysqli_query($conn, $sql)) {
        $inserted++;
    } else {
        $errors[] = "Baris " . ($i + 1) . ": " . mysqli_error($conn);
    }
}

$msg = "Berhasil import $inserted buku.";
if ($skipped > 0) $msg .= " $skipped baris dilewati (data kosong).";
if (count($errors) > 0) {
    $msg .= " " . count($errors) . " error.";
    $detail = implode(' | ', $errors);
    header('Location: ../index.php?page=buku&status=warning&msg=' . urlencode($msg) . '&detail=' . urlencode($detail));
    exit;
}

header('Location: ../index.php?page=buku&status=success&msg=' . urlencode($msg));
exit;
