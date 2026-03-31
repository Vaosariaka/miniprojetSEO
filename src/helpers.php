<?php

declare(strict_types=1);

function esc(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function base_url_path(string $path): string
{
    return $path === '' ? '/' : $path;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function redirect_to(string $path): never
{
    header('Location: ' . base_url_path($path));
    exit;
}

function ensure_admin(): void
{
    if (!is_logged_in()) {
        redirect_to('/admin/login');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validate_csrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function initialize_database(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS articles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            summary TEXT NOT NULL,
            content LONGTEXT NOT NULL,
            image_path VARCHAR(255) DEFAULT "assets/img/iran.png",
            image_alt VARCHAR(255) NOT NULL,
            meta_title VARCHAR(255) NOT NULL,
            meta_description VARCHAR(320) NOT NULL,
            is_published TINYINT(1) NOT NULL DEFAULT 1,
            published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $seedStmt = $pdo->prepare('SELECT id FROM articles WHERE slug = :slug LIMIT 1');
    $seedStmt->execute(['slug' => 'guerre-en-iran']);

    if (!$seedStmt->fetch()) {
        $insertSeed = $pdo->prepare(
            'INSERT INTO articles
             (title, slug, summary, content, image_path, image_alt, meta_title, meta_description, is_published)
             VALUES
             (:title, :slug, :summary, :content, :image_path, :image_alt, :meta_title, :meta_description, :is_published)'
        );

        $insertSeed->execute([
            'title' => 'Guerre en Iran',
            'slug' => 'guerre-en-iran',
            'summary' => 'Conflit debutant le 28 fevrier 2026 par des frappes ciblees americano-israeliennes, suivi de represailles iraniennes dans toute la region.',
            'content' => '<h2>Contexte</h2><p>La guerre d\'Iran debute le 28 fevrier 2026 avec une operation militaire conjointe americano-israelienne. Cote israelien, elle est nommee Operation Roaring Lion. Cote americain, elle prend le nom d\'Operation Epic Fury.</p><h2>Escalade</h2><p>En reponse, l\'Iran lance l\'operation Promesse honnete 4, avec des frappes et des drones vers plusieurs cibles au Moyen-Orient, a Chypre et au Caucase.</p><h3>Facteurs declencheurs</h3><p>Le conflit eclate six semaines apres une repression violente de manifestations anti-gouvernementales en Iran. Les tensions regionales et la rupture diplomatique precipitent l\'affrontement.</p><h2>Zones frappees</h2><p>Les premieres frappes visent notamment Teheran, Ispahan, Qom, Karadj et Kermanchah. La riposte iranienne touche aussi des bases militaires americaines dans la region et certaines infrastructures civiles.</p>',
            'image_path' => 'assets/img/iran.png',
            'image_alt' => 'Illustrationde guerre en iran',
            'meta_title' => 'Guerre en Iran - Analyse et chronologie',
            'meta_description' => 'Site d information sur la guerre en Iran: contexte, chronologie, zones de conflit et consequences regionales.',
            'is_published' => 1,
        ]);
    }
}

function ensure_default_admin(PDO $pdo): void
{
    $checkStmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $checkStmt->execute(['username' => 'admin']);

    if ($checkStmt->fetch()) {
        return;
    }

    $insertStmt = $pdo->prepare(
        'INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)'
    );

    $insertStmt->execute([
        'username' => 'admin',
        'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
    ]);
}

function fetch_published_articles(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT id, title, slug, summary, image_path, image_alt, published_at
         FROM articles
         WHERE is_published = 1
         ORDER BY published_at DESC, id DESC'
    );

    return $stmt->fetchAll();
}

function fetch_article_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare(
        'SELECT * FROM articles WHERE slug = :slug AND is_published = 1 LIMIT 1'
    );
    $stmt->execute(['slug' => $slug]);
    $article = $stmt->fetch();

    return $article ?: null;
}

function fetch_all_articles(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT id, title, slug, is_published, updated_at, published_at
         FROM articles
         ORDER BY updated_at DESC, id DESC'
    );

    return $stmt->fetchAll();
}

function fetch_article_by_id(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    return $article ?: null;
}

function create_article(PDO $pdo, array $payload): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO articles
         (title, slug, summary, content, image_path, image_alt, meta_title, meta_description, is_published, published_at)
         VALUES
         (:title, :slug, :summary, :content, :image_path, :image_alt, :meta_title, :meta_description, :is_published, NOW())'
    );

    $stmt->execute($payload);
}

function update_article(PDO $pdo, int $id, array $payload): void
{
    $payload['id'] = $id;

    $stmt = $pdo->prepare(
        'UPDATE articles
         SET title = :title,
             slug = :slug,
             summary = :summary,
             content = :content,
             image_path = :image_path,
             image_alt = :image_alt,
             meta_title = :meta_title,
             meta_description = :meta_description,
             is_published = :is_published
         WHERE id = :id'
    );

    $stmt->execute($payload);
}

function delete_article(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function normalize_slug(string $input): string
{
    $slug = strtolower(trim($input));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
    $slug = trim($slug, '-');

    return $slug === '' ? 'article-sans-titre' : $slug;
}
