<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    setFlashMessage('danger', 'Invalid category id.');
    redirect('categories.php');
}

$category = findCategoryById($conn, $id);

if (!$category) {
    setFlashMessage('danger', 'Category not found.');
    redirect('categories.php');
}

$pageTitle = 'Edit Category';
require_once 'includes/layout_start.php';
?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="shop-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <h1 class="h3 mb-1">Edit Category</h1>
                        <p class="text-muted mb-0">Rename the category without changing product records.</p>
                    </div>
                    <a href="categories.php" class="btn btn-outline-dark btn-sm">Back</a>
                </div>

                <?php renderFlashMessage(); ?>

                <form action="update_category.php" method="post" class="d-grid gap-3">
                    <?= csrfInput() ?>
                    <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                    <div>
                        <label for="name" class="form-label">Category name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="<?= escape(oldInput('category_name', (string) $category['name'])) ?>"
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-dark">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/layout_end.php'; ?>
