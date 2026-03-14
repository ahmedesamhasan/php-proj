<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireAuth();

$pageTitle = 'Change Password';
require_once 'includes/layout_start.php';
?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="shop-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <h1 class="h3 mb-1">Change Password</h1>
                        <p class="text-muted mb-0">Use a strong password with at least 8 characters.</p>
                    </div>
                    <a href="dashboard.php" class="btn btn-outline-dark btn-sm">Back</a>
                </div>

                <?php renderFlashMessage(); ?>

                <form action="update_password.php" method="post" class="d-grid gap-3">
                    <?= csrfInput() ?>
                    <div>
                        <label for="current_password" class="form-label">Current password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>
                    <div>
                        <label for="new_password" class="form-label">New password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                    </div>
                    <div>
                        <label for="confirm_password" class="form-label">Confirm new password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark">Update password</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/layout_end.php'; ?>
