<?php
/** @var string $pageTitle */
$pageTitle = $pageTitle ?? 'PHP Shop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?></title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page-wrapper">
        <nav class="navbar navbar-expand-lg border-bottom bg-white sticky-top">
            <div class="container">
                <a class="navbar-brand fw-semibold" href="dashboard.php">Simple PHP Shop</a>

                <?php if (isLoggedIn()): ?>
                    <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
                        <span class="small text-muted">Signed in as <?= escape(currentAdminName()) ?></span>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-dark">Dashboard</a>
                        <a href="index.php" class="btn btn-sm btn-outline-dark">Add product</a>
                        <a href="categories.php" class="btn btn-sm btn-outline-dark">Categories</a>
                        <a href="change_password.php" class="btn btn-sm btn-outline-dark">Password</a>
                        <form action="logout.php" method="post" class="d-inline">
                            <?= csrfInput() ?>
                            <button type="submit" class="btn btn-sm btn-dark">Logout</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
