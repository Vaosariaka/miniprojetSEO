<?php

declare(strict_types=1);

// =============================================================================
// FONCTIONS UTILITAIRES DE BASE
// =============================================================================

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
    
    // Vérifier que l'utilisateur a le rôle admin ou supérieur
    if (!has_role(['super_admin', 'admin', 'editor'])) {
        redirect_to('/admin/unauthorized');
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

// =============================================================================
// FONCTIONS D'AUTHENTIFICATION ET RÔLES
// =============================================================================

/**
 * Vérifie si l'utilisateur connecté a un rôle spécifique
 */
function has_role(array $allowedRoles): bool
{
    if (!isset($_SESSION['user_role_slug'])) {
        return false;
    }
    
    return in_array($_SESSION['user_role_slug'], $allowedRoles);
}

/**
 * Vérifie si l'utilisateur a une permission spécifique
 */
function has_permission(PDO $pdo, int $userId, string $permission): bool
{
    // Récupérer les permissions du rôle de l'utilisateur
    $stmt = $pdo->prepare(
        'SELECT r.permissions 
         FROM users u 
         JOIN roles r ON u.role_id = r.id 
         WHERE u.id = :user_id'
    );
    $stmt->execute(['user_id' => $userId]);
    $result = $stmt->fetch();
    
    if (!$result) {
        return false;
    }
    
    $permissions = json_decode($result['permissions'], true) ?? [];
    
    // Super admin a toutes les permissions
    if (in_array('*', $permissions)) {
        return true;
    }
    
    // Vérifier la permission demandée (supporte wildcard comme "articles.*")
    foreach ($permissions as $perm) {
        if ($perm === $permission) {
            return true;
        }
        if (str_ends_with($perm, '.*') && str_starts_with($permission, rtrim($perm, '*') . rtrim($perm, '.*'))) {
            return true;
        }
    }
    
    return false;
}

// =============================================================================
// INITIALISATION DE LA BASE DE DONNÉES
// =============================================================================

function initialize_database(PDO $pdo): void
{
    // Note: Les tables sont déjà créées par le script SQL fourni
    // Cette fonction sert à vérifier l'existence des données minimales
    
    // Vérifier si la table roles contient des données
    $stmt = $pdo->query('SELECT COUNT(*) FROM roles');
    if ($stmt->fetchColumn() == 0) {
        // Réinsérer les rôles par défaut
        $pdo->exec(
            "INSERT INTO roles (slug, label, permissions) VALUES
            ('super_admin', 'Super Administrateur', '[\"*\"]'),
            ('admin', 'Administrateur', '[\"articles.*\",\"categories.*\",\"users.*\",\"media.*\",\"settings.*\"]'),
            ('editor', 'Rédacteur en chef', '[\"articles.*\",\"categories.read\",\"media.*\"]'),
            ('author', 'Journaliste', '[\"articles.create\",\"articles.update_own\",\"media.upload\"]'),
            ('contributor', 'Contributeur', '[\"articles.create_draft\"]'),
            ('subscriber', 'Abonné', '[\"articles.read_premium\"]')"
        );
    }
    
    // Vérifier si les catégories existent
    $stmt = $pdo->query('SELECT COUNT(*) FROM categories');
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec(
            "INSERT INTO categories (slug, name, color, icon, sort_order) VALUES
            ('afrique', 'Afrique', '#5c6b5a', 'fa-globe-africa', 1),
            ('ameriques', 'Amériques', '#3d5a73', 'fa-globe-americas', 2),
            ('europe', 'Europe', '#b5452a', 'fa-globe-europe', 3),
            ('asie-pacifique', 'Asie-Pacifique', '#8b7355', 'fa-globe-asia', 4),
            ('moyen-orient', 'Moyen-Orient', '#c9a84c', 'fa-mosque', 5),
            ('economie', 'Économie', '#3d5a73', 'fa-chart-line', 6),
            ('science-tech', 'Science & Tech', '#5c6b5a', 'fa-microchip', 7),
            ('culture', 'Culture', '#8b7355', 'fa-palette', 8),
            ('opinion', 'Opinion', '#b5452a', 'fa-pen-nib', 9),
            ('sport', 'Sport', '#3d5a73', 'fa-futbol', 10)"
        );
    }
    
    // Vérifier les régions
    $stmt = $pdo->query('SELECT COUNT(*) FROM regions');
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec(
            "INSERT INTO regions (slug, name, flag_emoji) VALUES
            ('afrique-subsaharienne', 'Afrique subsaharienne', '🌍'),
            ('afrique-nord', 'Afrique du Nord', '🌍'),
            ('moyen-orient', 'Moyen-Orient', '🕌'),
            ('europe-ouest', 'Europe de l\'Ouest', '🌍'),
            ('europe-est', 'Europe de l\'Est', '🌍'),
            ('amerique-nord', 'Amérique du Nord', '🌎'),
            ('amerique-latine', 'Amérique latine', '🌎'),
            ('asie-est', 'Asie de l\'Est', '🌏'),
            ('asie-sud', 'Asie du Sud', '🌏'),
            ('asie-sud-est', 'Asie du Sud-Est', '🌏'),
            ('oceanie', 'Océanie', '🌏')"
        );
    }
}

