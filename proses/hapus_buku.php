<?php
include '../config/database.php';

$id = $_GET['id'];
$query = "DELETE FROM buku WHERE id_buku=$id";

if (mysqli_query($conn, $query)) {
    header("Location: ../index.php?page=buku&status=deleted");
} else {
    header("Location: ../index.php?page=buku&status=error");
}
