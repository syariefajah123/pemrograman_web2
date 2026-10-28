<?php
session_start();

// Konfigurasi database XAMPP. Sesuaikan bila username/password MySQL berbeda.
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'db_calon_mhs';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = null;
$connectionError = '';
$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType = $_SESSION['flash_type'] ?? 'success';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    $connectionError = 'Koneksi database gagal. Pastikan MySQL aktif dan file calon_mhs.sql sudah diimpor melalui phpMyAdmin.';
}

/** Mengubah nilai menjadi teks yang aman ditampilkan di HTML. */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Menguraikan kode contoh seperti A2-101-9. */
function uraikanKodePendaftaran(string $kodePendaftaran): ?array
{
    if (!preg_match('/^([ABV])([1-9])-(\d{3})-([0-9OND])$/i', $kodePendaftaran, $m)) {
        return null;
    }

    $lokasi = [
        'A' => 'Gedung A',
        'B' => 'Gedung B',
        'V' => 'Viktor',
    ];
    // Kode bulan satu karakter: 1-9 untuk Januari-September, 0 untuk Oktober,
    // N untuk November, dan D untuk Desember.
    $bulan = [
        '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
        '4' => 'April', '5' => 'Mei', '6' => 'Juni',
        '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
        '0' => 'Oktober', 'N' => 'November', 'D' => 'Desember',
    ];

    $tempat = strtoupper($m[1]);
    $gelombang = (int)$m[2];
    $kodeBulan = strtoupper($m[4]);

    return [
        'kode' => $tempat . $gelombang . ' - ' . $lokasi[$tempat] . ' - Gelombang ' . $gelombang . ' - ' . $bulan[$kodeBulan],
        'lokasi' => $lokasi[$tempat],
        'gelombang' => $gelombang,
        'bulan' => $bulan[$kodeBulan],
    ];
}

