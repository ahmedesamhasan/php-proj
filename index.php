<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

$categories = fetchCategories($conn);
$pageTitle = 'Add Product';
require_once 'includes/layout_start.php';
?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="shop-card shadow-sm">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <span class="badge text-bg-dark mb-3">Admin dashboard</span>
                        <h1 class="h3 mb-2">Add New Product</h1>
                        <p class="text-muted mb-0">Create a new product with a category, price, and image.</p>
                    </div>
                    <a href="products.php" class="btn btn-outline-dark">Manage products</a>
                </div>

                <?php renderFlashMessage(); ?>

                <form action="insert.php" method="post" enctype="multipart/form-data" class="d-grid gap-3">
                    <?= csrfInput() ?>

                    <div>
                        <label for="name" class="form-label">Product name</label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            placeholder="Enter product name"
                            value="<?= escape(oldInput('name')) ?>"
                            required
                        >
                    </div>

                    <div>
                        <label for="category_id" class="form-label">Category</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">Choose a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= oldInput('category_id') === (string) $category['id'] ? 'selected' : '' ?>>
                                    <?= escape($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="price" class="form-label">Price</label>
                        <input
                            type="number"
                            class="form-control"
                            id="price"
                            name="price"
                            min="0"
                            step="0.01"
                            placeholder="Enter product price"
                            value="<?= escape(oldInput('price')) ?>"
                            required
                        >
                    </div>

                    <div>
                        <label for="image" class="form-label">Product image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <div class="form-text">Allowed: JPG, PNG, WEBP, GIF. Max size: 2MB.</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="upload" class="btn btn-dark">Save product</button>
                        <a href="products.php" class="btn btn-light border">View all products</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/layout_end.php'; ?>
