-- Database tugas Pemrograman Web II - Pendaftaran Calon Mahasiswa
CREATE DATABASE IF NOT EXISTS db_calon_mhs
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE db_calon_mhs;

-- Jika tabel lama dari versi project sebelumnya ada, hapus agar struktur sesuai.
-- Catatan: mengimpor ulang file ini akan menghapus data pada tabel calon_mhs.
DROP TABLE IF EXISTS calon_mhs;

CREATE TABLE calon_mhs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    kode VARCHAR(120) NOT NULL,
    kode_pendaftaran VARCHAR(10) NOT NULL,
    nama_pendaftar VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    ttl VARCHAR(100) NOT NULL,
    asal_sekolah VARCHAR(120) NOT NULL,
    pekerjaan_ortu VARCHAR(100) NOT NULL,
    nilai_matematika TINYINT UNSIGNED NOT NULL,
    nilai_bahasa_inggris TINYINT UNSIGNED NOT NULL,
    nilai_umum TINYINT UNSIGNED NOT NULL,
    rata_rata DECIMAL(5,2) NOT NULL,
    keterangan ENUM('Lulus', 'Cadangan', 'Tidak Lulus') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_kode_pendaftaran (kode_pendaftaran),
    CONSTRAINT chk_nilai_matematika CHECK (nilai_matematika BETWEEN 0 AND 100),
    CONSTRAINT chk_nilai_bahasa_inggris CHECK (nilai_bahasa_inggris BETWEEN 0 AND 100),
    CONSTRAINT chk_nilai_umum CHECK (nilai_umum BETWEEN 0 AND 100)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
