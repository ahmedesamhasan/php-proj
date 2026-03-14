<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    setFlashMessage('danger', 'Invalid product id.');
    redirect('products.php');
}

$findProduct = mysqli_prepare($conn, 'SELECT id, category_id, name, price, image FROM products WHERE id = ?');
mysqli_stmt_bind_param($findProduct, 'i', $id);
mysqli_stmt_execute($findProduct);
$productResult = mysqli_stmt_get_result($findProduct);
$product = mysqli_fetch_assoc($productResult);
mysqli_stmt_close($findProduct);

if (!$product) {
    setFlashMessage('danger', 'Product not found.');
    redirect('products.php');
}

$categories = fetchCategories($conn);
$pageTitle = 'Edit Product';
require_once 'includes/layout_start.php';
?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="shop-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <h1 class="h3 mb-1">Edit Product</h1>
                        <p class="text-muted mb-0">Update the product details and image if needed.</p>
                    </div>
                    <a href="products.php" class="btn btn-outline-dark btn-sm">Back</a>
                </div>

                <?php renderFlashMessage(); ?>

                <form action="up.php" method="post" enctype="multipart/form-data" class="d-grid gap-3">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                    <input type="hidden" name="old_image" value="<?= escape($product['image']) ?>">

                    <div>
                        <label for="name" class="form-label">Product name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= escape($product['name']) ?>" required>
                    </div>

                    <div>
                        <label for="category_id" class="form-label">Category</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">Choose a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= (int) $product['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
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
                            value="<?= escape((string) $product['price']) ?>"
                            required
                        >
                    </div>

                    <div>
                        <label class="form-label d-block">Current image</label>
                        <img src="<?= escape($product['image']) ?>" alt="<?= escape($product['name']) ?>" class="current-image rounded border">
                    </div>

                    <div>
                        <label for="image" class="form-label">Replace image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Leave empty if you want to keep the current image.</div>
                    </div>

                    <button type="submit" name="update" class="btn btn-dark">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/layout_end.php'; ?>
