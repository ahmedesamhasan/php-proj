<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireGuest();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

verifyCsrfToken();

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

setOldInput([
    'email' => $email,
]);

if ($email === '' || $password === '') {
    setFlashMessage('danger', 'Email and password are required.');
    redirect('login.php');
}

$admin = findAdminByEmail($conn, $email);

if (!$admin || !password_verify($password, (string) $admin['password'])) {
    setFlashMessage('danger', 'Invalid login details.');
    redirect('login.php');
}

clearOldInput();
loginAdmin($admin);
setFlashMessage('success', 'Welcome back, ' . $admin['name'] . '.');
redirect('dashboard.php');
