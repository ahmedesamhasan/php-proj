<?php

declare(strict_types=1);

require_once 'config.php';
require_once 'includes/helpers.php';

requireGuest();

$pageTitle = 'Login';
require_once 'includes/layout_start.php';
?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="shop-card shadow-sm">
                <div class="text-center mb-4">
                    <span class="badge text-bg-dark mb-3">Admin access</span>
                    <h1 class="h3 mb-2">Login</h1>
                    <p class="text-muted mb-0">Use the seeded admin account to manage products.</p>
                </div>

                <?php renderFlashMessage(); ?>

                <form action="authenticate.php" method="post" class="d-grid gap-3">
                    <?= csrfInput() ?>

                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= escape(oldInput('email')) ?>" required>
                    </div>

                    <div>
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-dark">Login</button>
                </form>

                <div class="login-hint mt-3">
                    <div class="small text-muted">Default demo account</div>
                    <div><strong>Email:</strong> admin@example.com</div>
                    <div><strong>Password:</strong> admin123</div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/layout_end.php'; ?>
