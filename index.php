<?php
include 'config/auth.php';
$theme = $_SESSION['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/icons/icons-1.13.1/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --canvas: #ffffff;
            --surface-soft: #f1f4f7;
            --surface-raised: #f8f9fa;
            --ink-deep: #0a1317;
            --ink: #1c1e21;
            --charcoal: #444950;
            --slate: #4b4c4f;
            --steel: #5d6c7b;
            --stone: #8595a4;
            --hairline: #ced0d4;
            --hairline-soft: #dee3e9;
            --primary: #0064e0;
            --primary-deep: #0457cb;
            --primary-soft: rgba(0, 100, 224, 0.08);
            --success: #31a24c;
            --attention: #f2a918;
            --warning: #f7b928;
            --critical: #e41e3f;
            --critical-strong: #f0284a;
            --sidebar-bg: #ffffff;
            --critical-soft: rgba(240, 40, 74, 0.08);
            --success-soft: rgba(49, 162, 76, 0.08);
            --info-soft: rgba(0, 100, 224, 0.08);
            --warn-soft: rgba(242, 169, 24, 0.08);
        }

        .theme-dark {
            --canvas: #111315;
            --surface-soft: #1c1e20;
            --surface-raised: #191b1d;
            --ink-deep: #f0f0f0;
            --ink: #d1d5db;
            --charcoal: #9ca3af;
            --slate: #9aa0a8;
            --steel: #6b7280;
            --stone: #555b65;
            --hairline: #2e3033;
            --hairline-soft: #252729;
            --primary: #3b82f6;
            --primary-deep: #2563eb;
            --primary-soft: rgba(59, 130, 246, 0.1);
            --success: #22c55e;
            --attention: #eab308;
            --warning: #f59e0b;
            --critical: #ef4444;
            --critical-strong: #f87171;
            --sidebar-bg: #090a0b;
            --critical-soft: rgba(248, 113, 113, 0.08);
            --success-soft: rgba(34, 197, 94, 0.08);
            --info-soft: rgba(59, 130, 246, 0.08);
            --warn-soft: rgba(234, 179, 8, 0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
            background: var(--canvas);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--hairline-soft);
            padding: 0;
            display: flex;
            flex-direction: column;
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .sidebar-brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid var(--hairline-soft);
        }

        .sidebar-brand h4 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.2px;
            color: var(--ink-deep);
            margin-bottom: 2px;
        }

        .sidebar-brand small {
            font-size: 12px;
            font-weight: 400;
            color: var(--stone);
            letter-spacing: -0.12px;
        }

        .sidebar-user {
            padding: 16px 20px;
            border-bottom: 1px solid var(--hairline-soft);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-user-avatar {
            width: 36px;
            height: 36px;
            background: var(--ink-deep);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--canvas);
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            overflow: hidden;
        }

        .sidebar-user-label {
            font-size: 11px;
            font-weight: 400;
            color: var(--stone);
            letter-spacing: -0.11px;
        }

        .sidebar-user-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-nav {
            padding: 12px 12px;
            flex: 1;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            margin-bottom: 2px;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: -0.14px;
            color: var(--slate);
            border-radius: 100px;
            transition: all 0.2s ease;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--surface-soft);
            color: var(--ink-deep);
        }

        .sidebar-nav .nav-link.active {
            background: var(--ink-deep);
            color: var(--canvas);
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-divider {
            border-color: var(--hairline-soft);
            margin: 8px 12px;
        }

        .sidebar-nav .logout-link {
            color: var(--critical);
        }

        .sidebar-nav .logout-link:hover {
            background: rgba(228, 30, 63, 0.08);
            color: var(--critical-strong);
        }

        /* Main content */
        .main-content {
            min-height: 100vh;
            background: var(--canvas);
            padding: 32px 40px;
        }

        /* Cards - Meta style */
        .meta-card {
            background: var(--canvas);
            border: 1px solid var(--hairline-soft);
            border-radius: 32px;
            padding: 32px;
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .meta-card-compact {
            background: var(--canvas);
            border: 1px solid var(--hairline-soft);
            border-radius: 16px;
            padding: 24px;
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .meta-card-header {
            font-size: 24px;
            font-weight: 500;
            letter-spacing: -0.24px;
            color: var(--ink-deep);
            margin-bottom: 24px;
        }

        /* Buttons - Meta pill style */
        .btn-meta-primary {
            background: var(--ink-deep);
            color: var(--canvas);
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.14px;
            border: none;
            border-radius: 100px;
            padding: 12px 28px;
            transition: background 0.2s ease;
        }

        .btn-meta-primary:active {
            background: var(--charcoal);
        }

        .btn-meta-cobalt {
            background: var(--primary);
            color: var(--canvas);
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.14px;
            border: none;
            border-radius: 100px;
            padding: 12px 28px;
            transition: background 0.2s ease;
        }

        .btn-meta-cobalt:active {
            background: var(--primary-deep);
        }

        .btn-meta-ghost {
            background: transparent;
            color: var(--ink-deep);
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.14px;
            border: 2px solid rgba(10, 19, 23, 0.12);
            border-radius: 100px;
            padding: 10px 22px;
            transition: all 0.2s ease;
        }

        .btn-meta-ghost:active {
            border-color: var(--ink-deep);
        }

        .btn-meta-sm {
            font-size: 14px;
            padding: 8px 16px;
        }

        /* Inputs */
        .form-control, .form-select {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 400;
            letter-spacing: -0.16px;
            color: var(--ink);
            background: var(--canvas);
            border: 1px solid var(--hairline);
            border-radius: 8px;
            height: 44px;
            padding: 12px 14px;
            transition: border-color 0.2s ease, border-width 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
            border-width: 2px;
            box-shadow: none;
            padding: 11px 13px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.14px;
            color: var(--ink);
            margin-bottom: 8px;
        }

        /* Badges - pill style */
        .badge-meta {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: -0.12px;
            padding: 4px 10px;
            border-radius: 100px;
        }

        .badge-success { background: var(--success); color: #fff; }
        .badge-attention { background: var(--attention); color: var(--canvas); }
        .badge-critical { background: var(--critical); color: #fff; }
        .badge-warning { background: var(--warning); color: var(--ink-deep); }

        /* Table */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table thead th {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: -0.12px;
            color: var(--steel);
            text-transform: uppercase;
            padding: 12px 16px;
            border-bottom: 1px solid var(--hairline-soft);
            text-align: left;
        }

        .meta-table tbody td {
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 400;
            letter-spacing: -0.14px;
            color: var(--ink);
            border-bottom: 1px solid var(--hairline-soft);
            vertical-align: middle;
        }

        .meta-table tbody tr:last-child td {
            border-bottom: none;
        }

        .meta-table tbody tr:hover {
            background: var(--surface-soft);
        }

        /* Alerts */
        .alert-meta {
            border-radius: 16px;
            border: none;
            font-size: 14px;
            font-weight: 400;
            letter-spacing: -0.14px;
            padding: 14px 18px;
        }

        .alert-meta-success { background: var(--success-soft); color: var(--success); }
        .alert-meta-danger { background: var(--critical-soft); color: var(--critical-strong); }
        .alert-meta-info { background: var(--info-soft); color: var(--primary); }
        .alert-meta-warning { background: var(--warn-soft); color: var(--attention); }

        /* Modal */
        .modal-content {
            border-radius: 32px;
            border: none;
            background: var(--canvas);
        }

        .modal-header, .modal-footer {
            background: var(--canvas);
        }

        .modal-header .btn-close {
            filter: var(--btn-close-filter, none);
        }

        .theme-dark .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .modal-header {
            border-bottom: 1px solid var(--hairline-soft);
            padding: 24px 32px;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 500;
            letter-spacing: -0.24px;
            color: var(--ink-deep);
        }

        .modal-body {
            padding: 32px;
        }

        .modal-footer {
            border-top: 1px solid var(--hairline-soft);
            padding: 20px 32px;
        }

        /* Page header */
        .page-header {
            margin-bottom: 32px;
        }

        .page-header h2 {
            font-size: 36px;
            font-weight: 500;
            letter-spacing: -0.36px;
            color: var(--ink-deep);
            margin-bottom: 4px;
        }

        .page-header p {
            font-size: 16px;
            font-weight: 400;
            letter-spacing: -0.16px;
            color: var(--steel);
        }

        /* Pill tabs */
        .pill-tab {
            display: inline-block;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.14px;
            padding: 8px 16px;
            border-radius: 100px;
            border: 1px solid var(--hairline);
            background: var(--canvas);
            color: var(--ink);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .pill-tab.active {
            background: var(--ink-deep);
            color: var(--canvas);
            border-color: var(--ink-deep);
        }

        .pill-tab:hover:not(.active) {
            background: var(--surface-soft);
        }

        @media (max-width: 768px) {
            .main-content { padding: 24px 16px; }
            .meta-card { padding: 24px; border-radius: 24px; }
            .page-header h2 { font-size: 28px; }
        }
    </style>
</head>

<body class="theme-<?= $theme ?>">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0 sidebar">
                <div class="sidebar-brand">
                    <h4>Perpustakaan</h4>
                    <small>Digital Library</small>
                </div>
                <div class="sidebar-user">
                    <div class="sidebar-user-avatar"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?></div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-label">Masuk sebagai</div>
                        <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div>
                    </div>
                </div>
                <nav class="sidebar-nav">
                    <a class="nav-link <?= (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : '' ?>" href="?page=dashboard">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Dashboard
                    </a>
                    <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'buku') ? 'active' : '' ?>" href="?page=buku">
                        <i class="bi bi-book"></i> Buku
                    </a>
                    <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'anggota') ? 'active' : '' ?>" href="?page=anggota">
                        <i class="bi bi-people"></i> Anggota
                    </a>
                    <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'peminjaman') ? 'active' : '' ?>" href="?page=peminjaman">
                        <i class="bi bi-box-arrow-right"></i> Peminjaman
                    </a>
                    <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'pengembalian') ? 'active' : '' ?>" href="?page=pengembalian">
                        <i class="bi bi-box-arrow-in-right"></i> Pengembalian
                    </a>
                    <a class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'laporan') ? 'active' : '' ?>" href="?page=laporan">
                        <i class="bi bi-bar-chart"></i> Laporan
                    </a>
                    <hr class="sidebar-divider">
                    <a class="nav-link" href="proses/toggle_theme.php">
                        <i class="bi <?= $theme === 'dark' ? 'bi-sun' : 'bi-moon' ?>"></i> Mode <?= $theme === 'dark' ? 'Terang' : 'Gelap' ?>
                    </a>
                    <a class="nav-link logout-link" href="proses/logout.php" onclick="return confirm('Yakin ingin logout?')">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

                switch ($page) {
                    case 'dashboard':
                        include 'pages/dashboard.php';
                        break;
                    case 'buku':
                        include 'pages/buku.php';
                        break;
                    case 'anggota':
                        include 'pages/anggota.php';
                        break;
                    case 'peminjaman':
                        include 'pages/peminjaman.php';
                        break;
                    case 'pengembalian':
                        include 'pages/pengembalian.php';
                        break;
                    case 'laporan':
                        include 'pages/laporan.php';
                        break;
                    default:
                        include 'pages/dashboard.php';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);
    </script>
</body>

</html>
