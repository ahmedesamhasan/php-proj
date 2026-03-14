<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products.php');
}

verifyCsrfToken();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    setFlashMessage('danger', 'Invalid product id.');
    redirect('products.php');
}

$productImage = findProductImageById($conn, $id);

if (!$productImage) {
    setFlashMessage('danger', 'Product not found.');
    redirect('products.php');
}

$deleteProduct = mysqli_prepare($conn, 'DELETE FROM products WHERE id = ?');
mysqli_stmt_bind_param($deleteProduct, 'i', $id);
mysqli_stmt_execute($deleteProduct);
mysqli_stmt_close($deleteProduct);

deleteProductImage($productImage);

setFlashMessage('success', 'Product deleted successfully.');
redirect('products.php');