function ensure_default_admin(PDO $pdo): void
{
    $checkStmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $checkStmt->execute(['username' => 'superadmin']);

    if ($checkStmt->fetch()) {
        return;
    }

    // Récupérer l'ID du rôle super_admin
    $roleStmt = $pdo->prepare('SELECT id FROM roles WHERE slug = :slug LIMIT 1');
    $roleStmt->execute(['slug' => 'super_admin']);
    $role = $roleStmt->fetch();
    $roleId = $role ? $role['id'] : 1;

    $insertStmt = $pdo->prepare(
        'INSERT INTO users (role_id, username, email, password_hash, display_name, job_title, is_active, email_verified) 
         VALUES (:role_id, :username, :email, :password_hash, :display_name, :job_title, 1, 1)'
    );

    $insertStmt->execute([
        'role_id' => $roleId,
        'username' => 'superadmin',
        'email' => 'admin@actumonde.fr',
        'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
        'display_name' => 'Super Administrateur',
        'job_title' => 'Administrateur principal'
    ]);
}

// =============================================================================
// FONCTIONS ARTICLES (adaptées à la nouvelle structure)
// =============================================================================

function fetch_published_articles(PDO $pdo, int $limit = 12, ?int $categoryId = null): array
{
    $sql = '
        SELECT 
            a.id, 
            a.title, 
            a.slug, 
            a.excerpt as summary,
            a.content_html,
            a.published_at,
            a.is_breaking,
            a.is_featured,
            a.is_premium,
            a.view_count,
            a.comment_count,
            c.name as category_name,
            c.slug as category_slug,
            c.color as category_color,
            u.display_name as author_name,
            m.file_path as image_path,
            m.alt_text as image_alt
        FROM articles a
        LEFT JOIN categories c ON c.id = a.category_id
        LEFT JOIN users u ON u.id = a.author_id
        LEFT JOIN media m ON m.id = a.featured_media_id
        WHERE a.status = "published"
          AND a.published_at <= CURRENT_TIMESTAMP
    ';
    
    $params = [];
    if ($categoryId) {
        $sql .= ' AND a.category_id = :category_id';
        $params['category_id'] = $categoryId;
    }
    
    $sql .= ' ORDER BY a.published_at DESC, a.id DESC LIMIT :limit';
    $params['limit'] = $limit;
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();

    return $stmt->fetchAll();
}

