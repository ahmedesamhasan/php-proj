<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('categories.php');
}

verifyCsrfToken();

$name = trim((string) ($_POST['name'] ?? ''));
setOldInput([
    'category_name' => $name,
]);

$errors = validateCategoryName($name);
if ($errors !== []) {
    setFlashMessage('danger', implode(' ', $errors));
    redirect('categories.php');
}

if (categoryNameExists($conn, $name)) {
    setFlashMessage('warning', 'This category already exists.');
    redirect('categories.php');
}

createCategory($conn, $name);

clearOldInput();
setFlashMessage('success', 'Category added successfully.');
redirect('categories.php');
