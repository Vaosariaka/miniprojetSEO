<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités - ActuMonde</title>
    <style>
        /* Copiez tout le CSS ci-dessus ici */
    </style>
</head>
<body>

<section class="grid">
    <?php foreach ($articles as $article): ?>
        <article class="card">
            <img src="/miniprojetSEO/public/<?= esc($article['image_path']) ?>" 
                 alt="<?= esc($article['image_alt']) ?>">
            <h2><?= esc($article['title']) ?></h2>
            <p><?= esc($article['summary']) ?></p>
            <a class="btn" href="/article/<?= esc($article['slug']) ?>">Lire</a>
        </article>
    <?php endforeach; ?>
</section>

</body>
</html>