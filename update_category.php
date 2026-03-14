<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('categories.php');
}

verifyCsrfToken();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name = trim((string) ($_POST['name'] ?? ''));
setOldInput(['category_name' => $name]);

if (!$id) {
    setFlashMessage('danger', 'Invalid category id.');
    redirect('categories.php');
}

$errors = validateCategoryName($name);
if (!findCategoryById($conn, $id)) {
    $errors[] = 'Category not found.';
}

if (categoryNameExists($conn, $name, $id)) {
    $errors[] = 'This category name already exists.';
}

if ($errors !== []) {
    setFlashMessage('danger', implode(' ', array_unique($errors)));
    redirect('edit_category.php?id=' . $id);
}

updateCategoryName($conn, $id, $name);
clearOldInput();
setFlashMessage('success', 'Category updated successfully.');
redirect('categories.php');
