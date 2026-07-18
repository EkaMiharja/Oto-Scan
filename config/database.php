<?php
/**
 * ============================================
 * Konfigurasi Database
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 * 
 * File ini menangani koneksi ke database MySQL
 * menggunakan PDO (PHP Data Objects).
 * Konfigurasi menggunakan default Laragon.
 * 
 * Database: perumahan_kendaraan
 * Host: localhost
 * User: root
 * Password: (kosong - default Laragon)
 */

// -- Konfigurasi koneksi database --
$db_host    = 'localhost';              // Host database
$db_name    = 'perumahan_kendaraan';    // Nama database
$db_user    = 'root';                   // Username (default Laragon)
$db_pass    = '';                       // Password (default Laragon: kosong)
$db_charset = 'utf8mb4';               // Charset untuk mendukung emoji & karakter khusus

try {
    // Membuat koneksi PDO ke database MySQL
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset={$db_charset}",
        $db_user,
        $db_pass,
        [
            // Set mode error ke exception untuk debugging
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Set default fetch mode ke associative array
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Matikan emulasi prepared statement (keamanan lebih baik)
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

} catch (PDOException $e) {
    // Tampilkan pesan error jika koneksi gagal
    // Di production, sebaiknya log error dan tampilkan pesan generik
    die("❌ Koneksi database gagal: " . $e->getMessage());
}
