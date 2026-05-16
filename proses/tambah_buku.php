<?php
include '../config/database.php';

$judul = $_POST['judul'];
$pengarang = $_POST['pengarang'];
$penerbit = $_POST['penerbit'];
$tahun_terbit = $_POST['tahun_terbit'];
$stok = $_POST['stok'];

$query = "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, stok)  
          VALUES ('$judul', '$pengarang', '$penerbit', '$tahun_terbit', '$stok')";

if (mysqli_query($conn, $query)) {
    header("Location: ../index.php?page=buku&status=success");
} else {
    header("Location: ../index.php?page=buku&status=error");
}
