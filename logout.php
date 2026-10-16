<?php
require_once __DIR__ . '/../helpers/auth.php';
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;