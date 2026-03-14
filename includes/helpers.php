<?php

declare(strict_types=1);

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/CategoryService.php';
require_once __DIR__ . '/../services/DashboardService.php';
require_once __DIR__ . '/../services/ImageService.php';
require_once __DIR__ . '/../services/ProductService.php';

function escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function setFlashMessage(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlashMessage(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return is_array($flash) ? $flash : null;
}

function oldInput(string $key, string $default = ''): string
{
    return isset($_SESSION['old_input'][$key]) ? (string) $_SESSION['old_input'][$key] : $default;
}

function setOldInput(array $input): void
{
    $_SESSION['old_input'] = $input;
}

function clearOldInput(): void
{
    unset($_SESSION['old_input']);
}

function deleteProductImage(?string $imagePath): void
{
    if (!$imagePath) {
        return;
    }

    if (str_starts_with($imagePath, 'images/') && file_exists(__DIR__ . '/../' . $imagePath)) {
        @unlink(__DIR__ . '/../' . $imagePath);
    }
}

function uploadProductImage(array $file, ?string $oldImage = null): ?string
{
    $service = new ImageService();

    return $service->storeUploadedImage($file, __DIR__ . '/../images', $oldImage);
}

function validateProductData(string $name, string $price, int $categoryId): array
{
    $errors = [];

    if ($name === '') {
        $errors[] = 'Product name is required.';
    } elseif (mb_strlen($name) > 255) {
        $errors[] = 'Product name must be 255 characters or less.';
    }

    if ($price === '' || !is_numeric($price)) {
        $errors[] = 'Price must be a valid number.';
    } elseif ((float) $price < 0) {
        $errors[] = 'Price cannot be negative.';
    }

    if ($categoryId <= 0) {
        $errors[] = 'Please choose a category.';
    }

    return $errors;
}

function validateCategoryName(string $name): array
{
    $errors = [];

    if ($name === '') {
        $errors[] = 'Category name is required.';
    } elseif (mb_strlen($name) > 100) {
        $errors[] = 'Category name must be 100 characters or less.';
    }

    return $errors;
}

function validatePasswordChange(string $currentPassword, string $newPassword, string $confirmPassword): array
{
    $errors = [];

    if ($currentPassword === '') {
        $errors[] = 'Current password is required.';
    }

    if ($newPassword === '') {
        $errors[] = 'New password is required.';
    } elseif (strlen($newPassword) < 8) {
        $errors[] = 'New password must be at least 8 characters.';
    }

    if ($confirmPassword === '') {
        $errors[] = 'Please confirm the new password.';
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = 'New password and confirmation do not match.';
    }

    if ($currentPassword !== '' && $newPassword !== '' && $currentPassword === $newPassword) {
        $errors[] = 'New password must be different from the current password.';
    }

    return $errors;
}

function formatPrice(string $price): string
{
    return number_format((float) $price, 2, '.', '');
}

function getCurrentPage(): int
{
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

    return $page && $page > 0 ? $page : 1;
}

function getSearchKeyword(): string
{
    return trim((string) ($_GET['search'] ?? ''));
}

function getCategoryFilter(): int
{
    $categoryId = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

    return $categoryId && $categoryId > 0 ? $categoryId : 0;
}

function buildUrl(string $path, array $params = []): string
{
    $filteredParams = array_filter(
        $params,
        static fn ($value) => $value !== null && $value !== '' && $value !== 0
    );

    $query = http_build_query($filteredParams);

    return $query === '' ? $path : $path . '?' . $query;
}

function renderFlashMessage(): void
{
    $flash = getFlashMessage();

    if (!$flash || empty($flash['message'])) {
        return;
    }

    $typeMap = [
        'success' => 'success',
        'danger' => 'danger',
        'warning' => 'warning',
        'info' => 'info',
    ];

    $type = $typeMap[$flash['type']] ?? 'info';

    echo '<div class="alert alert-' . escape($type) . '" role="alert">' . escape((string) $flash['message']) . '</div>';
}

function csrfToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function csrfInput(): string
{
    return '<input type="hidden" name="csrf_token" value="' . escape(csrfToken()) . '">';
}

function verifyCsrfToken(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');

    if ($token === '' || !hash_equals(csrfToken(), $token)) {
        setFlashMessage('danger', 'Your session token is invalid. Please try again.');
        redirect('products.php');
    }
}

function isLoggedIn(): bool
{
    return isset($_SESSION['admin_user']);
}

function requireGuest(): void
{
    if (isLoggedIn()) {
        redirect('products.php');
    }
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        setFlashMessage('warning', 'Please log in first.');
        redirect('login.php');
    }
}

function currentAdminName(): string
{
    return (string) ($_SESSION['admin_user']['name'] ?? 'Admin');
}

function currentAdminId(): int
{
    return (int) ($_SESSION['admin_user']['id'] ?? 0);
}

function findAdminByEmail(mysqli $conn, string $email): ?array
{
    $service = new AuthService($conn);

    return $service->findAdminByEmail($email);
}

function loginAdmin(array $admin): void
{
    $_SESSION['admin_user'] = [
        'id' => (int) $admin['id'],
        'name' => (string) $admin['name'],
        'email' => (string) $admin['email'],
    ];
}

function logoutAdmin(): void
{
    unset($_SESSION['admin_user']);
}

function fetchCategories(mysqli $conn): array
{
    $service = new CategoryService($conn);

    return $service->all();
}

function fetchCategoriesWithProductCount(mysqli $conn): array
{
    $service = new CategoryService($conn);

    return $service->allWithProductCount();
}

function findCategoryById(mysqli $conn, int $categoryId): ?array
{
    $service = new CategoryService($conn);

    return $service->findById($categoryId);
}

function categoryExists(mysqli $conn, int $categoryId): bool
{
    $service = new CategoryService($conn);

    return $service->exists($categoryId);
}

function categoryNameExists(mysqli $conn, string $name, ?int $ignoreId = null): bool
{
    $service = new CategoryService($conn);

    return $service->nameExists($name, $ignoreId);
}

function createCategory(mysqli $conn, string $name): void
{
    $service = new CategoryService($conn);
    $service->create($name);
}

function updateCategoryName(mysqli $conn, int $id, string $name): void
{
    $service = new CategoryService($conn);
    $service->update($id, $name);
}

function deleteCategoryById(mysqli $conn, int $id): void
{
    $service = new CategoryService($conn);
    $service->delete($id);
}

function countProductsInCategory(mysqli $conn, int $id): int
{
    $service = new CategoryService($conn);

    return $service->productsCount($id);
}

function getDashboardStats(mysqli $conn): array
{
    $service = new DashboardService($conn);

    return $service->getStats();
}

function verifyAdminCurrentPassword(mysqli $conn, int $adminId, string $password): bool
{
    $service = new AuthService($conn);

    return $service->verifyCurrentPassword($adminId, $password);
}

function updateAdminPassword(mysqli $conn, int $adminId, string $newPassword): void
{
    $service = new AuthService($conn);
    $service->changePassword($adminId, $newPassword);
}

function findProductImageById(mysqli $conn, int $id): ?string
{
    $service = new ProductService($conn);

    return $service->findImageById($id);
}
