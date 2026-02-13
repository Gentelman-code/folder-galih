<?php
$servername = "localhost";
$username = "root";
$password = "";     // Default password XAMPP kosong
$dbname = "kelasdkv";

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Mengecek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
