<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Hapus cookie remember me jika ada
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect ke halaman login
header("Location: ./index.php?error=session");
exit();
?>
