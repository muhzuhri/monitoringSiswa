-- Database: monitoring_siswa
-- Import file ini ke MySQL (Laragon) sebelum menjalankan aplikasi

CREATE DATABASE IF NOT EXISTS monitoring_siswa
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE monitoring_siswa;

-- Tabel pengguna dengan berbagai peran:
-- - siswa
-- - guru
-- - dosen
-- - admin

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('siswa', 'guru', 'dosen', 'admin') NOT NULL,

    -- Data khusus siswa
    nim VARCHAR(50) NULL,
    kelas VARCHAR(50) NULL,
    jurusan VARCHAR(100) NULL,
    sekolah VARCHAR(150) NULL,
    perusahaan VARCHAR(150) NULL,

    -- Data khusus guru
    nip_guru VARCHAR(50) NULL,
    mapel VARCHAR(100) NULL,

    -- Data khusus dosen pembimbing
    nidn VARCHAR(50) NULL,
    prodi VARCHAR(100) NULL,
    perguruan_tinggi VARCHAR(150) NULL,

    -- Data khusus admin
    instansi VARCHAR(150) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

