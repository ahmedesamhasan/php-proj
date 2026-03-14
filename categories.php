<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

$categories = fetchCategoriesWithProductCount($conn);
$pageTitle = 'Categories';
require_once 'includes/layout_start.php';
?>
<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="shop-card shadow-sm h-100">
                <div class="mb-4">
                    <h1 class="h3 mb-1">Categories</h1>
                    <p class="text-muted mb-0">Create, rename, or remove empty categories.</p>
                </div>

                <?php renderFlashMessage(); ?>

                <form action="store_category.php" method="post" class="d-grid gap-3">
                    <?= csrfInput() ?>
                    <div>
                        <label for="name" class="form-label">Category name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= escape(oldInput('category_name')) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-dark">Save category</button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="shop-card shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                    <div>
                        <h2 class="h4 mb-1">Saved categories</h2>
                        <p class="text-muted mb-0">Each row shows how many products use that category.</p>
                    </div>
                    <a href="index.php" class="btn btn-outline-dark btn-sm">Add product</a>
                </div>

                <?php if ($categories !== []): ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Products</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= escape($category['name']) ?></td>
                                        <td><span class="badge text-bg-light border"><?= (int) $category['products_count'] ?></span></td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                <a href="edit_category.php?id=<?= (int) $category['id'] ?>" class="btn btn-outline-dark btn-sm">Edit</a>
                                                <form action="delete_category.php" method="post" class="d-inline">
                                                    <?= csrfInput() ?>
                                                    <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Delete this category?');"
                                                        <?= (int) $category['products_count'] > 0 ? 'disabled' : '' ?>
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="small text-muted mt-3">Delete is only available for empty categories.</div>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0">No categories yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/layout_end.php'; ?>
