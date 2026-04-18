<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VK Mini Module</title>
    <style>
        body{font-family:Arial,sans-serif;margin:0;background:#f5f7fb;color:#1a1a1a}
        .wrap{max-width:1100px;margin:0 auto;padding:20px}
        .card{background:#fff;border-radius:10px;padding:16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
        .header{display:flex;justify-content:space-between;align-items:center;background:#fff;padding:12px 20px;box-shadow:0 1px 4px rgba(0,0,0,.05)}
        .nav a{margin-right:10px;text-decoration:none;padding:8px 10px;border-radius:6px;color:#245}
        .nav .active{background:#2a69ff;color:#fff}
        .btn{display:inline-block;border:0;background:#2a69ff;color:#fff;padding:10px 14px;border-radius:8px;cursor:pointer;text-decoration:none}
        .btn.secondary{background:#5f6f82}
        .alert{padding:10px 12px;border-radius:8px;margin-bottom:12px}
        .alert.success{background:#e6ffec;color:#0b6a2f}
        .alert.error{background:#ffe8e8;color:#8a1f1f}
        input[type=text],input[type=password],textarea{width:100%;padding:8px;border:1px solid #d8deea;border-radius:6px}
        table{width:100%;border-collapse:collapse}
        th,td{border-bottom:1px solid #edf0f5;padding:8px;text-align:left;font-size:14px;vertical-align:top}
        .muted{color:#6b7280;font-size:13px}
        pre{background:#0f172a;color:#d5e0ff;padding:12px;border-radius:8px;overflow:auto;max-height:280px}
        .userbox{display:flex;gap:8px;align-items:center}
        .avatar{width:38px;height:38px;border-radius:999px;object-fit:cover;background:#e2e8f0}
    </style>
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>
<div class="wrap">
    <?php require __DIR__ . '/partials/nav.php'; ?>
    <?php require __DIR__ . '/partials/alerts.php'; ?>
    <?php require $contentView; ?>
</div>
</body>
</html>
