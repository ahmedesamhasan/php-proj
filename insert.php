<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['upload'])) {
    redirect('index.php');
}

verifyCsrfToken();

$name = trim((string) ($_POST['name'] ?? ''));
$price = trim((string) ($_POST['price'] ?? ''));
$categoryId = (int) ($_POST['category_id'] ?? 0);

setOldInput([
    'name' => $name,
    'price' => $price,
    'category_id' => (string) $categoryId,
]);

$errors = validateProductData($name, $price, $categoryId);

if (!categoryExists($conn, $categoryId)) {
    $errors[] = 'Selected category does not exist.';
}

if ($errors !== []) {
    setFlashMessage('danger', implode(' ', array_unique($errors)));
    redirect('index.php');
}

try {
    $imagePath = uploadProductImage($_FILES['image'] ?? []);

    if ($imagePath === null) {
        throw new RuntimeException('Please choose an image.');
    }

    $insertProduct = mysqli_prepare($conn, 'INSERT INTO products (category_id, name, price, image) VALUES (?, ?, ?, ?)');
    $formattedPrice = formatPrice($price);

    mysqli_stmt_bind_param($insertProduct, 'isss', $categoryId, $name, $formattedPrice, $imagePath);
    mysqli_stmt_execute($insertProduct);
    mysqli_stmt_close($insertProduct);

    clearOldInput();
    setFlashMessage('success', 'Product added successfully.');
    redirect('products.php');
} catch (Throwable $exception) {
    setFlashMessage('danger', $exception->getMessage());
    redirect('index.php');
}
