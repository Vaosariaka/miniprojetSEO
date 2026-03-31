-- =============================================================================
--  ACTUMONDE — Schéma de base de données
--  Système de gestion de journal en ligne avec éditeur TinyMCE
--  Version : 1.0 | Moteur : MySQL 8.0+ / MariaDB 10.6+
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================================================
-- 1. UTILISATEURS & RÔLES (Authentication / Autorisations)
-- =============================================================================

CREATE TABLE roles (
    id          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug        VARCHAR(50)      NOT NULL UNIQUE,          -- 'super_admin', 'admin', 'editor', 'author', 'contributor', 'subscriber'
    label       VARCHAR(100)     NOT NULL,                 -- 'Super Administrateur', 'Rédacteur en chef'…
    permissions JSON             NOT NULL DEFAULT ('[]'),  -- tableau de droits granulaires
    created_at  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données initiales
INSERT INTO roles (slug, label, permissions) VALUES
('super_admin',  'Super Administrateur', '["*"]'),
('admin',        'Administrateur',       '["articles.*","categories.*","users.*","media.*","settings.*"]'),
('editor',       'Rédacteur en chef',    '["articles.*","categories.read","media.*"]'),
('author',       'Journaliste',          '["articles.create","articles.update_own","media.upload"]'),
('contributor',  'Contributeur',         '["articles.create_draft"]'),
('subscriber',   'Abonné',               '["articles.read_premium"]');


CREATE TABLE users (
    id              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    role_id         TINYINT UNSIGNED NOT NULL DEFAULT 4,        -- author par défaut
    username        VARCHAR(80)      NOT NULL UNIQUE,
    email           VARCHAR(191)     NOT NULL UNIQUE,
    password_hash   VARCHAR(255)     NOT NULL,
    display_name    VARCHAR(120)     NOT NULL,
    bio             TEXT             DEFAULT NULL,              -- biographie affichée publiquement
    avatar_url      VARCHAR(500)     DEFAULT NULL,
    job_title       VARCHAR(120)     DEFAULT NULL,             -- ex: 'Correspondant Afrique'
    social_links    JSON             DEFAULT NULL,             -- {"twitter":"...", "linkedin":"..."}
    is_active       TINYINT(1)       NOT NULL DEFAULT 1,
    email_verified  TINYINT(1)       NOT NULL DEFAULT 0,
    last_login_at   TIMESTAMP         ,
    created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_users_role   (role_id),
    INDEX idx_users_email  (email),
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 2. TAXONOMIE (Catégories, Tags, Régions)
-- =============================================================================

CREATE TABLE categories (
    id            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_id     SMALLINT UNSIGNED DEFAULT NULL,             -- sous-catégorie possible
    slug          VARCHAR(120)      NOT NULL UNIQUE,
    name          VARCHAR(120)      NOT NULL,
    description   TEXT              DEFAULT NULL,
    color         CHAR(7)           DEFAULT '#b5452a',        -- couleur hex pour le badge
    icon          VARCHAR(60)       DEFAULT NULL,             -- classe Font Awesome ex: 'fa-globe-africa'
    meta_title    VARCHAR(200)      DEFAULT NULL,
    meta_desc     VARCHAR(400)      DEFAULT NULL,
    sort_order    SMALLINT          NOT NULL DEFAULT 0,
    is_active     TINYINT(1)        NOT NULL DEFAULT 1,
    created_at    TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cat_parent (parent_id),
    INDEX idx_cat_slug   (slug),
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories (slug, name, color, icon, sort_order) VALUES
('afrique',        'Afrique',        '#5c6b5a', 'fa-globe-africa',  1),
('ameriques',      'Amériques',      '#3d5a73', 'fa-globe-americas',2),
('europe',         'Europe',         '#b5452a', 'fa-globe-europe',  3),
('asie-pacifique', 'Asie-Pacifique', '#8b7355', 'fa-globe-asia',    4),
('moyen-orient',   'Moyen-Orient',   '#c9a84c', 'fa-mosque',        5),
('economie',       'Économie',       '#3d5a73', 'fa-chart-line',    6),
('science-tech',   'Science & Tech', '#5c6b5a', 'fa-microchip',     7),
('culture',        'Culture',        '#8b7355', 'fa-palette',       8),
('opinion',        'Opinion',        '#b5452a', 'fa-pen-nib',       9),
('sport',          'Sport',          '#3d5a73', 'fa-futbol',       10);


CREATE TABLE tags (
    id         MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug       VARCHAR(120)       NOT NULL UNIQUE,
    name       VARCHAR(120)       NOT NULL,
    created_at TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_tags_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE regions (
    id         SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug       VARCHAR(80)       NOT NULL UNIQUE,
    name       VARCHAR(100)      NOT NULL,
    flag_emoji CHAR(8)           DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO regions (slug, name, flag_emoji) VALUES
('afrique-subsaharienne', 'Afrique subsaharienne', '🌍'),
('afrique-nord',          'Afrique du Nord',       '🌍'),
('moyen-orient',          'Moyen-Orient',           '🕌'),
('europe-ouest',          'Europe de l\'Ouest',    '🌍'),
('europe-est',            'Europe de l\'Est',      '🌍'),
('amerique-nord',         'Amérique du Nord',      '🌎'),
('amerique-latine',       'Amérique latine',       '🌎'),
('asie-est',              'Asie de l\'Est',        '🌏'),
('asie-sud',              'Asie du Sud',           '🌏'),
('asie-sud-est',          'Asie du Sud-Est',       '🌏'),
('oceanie',               'Océanie',               '🌏');


-- =============================================================================
-- 3. MÉDIAS (Bibliothèque de fichiers)
-- =============================================================================

CREATE TABLE media (
    id            BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    uploaded_by   BIGINT UNSIGNED  NOT NULL,
    type          ENUM('image','video','audio','document','embed') NOT NULL DEFAULT 'image',
    filename      VARCHAR(255)     NOT NULL,              -- nom stocké sur le serveur
    original_name VARCHAR(255)     NOT NULL,              -- nom d'origine
    mime_type     VARCHAR(100)     NOT NULL,
    file_path     VARCHAR(500)     NOT NULL,              -- chemin relatif : /uploads/2025/06/photo.webp
    file_size     INT UNSIGNED     NOT NULL DEFAULT 0,    -- octets
    width         SMALLINT UNSIGNED DEFAULT NULL,         -- px (images)
    height        SMALLINT UNSIGNED DEFAULT NULL,
    alt_text      VARCHAR(400)     DEFAULT NULL,
    caption       VARCHAR(600)     DEFAULT NULL,
    credit        VARCHAR(200)     DEFAULT NULL,          -- © AFP / Reuters …
    meta          JSON             DEFAULT NULL,          -- données EXIF, durée vidéo…
    created_at    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_media_uploader (uploaded_by),
    INDEX idx_media_type     (type),
    CONSTRAINT fk_media_user FOREIGN KEY (uploaded_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 4. ARTICLES (cœur du système — stockage HTML TinyMCE)
-- =============================================================================

CREATE TABLE articles (
    id                BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    -- Auteurs
    author_id         BIGINT UNSIGNED   NOT NULL,
    editor_id         BIGINT UNSIGNED   DEFAULT NULL,              -- rédacteur ayant validé

    -- Classification
    category_id       SMALLINT UNSIGNED DEFAULT NULL,
    region_id         SMALLINT UNSIGNED DEFAULT NULL,
    featured_media_id BIGINT UNSIGNED   DEFAULT NULL,              -- image à la une (FK → media)

    -- Identifiants
    slug              VARCHAR(255)      NOT NULL UNIQUE,

    -- Contenu (TinyMCE → HTML stocké en LONGTEXT)
    title             VARCHAR(500)      NOT NULL,
    subtitle          VARCHAR(700)      DEFAULT NULL,              -- chapeau / accroche
    content_html      LONGTEXT          NOT NULL,                  -- ← sortie brute TinyMCE
    content_plain     LONGTEXT          DEFAULT NULL,              -- version texte seul (pour recherche)
    excerpt           TEXT              DEFAULT NULL,              -- résumé court (~300 car.)
    reading_time_min  TINYINT UNSIGNED  DEFAULT NULL,              -- calculé automatiquement

    -- État de publication
    status            ENUM('draft','pending_review','scheduled','published','archived','trash')
                                        NOT NULL DEFAULT 'draft',
    is_breaking       TINYINT(1)        NOT NULL DEFAULT 0,        -- flash info
    is_premium        TINYINT(1)        NOT NULL DEFAULT 0,        -- réservé abonnés
    is_featured       TINYINT(1)        NOT NULL DEFAULT 0,        -- mis en avant (hero)
    is_pinned         TINYINT(1)        NOT NULL DEFAULT 0,        -- épinglé en tête de rubrique

    -- Dates
    published_at      TIMESTAMP          ,              -- NULL = non publié
    scheduled_at      TIMESTAMP          ,              -- publication différée
    created_at        TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- SEO
    meta_title        VARCHAR(200)      DEFAULT NULL,
    meta_description  VARCHAR(400)      DEFAULT NULL,
    og_image_url      VARCHAR(500)      DEFAULT NULL,              -- Open Graph image
    canonical_url     VARCHAR(500)      DEFAULT NULL,

    -- Statistiques (dénormalisées pour performance)
    view_count        BIGINT UNSIGNED   NOT NULL DEFAULT 0,
    share_count       INT UNSIGNED      NOT NULL DEFAULT 0,
    comment_count     INT UNSIGNED      NOT NULL DEFAULT 0,

    -- Versionning
    revision_number   SMALLINT UNSIGNED NOT NULL DEFAULT 1,

    PRIMARY KEY (id),
    INDEX idx_art_status       (status),
    INDEX idx_art_published    (published_at DESC),
    INDEX idx_art_author       (author_id),
    INDEX idx_art_category     (category_id),
    INDEX idx_art_region       (region_id),
    INDEX idx_art_featured     (is_featured),
    INDEX idx_art_breaking     (is_breaking),
    FULLTEXT idx_art_search    (title, content_plain, excerpt),

    CONSTRAINT fk_art_author   FOREIGN KEY (author_id)         REFERENCES users(id),
    CONSTRAINT fk_art_editor   FOREIGN KEY (editor_id)         REFERENCES users(id),
    CONSTRAINT fk_art_cat      FOREIGN KEY (category_id)       REFERENCES categories(id)   ON DELETE SET NULL,
    CONSTRAINT fk_art_region   FOREIGN KEY (region_id)         REFERENCES regions(id)      ON DELETE SET NULL,
    CONSTRAINT fk_art_media    FOREIGN KEY (featured_media_id) REFERENCES media(id)        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 5. RÉVISIONS D'ARTICLES (historique des versions TinyMCE)
-- =============================================================================

CREATE TABLE article_revisions (
    id              BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    article_id      BIGINT UNSIGNED  NOT NULL,
    revised_by      BIGINT UNSIGNED  NOT NULL,
    revision_number SMALLINT UNSIGNED NOT NULL,
    title           VARCHAR(500)     NOT NULL,
    content_html    LONGTEXT         NOT NULL,             -- snapshot HTML TinyMCE
    change_summary  VARCHAR(400)     DEFAULT NULL,         -- note du rédacteur
    created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_rev_article (article_id, revision_number DESC),
    CONSTRAINT fk_rev_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_rev_user   FOREIGN KEY (revised_by)  REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 6. RELATIONS N:N
-- =============================================================================

-- Articles ↔ Tags
CREATE TABLE article_tags (
    article_id BIGINT UNSIGNED    NOT NULL,
    tag_id     MEDIUMINT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    CONSTRAINT fk_at_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_at_tag     FOREIGN KEY (tag_id)     REFERENCES tags(id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Articles liés (suggestions "À lire aussi")
CREATE TABLE article_related (
    article_id BIGINT UNSIGNED NOT NULL,
    related_id BIGINT UNSIGNED NOT NULL,
    sort_order TINYINT         NOT NULL DEFAULT 0,
    PRIMARY KEY (article_id, related_id),
    CONSTRAINT fk_ar_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_ar_related FOREIGN KEY (related_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Articles ↔ Médias (galerie interne, images dans le HTML TinyMCE)
CREATE TABLE article_media (
    article_id BIGINT UNSIGNED NOT NULL,
    media_id   BIGINT UNSIGNED NOT NULL,
    context    ENUM('gallery','inline','featured','video') NOT NULL DEFAULT 'inline',
    sort_order TINYINT         NOT NULL DEFAULT 0,
    PRIMARY KEY (article_id, media_id),
    CONSTRAINT fk_am_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_am_media   FOREIGN KEY (media_id)   REFERENCES media(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Co-auteurs
CREATE TABLE article_authors (
    article_id BIGINT UNSIGNED NOT NULL,
    user_id    BIGINT UNSIGNED NOT NULL,
    role       ENUM('author','co_author','contributor','translator') NOT NULL DEFAULT 'co_author',
    PRIMARY KEY (article_id, user_id),
    CONSTRAINT fk_aa_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_aa_user    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 7. NEWSLETTERS
-- =============================================================================

CREATE TABLE newsletter_lists (
    id          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug        VARCHAR(80)       NOT NULL UNIQUE,
    name        VARCHAR(150)      NOT NULL,
    description TEXT              DEFAULT NULL,
    frequency   ENUM('daily','weekly','breaking') NOT NULL DEFAULT 'daily',
    is_active   TINYINT(1)        NOT NULL DEFAULT 1,
    created_at  TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO newsletter_lists (slug, name, frequency) VALUES
('matinale',  'La Matinale — Sélection du jour',   'daily'),
('hebdo',     'L\'Hebdo Monde',                    'weekly'),
('flash',     'Flash Info — Alertes en temps réel','breaking');


CREATE TABLE newsletter_subscribers (
    id            BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    email         VARCHAR(191)      NOT NULL,
    first_name    VARCHAR(100)      DEFAULT NULL,
    status        ENUM('pending','active','unsubscribed','bounced') NOT NULL DEFAULT 'pending',
    source        VARCHAR(100)      DEFAULT 'website',  -- d'où vient l'inscription
    token         VARCHAR(128)      DEFAULT NULL,       -- token de confirmation/désabonnement
    confirmed_at  TIMESTAMP         ,
    created_at    TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE INDEX idx_ns_email (email),
    INDEX idx_ns_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE newsletter_subscriptions (
    subscriber_id BIGINT UNSIGNED   NOT NULL,
    list_id       SMALLINT UNSIGNED NOT NULL,
    subscribed_at TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (subscriber_id, list_id),
    CONSTRAINT fk_nsub_subscriber FOREIGN KEY (subscriber_id) REFERENCES newsletter_subscribers(id) ON DELETE CASCADE,
    CONSTRAINT fk_nsub_list       FOREIGN KEY (list_id)       REFERENCES newsletter_lists(id)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE newsletter_campaigns (
    id            BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    list_id       SMALLINT UNSIGNED NOT NULL,
    created_by    BIGINT UNSIGNED   NOT NULL,
    subject       VARCHAR(300)      NOT NULL,
    preheader     VARCHAR(200)      DEFAULT NULL,
    content_html  LONGTEXT          NOT NULL,             -- ← HTML TinyMCE de la newsletter
    status        ENUM('draft','scheduled','sending','sent','cancelled') NOT NULL DEFAULT 'draft',
    scheduled_at  TIMESTAMP         ,
    sent_at       TIMESTAMP         ,
    stats         JSON              DEFAULT NULL,         -- {"sent":0,"opened":0,"clicked":0,"bounced":0}
    created_at    TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_nc_list   (list_id),
    INDEX idx_nc_status (status),
    CONSTRAINT fk_nc_list    FOREIGN KEY (list_id)    REFERENCES newsletter_lists(id),
    CONSTRAINT fk_nc_creator FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 8. COMMENTAIRES
-- =============================================================================

CREATE TABLE comments (
    id          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    article_id  BIGINT UNSIGNED  NOT NULL,
    user_id     BIGINT UNSIGNED  DEFAULT NULL,             -- NULL = invité
    parent_id   BIGINT UNSIGNED  DEFAULT NULL,             -- réponse à un commentaire
    guest_name  VARCHAR(120)     DEFAULT NULL,
    guest_email VARCHAR(191)     DEFAULT NULL,
    content     TEXT             NOT NULL,
    status      ENUM('pending','approved','spam','trash') NOT NULL DEFAULT 'pending',
    ip_address  VARCHAR(45)      DEFAULT NULL,
    created_at  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cmt_article (article_id),
    INDEX idx_cmt_parent  (parent_id),
    INDEX idx_cmt_status  (status),
    CONSTRAINT fk_cmt_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_cmt_user    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE SET NULL,
    CONSTRAINT fk_cmt_parent  FOREIGN KEY (parent_id)  REFERENCES comments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 9. BREAKING NEWS & TICKER
-- =============================================================================

CREATE TABLE breaking_news (
    id          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_by  BIGINT UNSIGNED   NOT NULL,
    text        VARCHAR(500)      NOT NULL,               -- texte du ticker
    article_id  BIGINT UNSIGNED   DEFAULT NULL,           -- lien optionnel vers un article
    is_active   TINYINT(1)        NOT NULL DEFAULT 1,
    expires_at  TIMESTAMP         ,
    created_at  TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_bn_active (is_active, expires_at),
    CONSTRAINT fk_bn_creator FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT fk_bn_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 10. PAGES STATIQUES (À propos, Mentions légales… aussi via TinyMCE)
-- =============================================================================

CREATE TABLE pages (
    id           SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    created_by   BIGINT UNSIGNED   NOT NULL,
    slug         VARCHAR(200)      NOT NULL UNIQUE,
    title        VARCHAR(300)      NOT NULL,
    content_html LONGTEXT          NOT NULL,              -- ← HTML TinyMCE
    status       ENUM('draft','published') NOT NULL DEFAULT 'draft',
    meta_title   VARCHAR(200)      DEFAULT NULL,
    meta_desc    VARCHAR(400)      DEFAULT NULL,
    show_in_nav  TINYINT(1)        NOT NULL DEFAULT 0,
    sort_order   SMALLINT          NOT NULL DEFAULT 0,
    created_at   TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_pages_creator FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 11. STATISTIQUES (Analytiques légères)
-- =============================================================================

CREATE TABLE article_views (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    article_id   BIGINT UNSIGNED NOT NULL,
    user_id      BIGINT UNSIGNED DEFAULT NULL,
    session_id   VARCHAR(64)     DEFAULT NULL,            -- empreinte anonyme
    ip_address   VARCHAR(45)     DEFAULT NULL,
    referer      VARCHAR(500)    DEFAULT NULL,
    user_agent   VARCHAR(500)    DEFAULT NULL,
    viewed_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_av_article (article_id),
    INDEX idx_av_date    (viewed_at),
    CONSTRAINT fk_av_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE article_shares (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    article_id  BIGINT UNSIGNED NOT NULL,
    platform    ENUM('facebook','twitter','whatsapp','telegram','email','copy','linkedin','other') NOT NULL,
    shared_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_as_article (article_id),
    CONSTRAINT fk_as_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 12. JOURNAUX DE MODÉRATION & ACTIVITÉ
-- =============================================================================

CREATE TABLE activity_logs (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id      BIGINT UNSIGNED DEFAULT NULL,
    action       VARCHAR(100)    NOT NULL,                -- 'article.published', 'comment.approved'…
    target_type  VARCHAR(60)     DEFAULT NULL,            -- 'article', 'comment', 'user'…
    target_id    BIGINT UNSIGNED DEFAULT NULL,
    metadata     JSON            DEFAULT NULL,            -- données contextuelles
    ip_address   VARCHAR(45)     DEFAULT NULL,
    created_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_log_user   (user_id),
    INDEX idx_log_action (action),
    INDEX idx_log_date   (created_at),
    CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================================================
-- 13. PARAMÈTRES GLOBAUX DU SITE
-- =============================================================================

CREATE TABLE settings (
    setting_key   VARCHAR(120)  NOT NULL,
    setting_value LONGTEXT      DEFAULT NULL,             -- peut stocker du JSON ou du HTML TinyMCE
    setting_type  ENUM('string','integer','boolean','json','html') NOT NULL DEFAULT 'string',
    group_name    VARCHAR(80)   NOT NULL DEFAULT 'general',
    label         VARCHAR(200)  DEFAULT NULL,
    updated_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (setting_key, setting_value, setting_type, group_name, label) VALUES
('site_name',            'ActuMonde',                         'string',  'general',  'Nom du site'),
('site_tagline',         'L\'information internationale, en continu', 'string', 'general', 'Sous-titre'),
('site_url',             'https://actumonde.fr',              'string',  'general',  'URL du site'),
('logo_url',             '',                                  'string',  'general',  'Logo'),
('articles_per_page',    '12',                                'integer', 'display',  'Articles par page'),
('comments_enabled',     '1',                                 'boolean', 'comments', 'Commentaires activés'),
('comments_moderated',   '1',                                 'boolean', 'comments', 'Modération avant publication'),
('tinymce_api_key',      '',                                  'string',  'editor',   'Clé API TinyMCE'),
('meta_default_image',   '',                                  'string',  'seo',      'Image Open Graph par défaut'),
('google_analytics_id',  '',                                  'string',  'analytics','ID Google Analytics'),
('maintenance_mode',     '0',                                 'boolean', 'general',  'Mode maintenance'),
('ticker_enabled',       '1',                                 'boolean', 'display',  'Ticker actif');


-- =============================================================================
-- 14. VUES UTILES
-- =============================================================================

-- Vue : articles publiés avec toutes les métadonnées utiles
CREATE OR REPLACE VIEW v_articles_published AS
SELECT
    a.id,
    a.slug,
    a.title,
    a.subtitle,
    a.excerpt,
    a.content_html,
    a.reading_time_min,
    a.is_breaking,
    a.is_premium,
    a.is_featured,
    a.published_at,
    a.view_count,
    a.share_count,
    a.comment_count,
    -- Auteur
    u.display_name   AS author_name,
    u.job_title      AS author_title,
    u.avatar_url     AS author_avatar,
    -- Catégorie
    c.name           AS category_name,
    c.slug           AS category_slug,
    c.color          AS category_color,
    c.icon           AS category_icon,
    -- Région
    r.name           AS region_name,
    r.flag_emoji     AS region_flag,
    -- Média à la une
    m.file_path      AS featured_image_path,
    m.alt_text       AS featured_image_alt,
    m.credit         AS featured_image_credit
FROM articles a
JOIN users      u ON u.id = a.author_id
LEFT JOIN categories c ON c.id = a.category_id
LEFT JOIN regions    r ON r.id = a.region_id
LEFT JOIN media      m ON m.id = a.featured_media_id
WHERE a.status = 'published'
  AND a.published_at <= CURRENT_TIMESTAMP
ORDER BY a.published_at DESC;


-- Vue : articles les plus lus (7 derniers jours)
CREATE OR REPLACE VIEW v_trending_articles AS
SELECT
    a.id,
    a.slug,
    a.title,
    a.excerpt,
    c.name  AS category_name,
    c.color AS category_color,
    COUNT(av.id) AS views_7d
FROM articles a
LEFT JOIN article_views av ON av.article_id = a.id
    AND av.viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
LEFT JOIN categories c ON c.id = a.category_id
WHERE a.status = 'published'
GROUP BY a.id
ORDER BY views_7d DESC
LIMIT 10;


-- Vue : statistiques de la rédaction par auteur
CREATE OR REPLACE VIEW v_author_stats AS
SELECT
    u.id,
    u.display_name,
    u.job_title,
    COUNT(a.id)                                              AS total_articles,
    SUM(CASE WHEN a.status = 'published' THEN 1 ELSE 0 END) AS published,
    SUM(a.view_count)                                        AS total_views,
    MAX(a.published_at)                                      AS last_published_at
FROM users u
LEFT JOIN articles a ON a.author_id = u.id
GROUP BY u.id;


INSERT INTO users 
(role_id, username, email, password_hash, display_name, bio, job_title, is_active, email_verified)
VALUES

(1, 'superadmin', 'admin@actumonde.fr', 
'$2y$10$examplehashxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
'Super Admin', 
'Administrateur principal du site', 
'CTO', 
1, 1)
