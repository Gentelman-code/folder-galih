<?php
session_start();

// Koneksi database
require_once 'db_config.php';

$error = '';
$success = '';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input kosong
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } 
    // Validasi panjang username
    elseif (strlen($username) < 3) {
        $error = 'Username harus minimal 3 karakter!';
    }
    // Validasi panjang password
    elseif (strlen($password) < 6) {
        $error = 'Password harus minimal 6 karakter!';
    }
    // Validasi password match
    elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok!';
    }
    else {
        // Cek apakah username sudah ada
        $username_escape = mysqli_real_escape_string($conn, $username);
        $check_query = "SELECT id FROM users WHERE username = '$username_escape' LIMIT 1";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Username sudah digunakan! Silakan pilih username lain.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user baru
            $insert_query = "INSERT INTO users (username, password, created_at) VALUES ('$username_escape', '$hashed_password', NOW())";
            
            if (mysqli_query($conn, $insert_query)) {
                $success = 'Pendaftaran berhasil! Silakan login dengan akun baru Anda.';
            } else {
                $error = 'Terjadi kesalahan! Silakan coba lagi.';
            }
        }
    }
    
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem Login PHP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .register-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        .alert-success {
            background-color: #efe;
            border: 1px solid #cfc;
            color: #3c3;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #555;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Daftar Akun</h1>
            <p>Silakan isi form di bawah untuk mendaftar</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="./daftar.php">
            <div class="form-group">
                <label for="username">Nama Pengguna</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Masukkan nama pengguna"
                    required
                    autocomplete="username"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan kata sandi"
                    required
                    autocomplete="new-password"
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Kata Sandi</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    placeholder="Masukkan kata sandi lagi"
                    required
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="btn-register">Daftar</button>
        </form>

        <div class="divider">
            <span>atau</span>
        </div>

        <div class="login-link">
            Sudah punya akun? <a href="index.php">Masuk sekarang</a>
        </div>
        <?php else: ?>
        <div class="login-link">
            <a href="index.php">Klik di sini untuk login</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
