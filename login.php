<?php
require 'helper.php';

if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = (isset($_POST['username']) && is_string($_POST['username'])) ? trim($_POST['username']) : '';
    $password = (isset($_POST['password']) && is_string($_POST['password'])) ? $_POST['password'] : '';

    if ($username === '' || $password === '') {
        $error = t('err_login_kosong');
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT username, password FROM users WHERE username = ?');
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['login']    = true;
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        }
        $error = t('err_login_salah');
    }
}

mulai_halaman(t('login'));
?>
<?php if ($error !== ''): ?>
    <div class="gagal"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST">
    <p class="baris">
        <label for="username"><?= e(t('username')) ?>:</label><br>
        <input type="text" id="username" name="username" value="<?= e($username) ?>">
    </p>
    <p class="baris">
        <label for="password"><?= e(t('password')) ?>:</label><br>
        <input type="password" id="password" name="password">
    </p>

    <p class="baris" style="background:#fff8e1;border:1px solid #f0d98a;padding:8px 12px;font-size:14px;">
        <?= e(t('username')) ?>: <b>admin</b><br>
        <?= e(t('password')) ?>: <b>admin123</b>
    </p>

    <button type="submit" class="btn"><?= e(t('masuk')) ?></button>
</form>
<?php akhiri_halaman(); ?>