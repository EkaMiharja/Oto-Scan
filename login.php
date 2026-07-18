<?php
/**
 * ============================================
 * Halaman Login Penghuni
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 * 
 * Halaman ini menangani proses login penghuni/user:
 * - Validasi email dan password
 * - Verifikasi password dengan password_verify()
 * - Buat session user setelah login berhasil
 * - Redirect ke dashboard setelah berhasil login
 */

// Include file functions
require_once 'functions.php';

// -- Jika user sudah login, redirect ke dashboard --
if (isUserLoggedIn()) {
    redirect(BASE_URL . '/user/dashboard.php');
}

// -- Variabel untuk menampung error --
$error = '';

// -- Proses form login (hanya jika method POST) --
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan sanitasi input dari form
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // Password tidak disanitasi (akan diverifikasi hash)

    // Validasi: pastikan input tidak kosong
    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        // Cari user berdasarkan email di database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verifikasi password dengan hash di database
        if ($user && password_verify($password, $user['password'])) {
            // ✅ Login berhasil - set session data
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_nama']  = $user['nama_lengkap'];
            $_SESSION['user_email'] = $user['email'];

            // Set flash message selamat datang
            setFlash('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
            redirect(BASE_URL . '/user/dashboard.php');
        } else {
            // ❌ Login gagal - email atau password salah
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Penghuni - Sistem Manajemen Kendaraan Perumahan">
    <title>Login - Sistem Kendaraan Perumahan</title>

    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 (Ikon) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-page">

    <!-- ============================================ -->
    <!-- CONTAINER LOGIN                              -->
    <!-- ============================================ -->
    <div class="auth-container">
        <div class="auth-card">

            <!-- Header card dengan ikon dan judul -->
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-car-side"></i>
                </div>
                <h1>Sistem Kendaraan</h1>
                <p>Perumahan Digital</p>
            </div>

            <!-- Body card - Form login -->
            <div class="auth-body">
                <h2>Masuk ke Akun Anda</h2>

                <!-- Tampilkan pesan error jika ada -->
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>

                <!-- Tampilkan flash message (misal dari redirect setelah register) -->
                <?php renderFlash(); ?>

                <!-- Form Login -->
                <form method="POST" action="">
                    <!-- Input Email -->
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control"
                               placeholder="Masukkan email Anda"
                               value="<?= isset($email) ? $email : '' ?>"
                               required>
                    </div>

                    <!-- Input Password -->
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control"
                               placeholder="Masukkan password Anda"
                               required>
                    </div>

                    <!-- Tombol Login -->
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                    </button>
                </form>

                <!-- Footer card - Link registrasi & admin login -->
                <div class="auth-footer">
                    <p>Belum punya akun? <a href="<?= BASE_URL ?>/register.php">Daftar di sini</a></p>
                    <hr>
                    <a href="<?= BASE_URL ?>/admin/login.php" class="admin-link">
                        <i class="fas fa-shield-halved"></i> Login sebagai Admin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
