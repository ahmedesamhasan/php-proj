<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products.php');
}

verifyCsrfToken();
logoutAdmin();
setFlashMessage('success', 'You have been logged out.');
redirect('login.php');
