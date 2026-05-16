<?php
session_start();

$current = $_SESSION['theme'] ?? 'light';
$_SESSION['theme'] = ($current === 'light') ? 'dark' : 'light';

$ref = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header("Location: $ref");
