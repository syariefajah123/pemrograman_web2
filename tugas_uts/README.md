# Tugas UTS - Pemrograman Web II

Project pendaftaran calon mahasiswa baru sesuai form pada soal UTS, dengan penyimpanan MySQL di tabel `calon_mhs`.

## Isi folder
- `index.php` — form HTML, proses simpan PHP, penghitungan rata-rata, keterangan kelulusan, dan output tabel HTML.
- `calon_mhs.sql` — membuat database `db_calon_mhs` dan tabel `calon_mhs`.

## Cara menjalankan (XAMPP)
1. Salin folder `tugas_uts` ke `C:\\xampp\\htdocs\\tugas_uts`.
2. Buka XAMPP dan jalankan Apache serta MySQL.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih menu **Import**, lalu impor file `calon_mhs.sql`.
5. Buka `http://localhost/tugas_uts/`.
6. Isi data pendaftar, lalu klik **Simpan Data**. Data yang berhasil disimpan akan tampil di tabel.

## Ketentuan pengolahan
- Rata-rata = (Matematika + Bahasa Inggris + Umum) / 3.
- Rata-rata >= 70: `Lulus`.
- Rata-rata >= 60 dan < 70: `Cadangan`.
- Rata-rata < 60: `Tidak Lulus`.
- Contoh kode pendaftaran: `A2-101-9` (Gedung A, Gelombang 2, peserta 101, September).
- Kode bulan satu karakter yang digunakan: `1`-`9` untuk Januari-September, `0` untuk Oktober, `N` untuk November, dan `D` untuk Desember.

## Konfigurasi database
Konfigurasi koneksi MySQL berada di bagian atas `index.php`. Nilai default XAMPP adalah host `localhost`, user `root`, password kosong, database `db_calon_mhs`.

**Catatan:** Mengimpor ulang `calon_mhs.sql` akan menghapus tabel `calon_mhs` beserta data yang tersimpan sebelumnya, lalu membuat tabel baru.
