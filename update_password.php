<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('change_password.php');
}

verifyCsrfToken();

$currentPassword = (string) ($_POST['current_password'] ?? '');
$newPassword = (string) ($_POST['new_password'] ?? '');
$confirmPassword = (string) ($_POST['confirm_password'] ?? '');

$errors = validatePasswordChange($currentPassword, $newPassword, $confirmPassword);

if (!verifyAdminCurrentPassword($conn, currentAdminId(), $currentPassword)) {
    $errors[] = 'Current password is incorrect.';
}

if ($errors !== []) {
    setFlashMessage('danger', implode(' ', array_unique($errors)));
    redirect('change_password.php');
}

updateAdminPassword($conn, currentAdminId(), $newPassword);
setFlashMessage('success', 'Password updated successfully.');
redirect('dashboard.php');
