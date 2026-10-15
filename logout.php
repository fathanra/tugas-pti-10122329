<?php
require 'helper.php';

// Hapus status login saja; pilihan bahasa tetap tersimpan di session
unset($_SESSION['login'], $_SESSION['username']);
session_regenerate_id(true);

header('Location: login.php');
exit;