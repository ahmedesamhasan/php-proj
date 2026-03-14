<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

$stats = getDashboardStats($conn);
$pageTitle = 'Dashboard';
require_once 'includes/layout_start.php';
?>
<section class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Quick overview of products, categories, and recent activity.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php" class="btn btn-dark">Add product</a>
            <a href="categories.php" class="btn btn-outline-dark">Manage categories</a>
        </div>
    </div>

    <?php renderFlashMessage(); ?>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card shadow-sm h-100">
                <div class="small text-muted mb-2">Products</div>
                <div class="display-6 fw-semibold"><?= (int) $stats['products'] ?></div>
                <div class="text-muted">total saved items</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card shadow-sm h-100">
                <div class="small text-muted mb-2">Categories</div>
                <div class="display-6 fw-semibold"><?= (int) $stats['categories'] ?></div>
                <div class="text-muted">available groups</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card shadow-sm h-100">
                <div class="small text-muted mb-2">Average price</div>
                <div class="display-6 fw-semibold">$<?= escape(number_format((float) $stats['average_price'], 2)) ?></div>
                <div class="text-muted">based on current products</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="shop-card shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                    <div>
                        <h2 class="h4 mb-1">Latest products</h2>
                        <p class="text-muted mb-0">The newest items added to the shop.</p>
                    </div>
                    <a href="products.php" class="btn btn-outline-dark btn-sm">View all</a>
                </div>

                <?php if ($stats['latest_products'] !== []): ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['latest_products'] as $product): ?>
                                    <tr>
                                        <td><?= escape($product['name']) ?></td>
                                        <td><?= escape($product['category_name']) ?></td>
                                        <td>$<?= escape(number_format((float) $product['price'], 2)) ?></td>
                                        <td><?= escape((string) $product['created_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0">No products yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="shop-card shadow-sm h-100">
                <div class="mb-3">
                    <h2 class="h4 mb-1">Products by category</h2>
                    <p class="text-muted mb-0">Simple breakdown to spot empty or active categories.</p>
                </div>

                <?php if ($stats['category_breakdown'] !== []): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($stats['category_breakdown'] as $row): ?>
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center gap-3">
                                <span><?= escape($row['name']) ?></span>
                                <span class="badge text-bg-light border"><?= (int) $row['total'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0">No category data yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/layout_end.php'; ?>
