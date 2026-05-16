<?php 
include '../config/database.php'; 
 
$id_buku = $_POST['id_buku']; 
$judul = $_POST['judul']; 
$pengarang = $_POST['pengarang']; 
$penerbit = $_POST['penerbit']; 
$tahun_terbit = $_POST['tahun_terbit']; 
$stok = $_POST['stok']; 
 
$query = "UPDATE buku SET  
          judul='$judul',  
          pengarang='$pengarang',  
          penerbit='$penerbit',  
          tahun_terbit='$tahun_terbit',  
          stok='$stok'  
          WHERE id_buku=$id_buku"; 
 
if(mysqli_query($conn, $query)) { 
    header("Location: ../index.php?page=buku&status=success"); 
} else { 
    header("Location: ../index.php?page=buku&status=error"); 
} 
?>