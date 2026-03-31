<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Login') ?> - ActuMonde</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f6f8;
            color: #1f1f1f;
        }

        .wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <?= $content ?>
    </div>
</body>
</html>
