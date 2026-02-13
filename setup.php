<?php
// Setup database dan tabel untuk sistem login

require_once 'db_config.php';

// SQL untuk membuat tabel users
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    remember_token VARCHAR(255),
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Eksekusi query
if (mysqli_query($conn, $sql)) {
    echo "Tabel 'users' berhasil dibuat atau sudah ada.<br>";

    // Cek apakah ada user admin default
    $check_admin = "SELECT COUNT(*) as count FROM users WHERE username = 'admin'";
    $result = mysqli_query($conn, $check_admin);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] == 0) {
        // Buat user admin default
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_admin = "INSERT INTO users (username, password, email) VALUES ('admin', '$admin_password', 'admin@example.com')";

        if (mysqli_query($conn, $insert_admin)) {
            echo "User admin default berhasil dibuat.<br>";
            echo "Username: admin<br>";
            echo "Password: admin123<br>";
        } else {
            echo "Error membuat user admin: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "User admin sudah ada.<br>";
    }

} else {
    echo "Error membuat tabel: " . mysqli_error($conn) . "<br>";
}

mysqli_close($conn);

echo "<br><a href='index.php'>Kembali ke Login</a>";
?>