function fetch_featured_articles(PDO $pdo, int $limit = 3): array
{
    $stmt = $pdo->prepare('
        SELECT 
            a.id, 
            a.title, 
            a.slug, 
            a.subtitle,
            a.excerpt as summary,
            a.content_html,
            a.published_at,
            c.name as category_name,
            c.slug as category_slug,
            c.color as category_color,
            u.display_name as author_name,
            m.file_path as image_path,
            m.alt_text as image_alt
        FROM articles a
        LEFT JOIN categories c ON c.id = a.category_id
        LEFT JOIN users u ON u.id = a.author_id
        LEFT JOIN media m ON m.id = a.featured_media_id
        WHERE a.status = "published"
          AND a.is_featured = 1
          AND a.published_at <= CURRENT_TIMESTAMP
        ORDER BY a.published_at DESC
        LIMIT :limit
    ');
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function fetch_breaking_news(PDO $pdo): array
{
    $stmt = $pdo->query('
        SELECT 
            a.id, 
            a.title, 
            a.slug,
            a.is_breaking
        FROM articles a
        WHERE a.status = "published"
          AND a.is_breaking = 1
          AND a.published_at <= CURRENT_TIMESTAMP
        ORDER BY a.published_at DESC
        LIMIT 5
    ');
    
    return $stmt->fetchAll();
}

function fetch_article_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('
        SELECT 
            a.*,
            a.content_html as content,
            a.excerpt as summary,
            a.image_path,
            a.image_alt,
            c.id as category_id,
            c.name as category_name,
            c.slug as category_slug,
            c.color as category_color,
            u.id as author_id,
            u.display_name as author_name,
            u.job_title as author_title,
            u.avatar_url as author_avatar,
            u.bio as author_bio,
            m.file_path as featured_image_path,
            m.alt_text as featured_image_alt,
            m.credit as featured_image_credit
        FROM articles a
        LEFT JOIN categories c ON c.id = a.category_id
        LEFT JOIN users u ON u.id = a.author_id
        LEFT JOIN media m ON m.id = a.featured_media_id
        WHERE a.slug = :slug 
          AND a.status = "published"
          AND a.published_at <= CURRENT_TIMESTAMP
        LIMIT 1
    ');
    $stmt->execute(['slug' => $slug]);
    $article = $stmt->fetch();

    return $article ?: null;
}

function fetch_all_articles_admin(PDO $pdo, string $status = null): array
{
    $sql = '
        SELECT 
            a.id, 
            a.title, 
            a.slug, 
            a.status,
            a.is_featured,
            a.is_breaking,
            a.published_at,
            a.updated_at,
            a.view_count,
            c.name as category_name,
            u.display_name as author_name
        FROM articles a
        LEFT JOIN categories c ON c.id = a.category_id
        LEFT JOIN users u ON u.id = a.author_id
    ';
    
    if ($status) {
        $sql .= ' WHERE a.status = :status';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['status' => $status]);
    } else {
        $sql .= ' ORDER BY a.updated_at DESC, a.id DESC';
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}

function fetch_article_by_id(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('
        SELECT 
            a.*,
            a.content_html as content,
            a.excerpt as summary,
            c.id as category_id,
            c.name as category_name,
            u.id as author_id,
            u.display_name as author_name,
            m.file_path as image_path,
            m.alt_text as image_alt
        FROM articles a
        LEFT JOIN categories c ON c.id = a.category_id
        LEFT JOIN users u ON u.id = a.author_id
        LEFT JOIN media m ON m.id = a.featured_media_id
        WHERE a.id = :id
        LIMIT 1
    ');
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    return $article ?: null;
}

function create_article(PDO $pdo, array $payload): int
{
    $stmt = $pdo->prepare('
        INSERT INTO articles (
            author_id,
            category_id,
            region_id,
            slug,
            title,
            subtitle,
            content_html,
            excerpt,
            status,
            is_breaking,
            is_featured,
            is_premium,
            meta_title,
            meta_description,
            published_at,
            featured_media_id
        ) VALUES (
            :author_id,
            :category_id,
            :region_id,
            :slug,
            :title,
            :subtitle,
            :content_html,
            :excerpt,
            :status,
            :is_breaking,
            :is_featured,
            :is_premium,
            :meta_title,
            :meta_description,
            :published_at,
            :featured_media_id
        )
    ');
    
    $stmt->execute($payload);
    return (int)$pdo->lastInsertId();
}

function update_article(PDO $pdo, int $id, array $payload): void
{
    $payload['id'] = $id;
    
    $stmt = $pdo->prepare('
        UPDATE articles
        SET 
            category_id = :category_id,
            region_id = :region_id,
            slug = :slug,
            title = :title,
            subtitle = :subtitle,
            content_html = :content_html,
            excerpt = :excerpt,
            status = :status,
            is_breaking = :is_breaking,
            is_featured = :is_featured,
            is_premium = :is_premium,
            meta_title = :meta_title,
            meta_description = :meta_description,
            published_at = :published_at,
            featured_media_id = :featured_media_id,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ');

    $stmt->execute($payload);
}

function delete_article(PDO $pdo, int $id): void
{
    // Soft delete: passer en trash
    $stmt = $pdo->prepare('UPDATE articles SET status = "trash" WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function hard_delete_article(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function increment_article_views(PDO $pdo, int $articleId, ?string $ipAddress = null): void
{
    // Incrémenter le compteur dans articles
    $stmt = $pdo->prepare('UPDATE articles SET view_count = view_count + 1 WHERE id = :id');
    $stmt->execute(['id' => $articleId]);
    
    // Ajouter une entrée dans article_views
    $stmt = $pdo->prepare('
        INSERT INTO article_views (article_id, ip_address, user_agent, viewed_at)
        VALUES (:article_id, :ip_address, :user_agent, NOW())
    ');
    $stmt->execute([
        'article_id' => $articleId,
        'ip_address' => $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}

// =============================================================================
// FONCTIONS CATÉGORIES
// =============================================================================

function fetch_all_categories(PDO $pdo, bool $onlyActive = true): array
{
    $sql = 'SELECT * FROM categories';
    if ($onlyActive) {
        $sql .= ' WHERE is_active = 1';
    }
    $sql .= ' ORDER BY sort_order ASC, name ASC';
    
    return $pdo->query($sql)->fetchAll();
}

function fetch_category_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE slug = :slug AND is_active = 1 LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $category = $stmt->fetch();
    
    return $category ?: null;
}

function fetch_category_by_id(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $category = $stmt->fetch();
    
    return $category ?: null;
}

function get_articles_by_category(PDO $pdo, int $categoryId, int $limit = 10): array
{
    $stmt = $pdo->prepare('
        SELECT 
            a.id, 
            a.title, 
            a.slug, 
            a.excerpt as summary,
            a.published_at,
            u.display_name as author_name,
            m.file_path as image_path
        FROM articles a
        LEFT JOIN users u ON u.id = a.author_id
        LEFT JOIN media m ON m.id = a.featured_media_id
        WHERE a.category_id = :category_id
          AND a.status = "published"
          AND a.published_at <= CURRENT_TIMESTAMP
        ORDER BY a.published_at DESC
        LIMIT :limit
    ');
    $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// =============================================================================
// FONCTIONS MÉDIAS
// =============================================================================

function upload_media(PDO $pdo, int $userId, array $file, string $targetDir = '/uploads/'): ?array
{
    // Vérifier les erreurs
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Types autorisés
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4'];
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }
    
    // Taille max: 10MB
    if ($file['size'] > 10 * 1024 * 1024) {
        return null;
    }
    
    // Générer un nom unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    
    // Créer le dossier par date
    $dateFolder = date('Y/m');
    $fullDir = $_SERVER['DOCUMENT_ROOT'] . $targetDir . $dateFolder;
    if (!is_dir($fullDir)) {
        mkdir($fullDir, 0755, true);
    }
    
    $filePath = $targetDir . $dateFolder . '/' . $filename;
    $fullPath = $fullDir . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
        return null;
    }
    
    // Obtenir les dimensions pour les images
    $width = null;
    $height = null;
    if (str_starts_with($file['type'], 'image/')) {
        list($width, $height) = getimagesize($fullPath);
    }
    
    // Insérer en base
    $stmt = $pdo->prepare('
        INSERT INTO media (uploaded_by, type, filename, original_name, mime_type, file_path, file_size, width, height)
        VALUES (:uploaded_by, :type, :filename, :original_name, :mime_type, :file_path, :file_size, :width, :height)
    ');
    
    $type = str_starts_with($file['type'], 'image/') ? 'image' : 'video';
    
    $stmt->execute([
        'uploaded_by' => $userId,
        'type' => $type,
        'filename' => $filename,
        'original_name' => $file['name'],
        'mime_type' => $file['type'],
        'file_path' => $filePath,
        'file_size' => $file['size'],
        'width' => $width,
        'height' => $height
    ]);
    
    $mediaId = $pdo->lastInsertId();
    
    // Récupérer le média créé
    $stmt = $pdo->prepare('SELECT * FROM media WHERE id = :id');
    $stmt->execute(['id' => $mediaId]);
    
    return $stmt->fetch() ?: null;
}

// =============================================================================
// FONCTIONS UTILITAIRES
// =============================================================================

function normalize_slug1(string $input): string
{
    $slug = strtolower(trim($input));
    $slug = str_replace(
        ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'û', 'ü', 'ç', ' ', "'"],
        ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'c', '-', '-'],
        $slug
    );
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug) ?? '';
    $slug = trim($slug, '-');
    $slug = preg_replace('/-+/', '-', $slug) ?? '';

    return $slug === '' ? 'article-sans-titre' : $slug;
}

function calculate_reading_time(string $html): int
{
    // Supprimer les balises HTML
    $text = strip_tags($html);
    // Compter les mots (environ 200 mots par minute)
    $wordCount = str_word_count($text, 0, 'àâäéèêëîïôöûüç');
    return max(1, (int)ceil($wordCount / 200));
}