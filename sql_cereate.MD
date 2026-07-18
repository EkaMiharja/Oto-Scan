-- Buat Database
CREATE DATABASE IF NOT EXISTS perumahan_kendaraan;
USE perumahan_kendaraan;

-- =============================================
-- TABEL 1: USERS (Penghuni)
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(150) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tanggal_daftar DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================
-- TABEL 2: VEHICLES (Kendaraan)
-- =============================================
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nomor_plat VARCHAR(20) UNIQUE NOT NULL,
    jenis ENUM('Mobil', 'Motor') NOT NULL,
    merek_model VARCHAR(100) NOT NULL,
    tanggal_daftar_kendaraan DATE NOT NULL,
    qr_code_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABEL 3: ADMINS (Admin)
-- =============================================
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- Insert Admin Default (username: admin, password: admin123)
-- =============================================
INSERT INTO admins (nama, username, password) VALUES 
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); 
-- Password di atas adalah hash dari "admin123"