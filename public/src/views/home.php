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
<?php if (!empty($articles)): ?>
    <article class="hero-lead">
        <?php if (!empty($articles[0]['image_path'])): ?>
            <img src="/miniprojetSEO/public/<?= esc($articles[0]['image_path']) ?>" alt="<?= esc($articles[0]['image_alt'] ?? $articles[0]['title']) ?>" loading="eager">
        <?php else: ?>
            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=900&q=80" alt="Image par défaut" loading="eager">
        <?php endif; ?>
        <div class="hero-lead-text">
            <div class="section-tag"><?= esc($articles[0]['category_name'] ?? '') ?></div>
            <h2><?= esc($articles[0]['title']) ?></h2>
            <p><?= esc($articles[0]['summary']) ?></p>
            <div class="article-meta">
                <span class="byline">Par <?= esc($articles[0]['author_name'] ?? '') ?></span>
                <span class="timestamp"><i class="far fa-clock"></i> <?= esc($articles[0]['published_at']) ?></span>
                <a href="/miniprojetSEO/public/article/<?= esc($articles[0]['slug']) ?>" class="read-more">Lire l'article <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </article>
<?php else: ?>
    <p>Aucun article publié pour le moment.</p>
<?php endif; ?>
</body>
</html>



