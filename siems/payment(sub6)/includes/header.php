<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'College Payment System'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="#" style="letter-spacing: 0.5px;">
                <i class="bi bi-mortarboard-fill me-2 fs-4 align-middle"></i>
                <span class="align-middle text-white" style="font-weight: 800; opacity: 1 !important;">STUDENT PAYMENT SYSTEM</span>
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="navbar-text text-white me-4 fw-medium" style="opacity: 1;">
                        <i class="bi bi-person-circle me-1"></i> Welcome!!
                    </span>
                    <?php
                    require_once __DIR__ . '/auth.php';
                    $home_url = isset($_SESSION['role']) ? getUserHomePath($_SESSION['student_id'] ?? '', $_SESSION['role']) : 'index.php';
                    ?>
                    <a href="<?php echo SITE_URL . $home_url; ?>" class="btn bg-white text-success fw-bold rounded me-4" style="padding: 0.25rem 1rem; font-size: 0.9rem;">
                        Home
                    </a>
                    <a href="<?php echo SITE_URL; ?>logout.php" class="nav-link text-white fw-bold" style="padding: 0; opacity: 1;">
                        <i class="bi bi-power me-1"></i>Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container bg-white rounded-4 p-4 p-md-5 mt-4 mb-5 shadow-sm" style="min-height: 75vh; border: 1px solid #e2e8f0;">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['msg_type'] ?? 'info'; ?> alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" style="border-radius: 8px; <?php echo ($_SESSION['msg_type'] ?? 'info') === 'success' ? 'background-color: #f0fdf4; border-left: 4px solid #22c55e !important; color: #16a34a;' : 'background-color: #eff6ff; border-left: 4px solid #3b82f6 !important; color: #1d4ed8;'; ?>">
                <i class="bi <?php echo ($_SESSION['msg_type'] ?? 'info') === 'success' ? 'bi-check-circle-fill' : 'bi-info-circle-fill'; ?> me-3 fs-5"></i>
                <div class="flex-grow-1 fw-medium"><?php echo $_SESSION['message']; unset($_SESSION['message'], $_SESSION['msg_type']); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: none; opacity: 0.5;"></button>
            </div>
        <?php endif; ?>
