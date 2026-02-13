<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ./index.php?error=unauthorized");
    exit();
}

// Ambil data user dari session
$username = $_SESSION['username'];
$login_time = date('d F Y, H:i:s', $_SESSION['login_time']);

// Koneksi database untuk mendapatkan info tambahan
require_once './db_config.php';
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #ff6b6b;
            --secondary: #4ecdc4;
            --dark: #2d3436;
            --light: #f8f9fa;
            --accent: #ffd93d;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            padding: 25px 35px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.6s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-left h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 32px;
            background: linear-gradient(135deg, #fff, var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 2px;
        }

        .header-left p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-top: 5px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            text-align: right;
        }

        .user-info .username {
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }

        .user-info .role {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--primary), #e74c3c);
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.4);
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 25px;
            padding: 50px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.8s ease 0.2s backwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -50px;
            right: -50px;
        }

        .welcome-card h2 {
            color: #fff;
            font-size: 36px;
            margin-bottom: 10px;
            position: relative;
        }

        .welcome-card p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            position: relative;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            padding: 30px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            animation: fadeIn 0.8s ease backwards;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--secondary);
            box-shadow: 0 15px 35px rgba(78, 205, 196, 0.2);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .stat-card h3 {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .stat-value {
            color: #fff;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-change {
            color: var(--secondary);
            font-size: 13px;
            font-weight: 600;
        }

        /* Info Cards */
        .info-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            padding: 35px;
            animation: fadeIn 0.8s ease backwards;
        }

        .info-card h3 {
            color: #fff;
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .info-value {
            color: #fff;
            font-weight: 600;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .user-info {
                text-align: center;
            }

            .welcome-card {
                padding: 30px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .info-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>DASHBOARD</h1>
                <p>Sistem Manajemen Portal</p>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="username">👤 <?php echo htmlspecialchars($username); ?></div>
                    <div class="role">Administrator</div>
                </div>
                <a href="./logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="welcome-card">
            <h2>Selamat Datang Kembali! 👋</h2>
            <p>Senang melihat Anda lagi, <?php echo htmlspecialchars($username); ?>. Anda login pada <?php echo $login_time; ?></p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card" style="animation-delay: 0.3s;">
                <div class="stat-icon">📊</div>
                <h3>Total Pengguna</h3>
                <div class="stat-value">
                    <?php
                    $count_query = "SELECT COUNT(*) as total FROM users";
                    $count_result = mysqli_query($conn, $count_query);
                    $count_data = mysqli_fetch_assoc($count_result);
                    echo $count_data['total'];
                    ?>
                </div>
                <div class="stat-change">↑ 12% dari bulan lalu</div>
            </div>

            <div class="stat-card" style="animation-delay: 0.4s;">
                <div class="stat-icon">✅</div>
                <h3>Login Berhasil</h3>
                <div class="stat-value">100%</div>
                <div class="stat-change">Status: Aktif</div>
            </div>

            <div class="stat-card" style="animation-delay: 0.5s;">
                <div class="stat-icon">🔐</div>
                <h3>Keamanan</h3>
                <div class="stat-value">Tinggi</div>
                <div class="stat-change">Enkripsi Aktif</div>
            </div>

            <div class="stat-card" style="animation-delay: 0.6s;">
                <div class="stat-icon">⚡</div>
                <h3>Session Aktif</h3>
                <div class="stat-value">1</div>
                <div class="stat-change">Login saat ini</div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-card" style="animation-delay: 0.7s;">
                <h3>📋 Informasi Akun</h3>
                <div class="info-item">
                    <span class="info-label">Username</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">User ID</span>
                    <span class="info-value">#<?php echo $user['id']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Terdaftar Sejak</span>
                    <span class="info-value"><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status Akun</span>
                    <span class="info-value" style="color: var(--secondary);">Aktif</span>
                </div>
            </div>

            <div class="info-card" style="animation-delay: 0.8s;">
                <h3>🔒 Keamanan</h3>
                <div class="info-item">
                    <span class="info-label">Password Hash</span>
                    <span class="info-value" style="color: var(--secondary);">✓ Terenkripsi</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Login Terakhir</span>
                    <span class="info-value">
                        <?php
                        if ($user['last_login']) {
                            echo date('d M Y, H:i', strtotime($user['last_login']));
                        } else {
                            echo 'Pertama kali';
                        }
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">IP Address</span>
                    <span class="info-value"><?php echo $_SERVER['REMOTE_ADDR']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Session Timeout</span>
                    <span class="info-value">30 menit</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto logout setelah tidak aktif (30 menit)
        let inactivityTime = function () {
            let time;
            window.onload = resetTimer;
            document.onmousemove = resetTimer;
            document.onkeypress = resetTimer;
            document.onclick = resetTimer;
            document.onscroll = resetTimer;

            function logout() {
                alert('Sesi Anda telah berakhir karena tidak aktif.');
                window.location.href = './logout.php';
            }

            function resetTimer() {
                clearTimeout(time);
                time = setTimeout(logout, 1800000); // 30 menit
            }
        };

        inactivityTime();
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>
