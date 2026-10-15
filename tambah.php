<?php
require 'helper.php';
wajib_login();

$data  = ['nama_lengkap' => '', 'jenis_kelamin' => '', 'kota_asal' => '', 'hobi' => [], 'tentang_saya' => ''];
$error = [];

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$data, $error] = validasi($_POST);

    if (empty($error)) {
        $hobi = $data['hobi'] ? implode(',', $data['hobi']) : null;
        try {
            $stmt = mysqli_prepare(
                $conn,
                'INSERT INTO pendaftar (nama_lengkap, jenis_kelamin, kota_asal, hobi, tentang_saya)
                 VALUES (?, ?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param(
                $stmt, 'sssss',
                $data['nama_lengkap'], $data['jenis_kelamin'], $data['kota_asal'], $hobi, $data['tentang_saya']
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $_SESSION['flash'] = 'msg_tambah';
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $ex) {
            $error['umum'] = t('msg_gagal');
        }
    }
}

mulai_halaman(t('tambah'));
include '_form.php';
akhiri_halaman();