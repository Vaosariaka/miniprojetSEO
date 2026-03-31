<article class="article-row" style="display:block">
    <div class="article-row-body" style="padding: 1.5rem">
        <div class="section-tag"><?= esc($article['category_name'] ?? 'Actualite') ?></div>
        <h1 style="font-family:'Playfair Display',serif;font-size:2rem;margin:.6rem 0 1rem 0">
            <?= esc($article['title'] ?? '') ?>
        </h1>
        <p style="color:#555;margin-bottom:1rem">
            Par <?= esc($article['author_name'] ?? 'Redaction') ?> -
            <?= esc($article['published_at'] ?? '') ?>
        </p>

        <?php if (!empty($article['featured_image_path'])): ?>
            <img
                src="<?= esc($article['featured_image_path']) ?>"
                alt="<?= esc($article['featured_image_alt'] ?? $article['title']) ?>"
                style="width:100%;max-height:460px;object-fit:cover;border-radius:6px;margin-bottom:1rem"
            >
        <?php endif; ?>

        <div style="line-height:1.8;color:#222">
            <?= $article['content'] ?? '' ?>
        </div>
    </div>
</article>
