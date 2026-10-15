<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/koneksi.php';

const DAFTAR_KOTA = ['Bandung', 'Jakarta', 'Surabaya', 'Yogyakarta'];
const DAFTAR_HOBI = ['Membaca', 'Olahraga', 'Memasak'];

/* ================= BAHASA (disimpan di $_SESSION) ================= */
if (isset($_GET['lang']) && in_array($_GET['lang'], ['id', 'en'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'id';
}

$TEKS = [
    'id' => [
        'bahasa' => 'Bahasa', 'halo' => 'Halo', 'keluar' => 'Keluar',
        'login' => 'Login', 'username' => 'Username', 'password' => 'Kata Sandi', 'masuk' => 'Masuk',
        'data_pendaftar' => 'Data Pendaftar', 'tambah' => 'Tambah Data', 'ubah' => 'Ubah',
        'ubah_data' => 'Ubah Data', 'hapus' => 'Hapus', 'simpan' => 'Simpan', 'batal' => 'Batal',
        'no' => 'No', 'nama' => 'Nama Lengkap', 'jk' => 'Jenis Kelamin', 'kota' => 'Kota Asal',
        'hobi' => 'Hobi', 'tentang' => 'Tentang Saya', 'aksi' => 'Aksi',
        'pilih' => '-- Pilih --', 'kosong' => 'Belum ada data.',
        'konfirmasi' => 'Yakin ingin menghapus data ini?',
        'jk_Laki-laki' => 'Laki-laki', 'jk_Perempuan' => 'Perempuan',
        'hobi_Membaca' => 'Membaca', 'hobi_Olahraga' => 'Olahraga', 'hobi_Memasak' => 'Memasak',
        'msg_tambah' => 'Data berhasil ditambahkan.',
        'msg_ubah' => 'Data berhasil diperbarui.',
        'msg_hapus' => 'Data berhasil dihapus.',
        'msg_gagal' => 'Terjadi kesalahan saat menyimpan data.',
        'msg_tidak_ada' => 'Data tidak ditemukan.',
        'err_nama_kosong' => 'Nama lengkap wajib diisi.',
        'err_nama_panjang' => 'Nama harus 3 sampai 100 karakter.',
        'err_nama_format' => 'Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
        'err_jk_kosong' => 'Pilih jenis kelamin.',
        'err_kota_kosong' => 'Pilih kota asal.',
        'err_pilihan_invalid' => 'Pilihan tidak valid.',
        'err_tentang_panjang' => 'Maksimal 500 karakter.',
        'err_login_kosong' => 'Username dan kata sandi wajib diisi.',
        'err_login_salah' => 'Username atau kata sandi salah.',
    ],
    'en' => [
        'bahasa' => 'Language', 'halo' => 'Hello', 'keluar' => 'Logout',
        'login' => 'Login', 'username' => 'Username', 'password' => 'Password', 'masuk' => 'Sign In',
        'data_pendaftar' => 'Registrant Data', 'tambah' => 'Add Data', 'ubah' => 'Edit',
        'ubah_data' => 'Edit Data', 'hapus' => 'Delete', 'simpan' => 'Save', 'batal' => 'Cancel',
        'no' => 'No', 'nama' => 'Full Name', 'jk' => 'Gender', 'kota' => 'Hometown',
        'hobi' => 'Hobbies', 'tentang' => 'About Me', 'aksi' => 'Action',
        'pilih' => '-- Select --', 'kosong' => 'No data yet.',
        'konfirmasi' => 'Are you sure you want to delete this data?',
        'jk_Laki-laki' => 'Male', 'jk_Perempuan' => 'Female',
        'hobi_Membaca' => 'Reading', 'hobi_Olahraga' => 'Sports', 'hobi_Memasak' => 'Cooking',
        'msg_tambah' => 'Data added successfully.',
        'msg_ubah' => 'Data updated successfully.',
        'msg_hapus' => 'Data deleted successfully.',
        'msg_gagal' => 'An error occurred while saving the data.',
        'msg_tidak_ada' => 'Data not found.',
        'err_nama_kosong' => 'Full name is required.',
        'err_nama_panjang' => 'Name must be 3 to 100 characters.',
        'err_nama_format' => 'Name may only contain letters, spaces, dots, apostrophes, and hyphens.',
        'err_jk_kosong' => 'Please select a gender.',
        'err_kota_kosong' => 'Please select a hometown.',
        'err_pilihan_invalid' => 'Invalid choice.',
        'err_tentang_panjang' => 'Maximum 500 characters.',
        'err_login_kosong' => 'Username and password are required.',
        'err_login_salah' => 'Wrong username or password.',
    ],
];

function t(string $kunci): string
{
    global $TEKS;
    return $TEKS[$_SESSION['lang']][$kunci] ?? $kunci;
}

/* ================= SANITASI ================= */
// Untuk output ke HTML (double_encode=false supaya data yang sudah disanitasi tidak di-encode dua kali)
function e($nilai): string
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8', false);
}

// Untuk input sebelum disimpan ke database
function bersih($nilai): string
{
    return htmlspecialchars(trim((string) $nilai), ENT_QUOTES, 'UTF-8');
}

/* ================= VALIDASI ================= */
function validasi(array $in): array
{
    $ambil = fn(string $k): string => (isset($in[$k]) && is_string($in[$k])) ? trim($in[$k]) : '';

    $nama    = $ambil('nama_lengkap');
    $jk      = $ambil('jenis_kelamin');
    $kota    = $ambil('kota_asal');
    $tentang = $ambil('tentang_saya');
    $hobi    = (isset($in['hobi']) && is_array($in['hobi'])) ? $in['hobi'] : [];
    $error   = [];

    // Nama Lengkap -> if / elseif / else
    if ($nama === '') {
        $error['nama_lengkap'] = t('err_nama_kosong');
    } elseif (mb_strlen($nama) < 3 || mb_strlen($nama) > 100) {
        $error['nama_lengkap'] = t('err_nama_panjang');
    } elseif (!preg_match("/^[\p{L} .'\-]+$/u", $nama)) {
        $error['nama_lengkap'] = t('err_nama_format');
    }

    // Jenis Kelamin -> switch
    switch ($jk) {
        case 'Laki-laki':
        case 'Perempuan':
            break;
        case '':
            $error['jenis_kelamin'] = t('err_jk_kosong');
            break;
        default:
            $error['jenis_kelamin'] = t('err_pilihan_invalid');
    }

    // Kota Asal -> switch
    switch ($kota) {
        case '':
            $error['kota_asal'] = t('err_kota_kosong');
            break;
        default:
            if (!in_array($kota, DAFTAR_KOTA, true)) {
                $error['kota_asal'] = t('err_pilihan_invalid');
            }
    }

    // Hobi (opsional) -> setiap nilai harus ada di daftar
    $hobi_valid = [];
    foreach ($hobi as $h) {
        if (is_string($h) && in_array($h, DAFTAR_HOBI, true)) {
            $hobi_valid[] = $h;
        } else {
            $error['hobi'] = t('err_pilihan_invalid');
        }
    }

    // Tentang Saya (opsional)
    if (mb_strlen($tentang) > 500) {
        $error['tentang_saya'] = t('err_tentang_panjang');
    }

    $data = [
        'nama_lengkap'  => bersih($nama),
        'jenis_kelamin' => bersih($jk),
        'kota_asal'     => bersih($kota),
        'hobi'          => array_values(array_unique($hobi_valid)),
        'tentang_saya'  => bersih($tentang),
    ];

    return [$data, $error];
}

/* ================= SESSION LOGIN ================= */
function wajib_login(): void
{
    if (empty($_SESSION['login'])) {
        header('Location: login.php');
        exit;
    }
}

/* ================= TAMPILAN ================= */
function link_bahasa(string $kode): string
{
    return basename($_SERVER['PHP_SELF']) . '?' . http_build_query(array_merge($_GET, ['lang' => $kode]));
}

function tampil_error(array $error, string $kunci): void
{
    if (isset($error[$kunci])) {
        echo '<span class="err">' . e($error[$kunci]) . '</span>';
    }
}

function mulai_halaman(string $judul): void
{
    $lang  = $_SESSION['lang'];
    $masuk = !empty($_SESSION['login']);
    ?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f8; color: #222; }
        header { background: #1f4e79; color: #fff; padding: 12px 24px; }
        header h1 { margin: 0 0 6px; font-size: 20px; }
        nav { font-size: 14px; }
        nav a { color: #fff; }
        nav a.aktif { font-weight: bold; text-decoration: none; }
        main { max-width: 900px; margin: 20px auto; background: #fff; padding: 20px 24px; border-radius: 6px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #e8eef5; }
        .err { color: #c0392b; font-size: 13px; display: block; }
        .ok { background: #e3f4e8; border: 1px solid #9ad0a8; padding: 8px 12px; margin-bottom: 12px; }
        .gagal { background: #fbe9e7; border: 1px solid #e6a79f; padding: 8px 12px; margin-bottom: 12px; }
        .btn { display: inline-block; padding: 6px 12px; background: #1f4e79; color: #fff; border: 0;
               border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; }
        .btn.merah { background: #c0392b; }
        .btn.abu { background: #777; }
        p.baris { margin: 14px 0; }
        input[type=text], input[type=password], select, textarea { padding: 6px; width: 280px; max-width: 100%; }
        form.inline { display: inline; }
    </style>
</head>
<body>
<header>
    <h1>tugas PENERAPAN TEKNOLOGI INTERNET-10122329</h1>
    <nav>
        <?= e(t('bahasa')) ?>:
        <a href="<?= e(link_bahasa('id')) ?>" class="<?= $lang === 'id' ? 'aktif' : '' ?>">ID</a> |
        <a href="<?= e(link_bahasa('en')) ?>" class="<?= $lang === 'en' ? 'aktif' : '' ?>">EN</a>
        <?php if ($masuk): ?>
            &nbsp;|&nbsp; <?= e(t('halo')) ?>, <?= e($_SESSION['username']) ?>
            &nbsp;|&nbsp; <a href="logout.php"><?= e(t('keluar')) ?></a>
        <?php endif; ?>
    </nav>
</header>
<main>
<h2><?= e($judul) ?></h2>
<?php
}

function akhiri_halaman(): void
{
    echo "</main>\n</body>\n</html>\n";
}