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

if (!$id) {
    setFlashMessage('danger', 'Invalid category id.');
    redirect('categories.php');
}

$category = findCategoryById($conn, $id);
if (!$category) {
    setFlashMessage('danger', 'Category not found.');
    redirect('categories.php');
}

if (countProductsInCategory($conn, $id) > 0) {
    setFlashMessage('warning', 'You cannot delete a category that still has products.');
    redirect('categories.php');
}

deleteCategoryById($conn, $id);
setFlashMessage('success', 'Category deleted successfully.');
redirect('categories.php');
