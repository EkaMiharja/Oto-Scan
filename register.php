<?php
/**
 * ============================================
 * Halaman Registrasi Penghuni
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 * 
 * Halaman ini menangani proses registrasi penghuni baru:
 * - Form registrasi (nama lengkap, email, password, konfirmasi)
 * - Validasi input server-side (format email, panjang password, dll)
 * - Cek duplikasi email
 * - Password hashing dengan password_hash()
 * - Simpan data ke tabel users
 * - Redirect ke login setelah berhasil
 */

// Include file functions
require_once 'functions.php';

// -- Jika user sudah login, redirect ke dashboard --
if (isUserLoggedIn()) {
    redirect(BASE_URL . '/user/dashboard.php');
}

// -- Variabel untuk error dan menyimpan input lama --
$error = '';
$old = []; // Untuk re-fill form jika terjadi error

// -- Proses form registrasi (hanya jika method POST) --
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan sanitasi input dari form
    $nama     = sanitize($_POST['nama_lengkap'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Simpan input lama untuk ditampilkan kembali jika error
    $old = ['nama_lengkap' => $nama, 'email' => $email];

    // -- Validasi input --
    if (empty($nama) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // Cek apakah email sudah terdaftar di database
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
        } else {
            // -- Simpan data user baru ke database --
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash password
            $tanggalDaftar  = date('Y-m-d'); // Tanggal hari ini

            $stmt = $pdo->prepare(
                "INSERT INTO users (nama_lengkap, email, password, tanggal_daftar) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$nama, $email, $hashedPassword, $tanggalDaftar]);

            // ✅ Registrasi berhasil
            setFlash('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
            redirect(BASE_URL . '/login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Registrasi Penghuni - Sistem Manajemen Kendaraan Perumahan">
    <title>Registrasi - Sistem Kendaraan Perumahan</title>

    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-page">

    <!-- ============================================ -->
    <!-- CONTAINER REGISTRASI                         -->
    <!-- ============================================ -->
    <div class="auth-container">
        <div class="auth-card">

            <!-- Header card -->
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Daftar Akun Baru</h1>
                <p>Sistem Kendaraan Perumahan</p>
            </div>

            <!-- Body card - Form registrasi -->
            <div class="auth-body">

                <!-- Tampilkan error jika ada -->
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>

                <!-- Form Registrasi -->
                <form method="POST" action="">
                    <!-- Input Nama Lengkap -->
                    <div class="form-group">
                        <label for="nama_lengkap">
                            <i class="fas fa-user"></i> Nama Lengkap
                        </label>
                        <input type="text"
                               id="nama_lengkap"
                               name="nama_lengkap"
                               class="form-control"
                               placeholder="Masukkan nama lengkap"
                               value="<?= $old['nama_lengkap'] ?? '' ?>"
                               required>
                    </div>

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
                               value="<?= $old['email'] ?? '' ?>"
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
                               placeholder="Minimal 6 karakter"
                               required
                               minlength="6">
                    </div>

                    <!-- Input Konfirmasi Password -->
                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-lock"></i> Konfirmasi Password
                        </label>
                        <input type="password"
                               id="confirm_password"
                               name="confirm_password"
                               class="form-control"
                               placeholder="Ulangi password Anda"
                               required
                               minlength="6">
                    </div>

                    <!-- Tombol Daftar -->
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                    </button>
                </form>

                <!-- Footer card - Link ke login -->
                <div class="auth-footer">
                    <p>Sudah punya akun? <a href="<?= BASE_URL ?>/login.php">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
