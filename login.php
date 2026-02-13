<?php
session_start();

// Koneksi database
require_once 'db_config.php';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil data dari form
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    // Validasi input kosong
    if (empty($username) || empty($password)) {
        header("Location: ./index.php?error=empty");
        exit();
    }

    // Validasi panjang input
    if (strlen($username) < 3 || strlen($password) < 6) {
        header("Location: ./index.php?error=invalid");
        exit();
    }

    // Escape string untuk mencegah SQL injection
    $username = mysqli_real_escape_string($conn, $username);

    // Query untuk mengecek user
    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $user['password'])) {

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();

            // Update last login
            $update_query = "UPDATE users SET last_login = NOW() WHERE id = " . $user['id'];
            mysqli_query($conn, $update_query);

            // Remember me functionality
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 hari

                // Simpan token ke database
                $hashed_token = password_hash($token, PASSWORD_DEFAULT);
                $save_token = "UPDATE users SET remember_token = '$hashed_token' WHERE id = " . $user['id'];
                mysqli_query($conn, $save_token);
            }

            // Redirect ke dashboard
            header("Location: dashboard.php");
            exit();

        } else {
            // Password salah
            header("Location: index.php?error=invalid");
            exit();
        }
    } else {
        // Username tidak ditemukan
        header("Location: index.php?error=invalid");
        exit();
    }

    mysqli_close($conn);

} else {
    // Jika akses langsung tanpa POST
    header("Location: ./index.php");
    exit();
}
?>
