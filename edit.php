<?php
require 'helper.php';
wajib_login();

// Ambil id unik dari URL dan pastikan integer positif
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    header('Location: index.php');
    exit;
}

// Ambil data lama
$stmt = mysqli_prepare($conn, 'SELECT * FROM pendaftar WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) {
    $_SESSION['flash'] = 'msg_tidak_ada';
    header('Location: index.php');
    exit;
}

$data = [
    'nama_lengkap'  => $row['nama_lengkap'],
    'jenis_kelamin' => $row['jenis_kelamin'],
    'kota_asal'     => $row['kota_asal'],
    'hobi'          => $row['hobi'] ? explode(',', $row['hobi']) : [],
    'tentang_saya'  => $row['tentang_saya'],
];
$error = [];

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$data, $error] = validasi($_POST);

    if (empty($error)) {
        $hobi = $data['hobi'] ? implode(',', $data['hobi']) : null;
        try {
            $stmt = mysqli_prepare(
                $conn,
                'UPDATE pendaftar
                 SET nama_lengkap = ?, jenis_kelamin = ?, kota_asal = ?, hobi = ?, tentang_saya = ?
                 WHERE id = ?'
            );
            mysqli_stmt_bind_param(
                $stmt, 'sssssi',
                $data['nama_lengkap'], $data['jenis_kelamin'], $data['kota_asal'], $hobi, $data['tentang_saya'], $id
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $_SESSION['flash'] = 'msg_ubah';
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $ex) {
            $error['umum'] = t('msg_gagal');
        }
    }
}

mulai_halaman(t('ubah_data'));
include '_form.php';
akhiri_halaman();