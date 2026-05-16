<?php
session_start();

if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}
$theme = $_SESSION['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Perpustakaan</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/icons/icons-1.13.1/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --canvas: #ffffff;
            --surface-soft: #f1f4f7;
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
            --success: #31a24c;
            --critical: #e41e3f;
            --critical-strong: #f0284a;
            --critical-soft: rgba(240, 40, 74, 0.08);
            --info-soft: rgba(0, 100, 224, 0.08);
        }

        .theme-dark {
            --canvas: #111315;
            --surface-soft: #1c1e20;
            --ink-deep: #f0f0f0;
            --ink: #d1d5db;
            --charcoal: #9ca3af;
            --slate: #4b4c4f;
            --steel: #6b7280;
            --stone: #555b65;
            --hairline: #2e3033;
            --hairline-soft: #252729;
            --primary: #3b82f6;
            --primary-deep: #2563eb;
            --success: #22c55e;
            --critical: #ef4444;
            --critical-strong: #f87171;
            --critical-soft: rgba(248, 113, 113, 0.08);
            --info-soft: rgba(59, 130, 246, 0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
            background: var(--canvas);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 24px;
            animation: fadeUp 0.5s ease-out both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-brand {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-brand-icon {
            width: 56px;
            height: 56px;
            background: var(--ink-deep);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .login-brand-icon i {
            font-size: 1.5rem;
            color: var(--canvas);
        }

        .login-brand h1 {
            font-size: 28px;
            font-weight: 500;
            letter-spacing: -0.28px;
            color: var(--ink-deep);
            margin-bottom: 6px;
        }

        .login-brand p {
            font-size: 16px;
            font-weight: 400;
            letter-spacing: -0.16px;
            color: var(--steel);
            line-height: 1.5;
        }

        .login-form {
            background: var(--canvas);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.14px;
            color: var(--ink);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--stone);
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-control {
            width: 100%;
            height: 44px;
            padding: 12px 14px 12px 42px;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 400;
            letter-spacing: -0.16px;
            color: var(--ink);
            background: var(--canvas);
            border: 1px solid var(--hairline);
            border-radius: 8px;
            transition: border-color 0.2s ease, border-width 0.2s ease;
        }

        .form-control::placeholder {
            color: var(--stone);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            border-width: 2px;
            padding: 11px 13px 11px 41px;
        }

        .input-wrapper:focus-within i {
            color: var(--primary);
        }

        .btn-login {
            width: 100%;
            height: 44px;
            background: var(--ink-deep);
            color: var(--canvas);
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -0.14px;
            border: none;
            border-radius: 100px;
            padding: 14px 30px;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-top: 8px;
        }

        .btn-login:active {
            background: var(--charcoal);
        }

        .alert {
            border-radius: 16px;
            border: none;
            font-size: 14px;
            font-weight: 400;
            letter-spacing: -0.14px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: var(--critical-soft);
            color: var(--critical-strong);
        }

        .alert-info {
            background: var(--info-soft);
            color: var(--primary);
        }

        .alert .btn-close {
            opacity: 0.6;
        }

        .login-footer {
            text-align: center;
            margin-top: 32px;
            font-size: 12px;
            font-weight: 400;
            color: var(--stone);
            letter-spacing: -0.12px;
        }

        @media (max-width: 480px) {
            .login-container { padding: 16px; }
            .login-brand { margin-bottom: 32px; }
        }
    </style>
</head>

<body class="theme-<?= $theme ?>">
    <div class="login-container">
        <div class="login-brand">
            <h1>Perpustakaan</h1>
            <p>Masuk ke sistem perpustakaan digital</p>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'gagal'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Username atau password salah.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['status'] == 'logout'): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    Anda telah logout.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="proses/login_action.php" method="POST" class="login-form">
            <div class="form-group">
                <label class="form-label">Username</label>
                <div class="input-wrapper">
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                    <i class="bi bi-person"></i>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    <i class="bi bi-lock"></i>
                </div>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <div class="login-footer">
            &copy; 2026 Perpustakaan Digital
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
