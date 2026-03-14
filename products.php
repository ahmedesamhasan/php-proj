<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

$searchKeyword = getSearchKeyword();
$categoryFilter = getCategoryFilter();
$currentPage = getCurrentPage();
$perPage = 6;
$offset = ($currentPage - 1) * $perPage;
$categories = fetchCategories($conn);

$filters = [];
$params = [];
$types = '';

if ($searchKeyword !== '') {
    $filters[] = 'p.name LIKE ?';
    $params[] = '%' . $searchKeyword . '%';
    $types .= 's';
}

if ($categoryFilter > 0) {
    $filters[] = 'p.category_id = ?';
    $params[] = $categoryFilter;
    $types .= 'i';
}

$whereSql = $filters !== [] ? ' WHERE ' . implode(' AND ', $filters) : '';

$totalProducts = 0;
$products = [];

$countSql = 'SELECT COUNT(*) AS total FROM products p' . $whereSql;
$countStatement = mysqli_prepare($conn, $countSql);
if ($types !== '') {
    mysqli_stmt_bind_param($countStatement, $types, ...$params);
}
mysqli_stmt_execute($countStatement);
$countResult = mysqli_stmt_get_result($countStatement);
$totalProducts = (int) (mysqli_fetch_assoc($countResult)['total'] ?? 0);
mysqli_stmt_close($countStatement);

$productSql = 'SELECT p.id, p.name, p.price, p.image, c.name AS category_name
    FROM products p
    INNER JOIN categories c ON c.id = p.category_id'
    . $whereSql .
    ' ORDER BY p.id DESC LIMIT ? OFFSET ?';
$productStatement = mysqli_prepare($conn, $productSql);
$productTypes = $types . 'ii';
$productParams = [...$params, $perPage, $offset];
mysqli_stmt_bind_param($productStatement, $productTypes, ...$productParams);
mysqli_stmt_execute($productStatement);
$productResult = mysqli_stmt_get_result($productStatement);

while ($row = mysqli_fetch_assoc($productResult)) {
    $products[] = $row;
}

mysqli_stmt_close($productStatement);

$totalPages = max(1, (int) ceil($totalProducts / $perPage));

if ($currentPage > $totalPages && $totalProducts > 0) {
    redirect(buildUrl('products.php', [
        'search' => $searchKeyword,
        'category_id' => $categoryFilter,
        'page' => $totalPages,
    ]));
}

$pageTitle = 'Products';
require_once 'includes/layout_start.php';
?>
<section class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Products</h1>
            <p class="text-muted mb-0">Manage products, filter by category, and browse with pagination.</p>
        </div>
        <a href="index.php" class="btn btn-dark">Add new product</a>
    </div>

    <?php renderFlashMessage(); ?>

    <div class="row g-4 mb-4">
        <div class="col-lg-9">
            <div class="shop-card shadow-sm p-3 p-md-4 h-100">
                <form action="products.php" method="get" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="form-label">Search by product name</label>
                        <input
                            type="text"
                            class="form-control"
                            id="search"
                            name="search"
                            value="<?= escape($searchKeyword) ?>"
                            placeholder="Try laptop, phone, watch..."
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Category filter</label>
                        <select id="category_id" name="category_id" class="form-select">
                            <option value="">All categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= $categoryFilter === (int) $category['id'] ? 'selected' : '' ?>>
                                    <?= escape($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 d-grid d-md-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-fill">Filter</button>
                        <a href="products.php" class="btn btn-light border flex-fill">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="stats-card shadow-sm h-100">
                <div class="small text-muted mb-2">Quick summary</div>
                <div class="display-6 fw-semibold"><?= $totalProducts ?></div>
                <div class="text-muted">matching product<?= $totalProducts === 1 ? '' : 's' ?></div>
                <div class="small text-muted mt-3">Page <?= $currentPage ?> of <?= $totalPages ?></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if ($products !== []): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-sm-6 col-lg-4">
                    <article class="card product-card h-100 shadow-sm border-0">
                        <img
                            src="<?= escape($product['image']) ?>"
                            class="card-img-top product-image"
                            alt="<?= escape($product['name']) ?>"
                        >
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h2 class="h5 card-title mb-0"><?= escape($product['name']) ?></h2>
                                <span class="badge text-bg-light border"><?= escape($product['category_name']) ?></span>
                            </div>
                            <p class="text-muted mb-4">$<?= escape(number_format((float) $product['price'], 2)) ?></p>

                            <div class="mt-auto d-flex gap-2">
                                <a href="update.php?id=<?= (int) $product['id'] ?>" class="btn btn-outline-dark btn-sm w-100">Edit</a>

                                <form action="delete.php" method="post" class="w-100">
                                    <?= csrfInput() ?>
                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm w-100"
                                        onclick="return confirm('Delete this product?');"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-secondary mb-0" role="alert">
                    No products found<?= $searchKeyword !== '' ? ' for "' . escape($searchKeyword) . '"' : '' ?>.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="mt-4" aria-label="Products pagination">
            <ul class="pagination justify-content-center flex-wrap">
                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <a
                        class="page-link"
                        href="<?= escape(buildUrl('products.php', ['search' => $searchKeyword, 'category_id' => $categoryFilter, 'page' => $currentPage - 1])) ?>"
                    >
                        Previous
                    </a>
                </li>

                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <li class="page-item <?= $page === $currentPage ? 'active' : '' ?>">
                        <a
                            class="page-link"
                            href="<?= escape(buildUrl('products.php', ['search' => $searchKeyword, 'category_id' => $categoryFilter, 'page' => $page])) ?>"
                        >
                            <?= $page ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a
                        class="page-link"
                        href="<?= escape(buildUrl('products.php', ['search' => $searchKeyword, 'category_id' => $categoryFilter, 'page' => $currentPage + 1])) ?>"
                    >
                        Next
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</section>
<?php require_once 'includes/layout_end.php'; ?>
