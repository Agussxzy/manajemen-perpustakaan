<?php 
$host = 'localhost'; 
$user = 'root'; 
$pass = ''; 
$dbname = 'perpustakaan'; 
 
$conn = mysqli_connect($host, $user, $pass, $dbname); 
 
if (!$conn) { 
    die("Koneksi database gagal: " . mysqli_connect_error()); 
} 
 
// Set timezone 
date_default_timezone_set('Asia/Jakarta'); 
?>