// Proses input dan penyimpanan data calon mahasiswa ke database.
if ($conn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $kodePendaftaran = strtoupper(trim($_POST['kode_pendaftaran'] ?? ''));
    $namaPendaftar = trim($_POST['nama_pendaftar'] ?? '');
    $jenisKelamin = trim($_POST['jenis_kelamin'] ?? '');
    $ttl = trim($_POST['ttl'] ?? '');
    $asalSekolah = trim($_POST['asal_sekolah'] ?? '');
    $pekerjaanOrtu = trim($_POST['pekerjaan_ortu'] ?? '');
    $nilaiMatematika = filter_var($_POST['nilai_matematika'] ?? null, FILTER_VALIDATE_INT);
    $nilaiBahasaInggris = filter_var($_POST['nilai_bahasa_inggris'] ?? null, FILTER_VALIDATE_INT);
    $nilaiUmum = filter_var($_POST['nilai_umum'] ?? null, FILTER_VALIDATE_INT);

    $hasilKode = uraikanKodePendaftaran($kodePendaftaran);

    if ($kodePendaftaran === '' || $namaPendaftar === '' || $jenisKelamin === '' ||
        $ttl === '' || $asalSekolah === '' || $pekerjaanOrtu === '') {
        $_SESSION['flash_message'] = 'Semua kolom wajib diisi terlebih dahulu.';
        $_SESSION['flash_type'] = 'error';
    } elseif (!$hasilKode) {
        $_SESSION['flash_message'] = 'Format kode pendaftaran salah. Gunakan contoh A2-101-9 (A/B/V, nomor gelombang, 3 angka nomor peserta, dan kode bulan).';
        $_SESSION['flash_type'] = 'error';
    } elseif (!in_array($jenisKelamin, ['Laki-laki', 'Perempuan'], true)) {
        $_SESSION['flash_message'] = 'Silakan pilih jenis kelamin yang tersedia.';
        $_SESSION['flash_type'] = 'error';
    } elseif ($nilaiMatematika === false || $nilaiBahasaInggris === false || $nilaiUmum === false ||
        $nilaiMatematika < 0 || $nilaiMatematika > 100 ||
        $nilaiBahasaInggris < 0 || $nilaiBahasaInggris > 100 ||
        $nilaiUmum < 0 || $nilaiUmum > 100) {
        $_SESSION['flash_message'] = 'Nilai Matematika, Bahasa Inggris, dan Umum harus berupa angka 0 sampai 100.';
        $_SESSION['flash_type'] = 'error';
    } else {
        $kode = $hasilKode['kode'];
        $rataRataAsli = ($nilaiMatematika + $nilaiBahasaInggris + $nilaiUmum) / 3;
        $rataRata = round($rataRataAsli, 2);

        if ($rataRataAsli >= 70) {
            $keterangan = 'Lulus';
        } elseif ($rataRataAsli >= 60) {
            $keterangan = 'Cadangan';
        } else {
            $keterangan = 'Tidak Lulus';
        }

        try {
            $stmt = $conn->prepare(
                'INSERT INTO calon_mhs
                (kode, kode_pendaftaran, nama_pendaftar, jenis_kelamin, ttl, asal_sekolah, pekerjaan_ortu,
                 nilai_matematika, nilai_bahasa_inggris, nilai_umum, rata_rata, keterangan)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param(
                'sssssssiiids',
                $kode,
                $kodePendaftaran,
                $namaPendaftar,
                $jenisKelamin,
                $ttl,
                $asalSekolah,
                $pekerjaanOrtu,
                $nilaiMatematika,
                $nilaiBahasaInggris,
                $nilaiUmum,
                $rataRata,
                $keterangan
            );
            $stmt->execute();
            $stmt->close();

            $_SESSION['flash_message'] = 'Data calon mahasiswa berhasil disimpan ke database.';
            $_SESSION['flash_type'] = 'success';
        } catch (mysqli_sql_exception $e) {
            if ((int)$e->getCode() === 1062) {
                $_SESSION['flash_message'] = 'Kode pendaftaran tersebut sudah digunakan. Gunakan kode yang berbeda.';
            } else {
                $_SESSION['flash_message'] = 'Data gagal disimpan. Pastikan tabel calon_mhs sudah dibuat dari file calon_mhs.sql.';
            }
            $_SESSION['flash_type'] = 'error';
        }
    }

    // Redirect mencegah data tersimpan dua kali ketika halaman di-refresh.
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Mengambil data yang tersimpan untuk ditampilkan pada tabel HTML.
$dataCalon = [];
$jumlahPendaftar = 0;
$jumlahLulus = 0;
$jumlahCadangan = 0;
$jumlahTidakLulus = 0;

if ($conn) {
    try {
        $result = $conn->query('SELECT * FROM calon_mhs ORDER BY id ASC');
        while ($row = $result->fetch_assoc()) {
            $dataCalon[] = $row;
            if ($row['keterangan'] === 'Lulus') {
                $jumlahLulus++;
            } elseif ($row['keterangan'] === 'Cadangan') {
                $jumlahCadangan++;
            } elseif ($row['keterangan'] === 'Tidak Lulus') {
                $jumlahTidakLulus++;
            }
        }
        $result->free();
        $jumlahPendaftar = count($dataCalon);
    } catch (mysqli_sql_exception $e) {
        $connectionError = 'Tabel calon_mhs belum tersedia. Import file calon_mhs.sql terlebih dahulu.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pendaftaran Calon Mahasiswa</title>
    <style>
        :root {
            --navy: #0f1b3d;
            --ink: #0f172a;
            --text: #1e293b;
            --muted: #64748b;
            --line: #e2e8f0;
            --line-soft: #eef2f8;
            --bg: #f1f5fb;
            --card: #ffffff;
            --indigo: #4f46e5;
            --indigo-dark: #4338ca;
            --indigo-soft: #eef2ff;
            --green: #16a34a;
            --amber: #d97706;
            --red: #dc2626;
            --radius: 18px;
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
            --shadow: 0 18px 40px -24px rgba(15, 23, 42, .45);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--text);
            background:
                radial-gradient(900px 480px at 10% -10%, #e0e7ff 0%, transparent 60%),
                radial-gradient(760px 420px at 100% 0%, #dbeafe 0%, transparent 55%),
                var(--bg);
            font-family: "Segoe UI", system-ui, -apple-system, "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            line-height: 1.5;
        }

        /* ---------- HERO ---------- */
        .hero {
            position: relative;
            overflow: hidden;
            padding: 42px 20px 88px;
            color: #fff;
            background: linear-gradient(120deg, #0b1430 0%, #1b2a72 48%, #3b32b0 100%);
        }
        .hero::before, .hero::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .hero::before {
            width: 460px; height: 460px; right: -140px; top: -200px;
            background: radial-gradient(circle, rgba(99, 102, 241, .6), transparent 68%);
        }
        .hero::after {
            width: 340px; height: 340px; left: -100px; bottom: -190px;
            background: radial-gradient(circle, rgba(56, 189, 248, .38), transparent 68%);
        }
        .hero-inner { position: relative; max-width: 1320px; margin: auto; }

        .hero-top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }
        .brand-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: #22d3ee;
            box-shadow: 0 0 0 4px rgba(34, 211, 238, .22);
        }
        .eyebrow {
            margin: 0;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #bfdbfe;
        }
        h1 {
            margin: 0 0 10px;
            font-size: clamp(25px, 3.2vw, 38px);
            line-height: 1.15;
            letter-spacing: -.02em;
        }
        .hero p.lead {
            margin: 0;
            max-width: 620px;
            color: #d5e2fb;
            font-size: 14.5px;
            line-height: 1.65;
        }
        .hero-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
        }
        .hero-chips span {
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .18);
            color: #e6edff;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .02em;
            backdrop-filter: blur(6px);
        }

        /* ---------- LAYOUT ---------- */
        .container {
            width: min(1320px, calc(100% - 32px));
            margin: -46px auto 48px;
        }
        .panel {
            margin-bottom: 24px;
            padding: 26px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--line-soft);
        }
        h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            font-size: 19px;
            letter-spacing: -.01em;
            color: var(--navy);
        }
        h2::before {
            content: "";
            width: 4px; height: 20px;
            border-radius: 99px;
            background: linear-gradient(180deg, #4f46e5, #22d3ee);
        }
        .count-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: var(--indigo-soft);
            color: var(--indigo-dark);
            border: 1px solid #dbe2ff;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
        }

        /* ---------- ALERT ---------- */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 13.5px;
            line-height: 1.55;
            background: #fff;
            box-shadow: var(--shadow);
        }
        .alert::before {
            content: "!";
            flex: 0 0 20px;
            width: 20px; height: 20px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: var(--red);
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            margin-top: 1px;
        }
        .alert.success { color: #14532d; background: #f2fdf6; border: 1px solid #bbf7d0; }
        .alert.success::before { content: "✓"; background: var(--green); }
        .alert.error { color: #7f1d1d; background: #fff5f5; border: 1px solid #fecaca; }

        /* ---------- FORM ---------- */
        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }
        .field { display: flex; flex-direction: column; gap: 7px; min-width: 0; }
        .field label {
            font-size: 12.5px;
            font-weight: 700;
            color: #334155;
            letter-spacing: .01em;
        }
        .required { color: var(--red); }

        input, select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d7dfeb;
            border-radius: 10px;
            background: #f8fafd;
            color: var(--ink);
            font: inherit;
            font-size: 14px;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }
        input::placeholder { color: #9aa8bd; }
        input:hover, select:hover { background: #fff; border-color: #c3cee0; }
        input:focus, select:focus {
            outline: none;
            background: #fff;
            border-color: var(--indigo);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, .13);
        }
        input[type=number] { appearance: textfield; -moz-appearance: textfield; }
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        select { cursor: pointer; }

        .section-label {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 8px 0 0;
            padding-bottom: 10px;
            border-bottom: 1px dashed var(--line);
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #475569;
        }
        .section-label::before {
            content: "";
            width: 6px; height: 18px;
            border-radius: 99px;
            background: linear-gradient(180deg, var(--indigo), #22d3ee);
        }

        .hint { margin: 0; color: var(--muted); font-size: 12px; line-height: 1.5; }

        .code-preview {
            display: flex;
            align-items: center;
            min-height: 44px;
            padding: 10px 14px;
            border: 1px dashed #a5b4fc;
            border-radius: 10px;
            background: linear-gradient(180deg, #f5f7ff, #eef2ff);
            color: #3730a3;
            font-size: 12.5px;
            line-height: 1.5;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--line-soft);
        }
        button {
            border: 0;
            border-radius: 10px;
            padding: 12px 24px;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            box-shadow: 0 10px 22px -10px rgba(79, 70, 229, .9);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px -10px rgba(79, 70, 229, .95);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-secondary { color: #334155; background: #eef2f8; }
        .btn-secondary:hover { background: #e2e8f0; }

        /* ---------- STATS ---------- */
        .summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }
        .stat {
            position: relative;
            overflow: hidden;
            padding: 17px 18px 15px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: linear-gradient(180deg, #fbfdff, #f6f9fe);
            box-shadow: var(--shadow-sm);
        }
        .stat::before {
            content: "";
            position: absolute;
            top: 0; bottom: 0; left: 0;
            width: 4px;
            background: var(--accent, var(--indigo));
        }
        .stat:nth-child(1) { --accent: #4f46e5; }
        .stat:nth-child(2) { --accent: #16a34a; }
        .stat:nth-child(3) { --accent: #d97706; }
        .stat:nth-child(4) { --accent: #dc2626; }
        .stat span {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .03em;
            text-transform: uppercase;
        }
        .stat strong {
            font-size: 27px;
            line-height: 1.1;
            color: var(--navy);
            letter-spacing: -.02em;
        }

        /* ---------- TABLE ---------- */
        .table-wrap {
            overflow: auto;
            max-height: min(68vh, 620px);
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--shadow-sm);
        }
        .table-wrap::-webkit-scrollbar { width: 10px; height: 10px; }
        .table-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; border: 2px solid #fff; }
        .table-wrap::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 12.5px;
            white-space: nowrap;
        }
        thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            padding: 13px 12px;
            background: #eef2fb;
            border-bottom: 1px solid var(--line);
            color: #41506b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            text-align: left;
        }
        tbody td {
            padding: 12px;
            border-bottom: 1px solid var(--line-soft);
            vertical-align: middle;
        }
        tbody tr:nth-child(even) td { background: #fbfdff; }
        tbody tr:hover td { background: #f2f7ff; }
        tbody tr:last-child td { border-bottom: 0; }
        td strong { color: var(--navy); font-weight: 800; letter-spacing: .01em; }

        .empty {
            padding: 34px 16px !important;
            color: var(--muted);
            text-align: center !important;
            font-size: 13px;
        }

        tfoot td {
            padding: 12px;
            background: #f8fafc;
            border-top: 1px solid var(--line);
            font-weight: 800;
            font-size: 11.5px;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #475569;
        }
        tfoot td:first-child { text-align: right; }
        tfoot td:last-child { color: var(--navy); }

        .status {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .02em;
        }
        .status.lulus { color: #14532d; background: #dcfce7; box-shadow: inset 0 0 0 1px #bbf7d0; }
        .status.cadangan { color: #78350f; background: #fef3c7; box-shadow: inset 0 0 0 1px #fde68a; }
        .status.tidak-lulus { color: #7f1d1d; background: #fee2e2; box-shadow: inset 0 0 0 1px #fecaca; }

        /* ---------- NOTES ---------- */
        .notes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 20px;
        }
        .note {
            padding: 17px 19px;
            background: linear-gradient(180deg, #fbfdff, #f5f9ff);
            border: 1px solid var(--line);
            border-radius: 12px;
            font-size: 12.5px;
            line-height: 1.7;
            color: #334155;
        }
        .note strong { color: var(--navy); }
        .note ul { margin: 9px 0 0; padding-left: 18px; }
        .note li { margin-bottom: 3px; }
        .note li::marker { color: var(--indigo); }

        footer {
            padding: 10px 0 0;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            letter-spacing: .02em;
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 900px) {
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 600px) {
            .hero { padding: 32px 16px 76px; }
            .container { width: calc(100% - 20px); margin-top: -40px; }
            .panel { padding: 18px; border-radius: 14px; }
            .grid, .notes { grid-template-columns: 1fr; }
            .section-label { grid-column: auto; }
            .actions { flex-direction: column-reverse; }
            .actions button { width: 100%; }
            .stat strong { font-size: 23px; }
        }
    </style>
</head>
<body>
<header class="hero">
    <div class="hero-inner">
        <div class="hero-top">
            <span class="brand-dot"></span>
            <p class="eyebrow">Pemrograman Web II · Universitas Pamulang</p>
        </div>
        <h1>Pendaftaran Calon Mahasiswa Baru</h1>
        <p class="lead">Form input, proses penyimpanan PHP ke MySQL, dan laporan data calon mahasiswa.</p>
        <div class="hero-chips">
            <span>Input Data</span>
            <span>Validasi Otomatis</span>
            <span>Laporan Hasil Tes</span>
        </div>
    </div>
</header>

<main class="container">
    <?php if ($connectionError !== ''): ?>
        <div class="alert error"><?= e($connectionError) ?></div>
    <?php endif; ?>
    <?php if ($flashMessage !== ''): ?>
        <div class="alert <?= $flashType === 'error' ? 'error' : 'success' ?>"><?= e($flashMessage) ?></div>
    <?php endif; ?>

    <section class="panel">
        <div class="panel-heading">
            <h2>Input Pendaftaran</h2>
            <span class="count-pill">Jumlah data: <?= (int)$jumlahPendaftar ?></span>
        </div>
        <form method="post" action="" id="formPendaftaran">
            <div class="grid">
                <div class="section-label">Identitas pendaftar</div>
                <div class="field">
                    <label for="kode_pendaftaran">Kode Pendaftaran <span class="required">*</span></label>
                    <input type="text" id="kode_pendaftaran" name="kode_pendaftaran" maxlength="10" placeholder="Contoh: A2-101-9" pattern="[ABVabv][1-9]-[0-9]{3}-[0-9ONDond]" title="Contoh format yang benar: A2-101-9" required>
                    <p class="hint">Format: kode tempat dan gelombang - nomor peserta - kode bulan.</p>
                </div>
                <div class="field">
                    <label for="nama_pendaftar">Nama Pendaftar <span class="required">*</span></label>
                    <input type="text" id="nama_pendaftar" name="nama_pendaftar" maxlength="100" placeholder="Nama lengkap" required>
                </div>
                <div class="field">
                    <label for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">-- Pilih jenis kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="field">
                    <label for="ttl">TTL <span class="required">*</span></label>
                    <input type="text" id="ttl" name="ttl" maxlength="100" placeholder="Tempat, tanggal lahir" required>
                </div>
                <div class="field">
                    <label for="asal_sekolah">Asal Sekolah <span class="required">*</span></label>
                    <input type="text" id="asal_sekolah" name="asal_sekolah" maxlength="120" placeholder="Nama sekolah asal" required>
                </div>
                <div class="field">
                    <label for="pekerjaan_ortu">Pekerjaan Orang Tua <span class="required">*</span></label>
                    <input type="text" id="pekerjaan_ortu" name="pekerjaan_ortu" maxlength="100" placeholder="Pekerjaan orang tua" required>
                </div>
                <div class="field">
                    <label>Hasil pembacaan kode</label>
                    <div class="code-preview" id="kodePreview">Detail tempat tes, gelombang, dan bulan muncul di sini.</div>
                </div>

                <div class="section-label">Nilai tes (0–100)</div>
                <div class="field">
                    <label for="nilai_matematika">Matematika <span class="required">*</span></label>
                    <input type="number" id="nilai_matematika" name="nilai_matematika" min="0" max="100" step="1" placeholder="0 - 100" required>
                </div>
                <div class="field">
                    <label for="nilai_bahasa_inggris">Bahasa Inggris <span class="required">*</span></label>
                    <input type="number" id="nilai_bahasa_inggris" name="nilai_bahasa_inggris" min="0" max="100" step="1" placeholder="0 - 100" required>
                </div>
                <div class="field">
                    <label for="nilai_umum">Umum <span class="required">*</span></label>
                    <input type="number" id="nilai_umum" name="nilai_umum" min="0" max="100" step="1" placeholder="0 - 100" required>
                </div>
                <div class="field">
                    <label>Pratinjau rata-rata</label>
                    <div class="code-preview" id="rataPreview">Rata-rata akan dihitung otomatis.</div>
                </div>
            </div>
            <div class="actions">
                <button class="btn-secondary" type="reset">Batal</button>
                <button class="btn-primary" type="submit" name="simpan" value="1">Simpan Data</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <h2>Tabel Data Calon Mahasiswa</h2>
            <span class="count-pill"><?= (int)$jumlahPendaftar ?> pendaftar tersimpan</span>
        </div>

        <div class="summary">
            <div class="stat"><span>Jumlah Pendaftar</span><strong><?= (int)$jumlahPendaftar ?></strong></div>
            <div class="stat"><span>Peserta Lulus</span><strong><?= (int)$jumlahLulus ?></strong></div>
            <div class="stat"><span>Peserta Cadangan</span><strong><?= (int)$jumlahCadangan ?></strong></div>
            <div class="stat"><span>Tidak Lulus</span><strong><?= (int)$jumlahTidakLulus ?></strong></div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Kode Pendf.</th>
                        <th>Nama Pendft.</th>
                        <th>Jns Kel</th>
                        <th>TTL</th>
                        <th>Asal Sekolah</th>
                        <th>Pek. Ortu</th>
                        <th>Mat</th>
                        <th>B. Ingr</th>
                        <th>Umum</th>
                        <th>Rata-Rata</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($dataCalon) === 0): ?>
                    <tr><td class="empty" colspan="13">Belum ada data. Silakan isi formulir dan klik Simpan Data.</td></tr>
                <?php else: ?>
                    <?php foreach ($dataCalon as $i => $row): ?>
                        <?php $statusClass = $row['keterangan'] === 'Lulus' ? 'lulus' : ($row['keterangan'] === 'Cadangan' ? 'cadangan' : 'tidak-lulus'); ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($row['kode']) ?></td>
                            <td><strong><?= e($row['kode_pendaftaran']) ?></strong></td>
                            <td><?= e($row['nama_pendaftar']) ?></td>
                            <td><?= e($row['jenis_kelamin']) ?></td>
                            <td><?= e($row['ttl']) ?></td>
                            <td><?= e($row['asal_sekolah']) ?></td>
                            <td><?= e($row['pekerjaan_ortu']) ?></td>
                            <td><?= (int)$row['nilai_matematika'] ?></td>
                            <td><?= (int)$row['nilai_bahasa_inggris'] ?></td>
                            <td><?= (int)$row['nilai_umum'] ?></td>
                            <td><?= number_format((float)$row['rata_rata'], 2, ',', '.') ?></td>
                            <td><span class="status <?= e($statusClass) ?>"><?= e($row['keterangan']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="11">JUMLAH PENDAFTAR</td><td colspan="2"><?= (int)$jumlahPendaftar ?></td></tr>
                    <tr><td colspan="11">JUMLAH PESERTA LULUS</td><td colspan="2"><?= (int)$jumlahLulus ?></td></tr>
                    <tr><td colspan="11">JUMLAH TIDAK PESERTA LULUS</td><td colspan="2"><?= (int)$jumlahTidakLulus ?></td></tr>
                </tfoot>
            </table>
        </div>

        <div class="notes">
            <div class="note">
                <strong>Keterangan kode pendaftaran</strong>
                <ul>
                    <li>Karakter awal: A = Gedung A, B = Gedung B, V = Viktor.</li>
                    <li>Karakter kedua menunjukkan gelombang tes.</li>
                    <li>Contoh <strong>A2-101-9</strong>: Gedung A, Gelombang 2, nomor peserta 101, bulan September.</li>
                    <li>Kode bulan satu karakter: 1–9 = Januari–September, 0 = Oktober, N = November, D = Desember.</li>
                </ul>
            </div>
            <div class="note">
                <strong>Ketentuan hasil tes</strong>
                <ul>
                    <li>Rata-rata = (Matematika + Bahasa Inggris + Umum) ÷ 3.</li>
                    <li>Rata-rata 70 atau lebih: <strong>Lulus</strong>.</li>
                    <li>Rata-rata 60 sampai kurang dari 70: <strong>Cadangan</strong>.</li>
                    <li>Rata-rata kurang dari 60: <strong>Tidak Lulus</strong>.</li>
                </ul>
            </div>
        </div>
    </section>
    <footer>Tugas UTS · Pemrograman Web II · Data tersimpan pada database MySQL</footer>
</main>

<script>
const inputKode = document.getElementById('kode_pendaftaran');
const kodePreview = document.getElementById('kodePreview');
const skorInputs = [
    document.getElementById('nilai_matematika'),
    document.getElementById('nilai_bahasa_inggris'),
    document.getElementById('nilai_umum')
];
const rataPreview = document.getElementById('rataPreview');

function updateKodePreview() {
    const kode = inputKode.value.trim().toUpperCase();
    const match = kode.match(/^([ABV])([1-9])-(\d{3})-([0-9OND])$/);
    if (!match) {
        kodePreview.textContent = 'Contoh format: A2-101-9';
        return;
    }
    const tempat = { A: 'Gedung A', B: 'Gedung B', V: 'Viktor' }[match[1]];
    const bulan = { '1': 'Januari', '2': 'Februari', '3': 'Maret', '4': 'April', '5': 'Mei', '6': 'Juni', '7': 'Juli', '8': 'Agustus', '9': 'September', '0': 'Oktober', N: 'November', D: 'Desember' }[match[4]];
    kodePreview.textContent = `${tempat} · Gelombang ${match[2]} · Nomor ${match[3]} · ${bulan}`;
}

function updateRataPreview() {
    const values = skorInputs.map(input => input.value.trim());
    if (values.some(value => value === '')) {
        rataPreview.textContent = 'Isi ketiga nilai untuk melihat rata-rata.';
        return;
    }
    const nums = values.map(Number);
    if (nums.some(value => !Number.isFinite(value) || value < 0 || value > 100)) {
        rataPreview.textContent = 'Nilai harus berada di antara 0 dan 100.';
        return;
    }
    const rata = nums.reduce((total, value) => total + value, 0) / nums.length;
    const hasil = rata >= 70 ? 'Lulus' : (rata >= 60 ? 'Cadangan' : 'Tidak Lulus');
    rataPreview.textContent = `Rata-rata: ${rata.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} · ${hasil}`;
}

inputKode.addEventListener('input', updateKodePreview);
skorInputs.forEach(input => input.addEventListener('input', updateRataPreview));
document.getElementById('formPendaftaran').addEventListener('reset', () => {
    setTimeout(() => {
        updateKodePreview();
        updateRataPreview();
    }, 0);
});
</script>
</body>
</html>