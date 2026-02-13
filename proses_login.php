<?php
session_start();
require_once 'db_config.php';

$nama_pengguna = mysqli_real_escape_string($conn, $_POST['username']);
$kata_sandi = $_POST['password'];

$query = "SELECT * FROM users WHERE username='$nama_pengguna'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    if (password_verify($kata_sandi, $row['password'])) {
        // Set session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $nama_pengguna;
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        // Update last login
        $update_query = "UPDATE users SET last_login = NOW() WHERE id = " . $row['id'];
        mysqli_query($conn, $update_query);

        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: index.php?error=invalid");
        exit();
    }
} else {
    header("Location: index.php?error=invalid");
    exit();
}

mysqli_close($conn);
?>
