<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update'])) {
    redirect('products.php');
}

verifyCsrfToken();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name = trim((string) ($_POST['name'] ?? ''));
$price = trim((string) ($_POST['price'] ?? ''));
$oldImage = trim((string) ($_POST['old_image'] ?? ''));
$categoryId = (int) ($_POST['category_id'] ?? 0);

if (!$id) {
    setFlashMessage('danger', 'Invalid product id.');
    redirect('products.php');
}

$errors = validateProductData($name, $price, $categoryId);

if (!categoryExists($conn, $categoryId)) {
    $errors[] = 'Selected category does not exist.';
}

if ($errors !== []) {
    setFlashMessage('danger', implode(' ', array_unique($errors)));
    redirect('update.php?id=' . $id);
}

try {
    $imagePath = uploadProductImage($_FILES['image'] ?? [], $oldImage);

    $updateProduct = mysqli_prepare($conn, 'UPDATE products SET category_id = ?, name = ?, price = ?, image = ? WHERE id = ?');
    $formattedPrice = formatPrice($price);

    mysqli_stmt_bind_param($updateProduct, 'isssi', $categoryId, $name, $formattedPrice, $imagePath, $id);
    mysqli_stmt_execute($updateProduct);
    mysqli_stmt_close($updateProduct);

    setFlashMessage('success', 'Product updated successfully.');
    redirect('products.php');
} catch (Throwable $exception) {
    setFlashMessage('danger', $exception->getMessage());
    redirect('update.php?id=' . $id);
}
