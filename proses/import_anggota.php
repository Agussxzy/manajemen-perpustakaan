<?php
include '../config/database.php';
require '../vendor/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;

if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../index.php?page=anggota&status=error&msg=Gagal+upload+file');
    exit;
}

$ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
if ($ext !== 'xlsx') {
    header('Location: ../index.php?page=anggota&status=error&msg=Hanya+file+.xlsx+yang+didukung');
    exit;
}

$file_tmp = $_FILES['file_excel']['tmp_name'];

$xlsx = SimpleXLSX::parse($file_tmp);
if (!$xlsx) {
    header('Location: ../index.php?page=anggota&status=error&msg=Gagal+membaca+file+Excel');
    exit;
}

$rows = $xlsx->rows();
$inserted = 0;
$skipped = 0;
$errors = [];

// Remove header row if it exists (optional — match column headers)
// First row: check if it looks like a header
$start = 0;
if (count($rows) > 0) {
    $first = $rows[0];
    $headerKeywords = ['nomor', 'anggota', 'nama', 'alamat', 'telepon', 'no_'];
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
    $nomor_anggota = trim($row[0] ?? '');
    $nama = trim($row[1] ?? '');
    $alamat = trim($row[2] ?? '');
    $no_telepon = trim($row[3] ?? '');

    if (empty($nomor_anggota) || empty($nama)) {
        $skipped++;
        continue;
    }

    $nomor_anggota = mysqli_real_escape_string($conn, $nomor_anggota);
    $nama = mysqli_real_escape_string($conn, $nama);
    $alamat = mysqli_real_escape_string($conn, $alamat);
    $no_telepon = mysqli_real_escape_string($conn, $no_telepon);

    $check = mysqli_query($conn, "SELECT id_anggota FROM anggota WHERE nomor_anggota = '$nomor_anggota'");
    if (mysqli_num_rows($check) > 0) {
        $skipped++;
        continue;
    }

    $sql = "INSERT INTO anggota (nomor_anggota, nama, alamat, no_telepon) VALUES ('$nomor_anggota', '$nama', '$alamat', '$no_telepon')";
    if (mysqli_query($conn, $sql)) {
        $inserted++;
    } else {
        $errors[] = "Baris " . ($i + 1) . ": " . mysqli_error($conn);
    }
}

$msg = "Berhasil import $inserted anggota.";
if ($skipped > 0) $msg .= " $skipped baris dilewati (duplikat / data kosong).";
if (count($errors) > 0) {
    $msg .= " " . count($errors) . " error.";
    $detail = implode(' | ', $errors);
    header('Location: ../index.php?page=anggota&status=warning&msg=' . urlencode($msg) . '&detail=' . urlencode($detail));
    exit;
}

header('Location: ../index.php?page=anggota&status=success&msg=' . urlencode($msg));
exit;
