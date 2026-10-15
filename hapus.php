<?php
require 'helper.php';
wajib_login();

// DELETE (hanya lewat POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($id !== false) {
        $stmt = mysqli_prepare($conn, 'DELETE FROM pendaftar WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION['flash'] = 'msg_hapus';
    }
}

header('Location: index.php');
exit;