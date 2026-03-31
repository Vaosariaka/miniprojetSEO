<?php if (empty($articles)): ?>
    <article class="article-row">
        <div class="article-row-body">
            <div class="section-tag">Info</div>
            <h4>Pas encore d'autres articles publies</h4>
            <p>L'article principal est affiche dans la section A la une.</p>
        </div>
    </article>
<?php else: ?>
    <?php foreach ($articles as $article): ?>
        <article class="article-row">
            <img
                src="<?= esc($article['image_path'] ?? 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=400&q=80') ?>"
                alt="<?= esc($article['image_alt'] ?? $article['title']) ?>"
            >
            <div class="article-row-body">
                <div class="section-tag"><?= esc($article['category_name'] ?? 'Actualite') ?></div>
                <h4>
                    <a href="<?= esc(($base ?? '') . '/article/' . ($article['slug'] ?? '')) ?>">
                        <?= esc($article['title'] ?? '') ?>
                    </a>
                </h4>
                <p><?= esc($article['summary'] ?? '') ?></p>
                <div class="article-meta">
                    <span class="byline"><?= esc($article['author_name'] ?? 'Redaction') ?></span>
                    <span class="timestamp"><i class="far fa-clock"></i> <?= esc($article['published_at'] ?? '') ?></span>
                    <?php if (!empty($article['slug'])): ?>
                        <a href="<?= esc(($base ?? '') . '/article/' . $article['slug']) ?>" class="read-more">
                            Voir article <i class="fas fa-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
