<?php

function home($pdo) {
    $articles = fetch_published_articles($pdo, 12);
    $GLOBALS['front_articles'] = $articles;
    $articles = array_slice($articles, 1);

    require __DIR__ . '/../views/home.php';
}

function showArticle($pdo, string $slug) {
    $article = fetch_article_by_slug($pdo, $slug);

    if (!$article) {
        http_response_code(404);
        echo '<h2>404 - Article non trouve</h2>';
        return;
    }

    require __DIR__ . '/../views/article.php';
}

function backend(){
    require __DIR__ . '/../views/backoffice.php';
}

function saveArticle($pdo) {

    header('Content-Type: application/json');

    // 🔍 DÉBOGAGE : Voir ce qui est réellement reçu
    error_log('=== DONNÉES REÇUES ===');
    error_log(print_r($_POST, true));

    // 1. Récupération des données avec les BONS noms (ceux du formulaire)
    $title           = $_POST['title'] ?? '';
    $slug            = $_POST['slug'] ?? '';
    $excerpt         = $_POST['excerpt'] ?? '';      // ⚠️ 'excerpt' et non 'summary'
    $content_html    = $_POST['content_html'] ?? '';  // ⚠️ 'content_html' et non 'content'
    $subtitle        = $_POST['subtitle'] ?? '';
    $category_id     = $_POST['category_id'] ?? null;
    $region_id       = $_POST['region_id'] ?? null;
    $status          = $_POST['status'] ?? 'draft';
    $published_at    = $_POST['published_at'] ?? null;
    $featured_media_id = $_POST['featured_media_id'] ?? null;
    $is_breaking     = $_POST['is_breaking'] ?? 0;
    $is_featured     = $_POST['is_featured'] ?? 0;
    $is_premium      = $_POST['is_premium'] ?? 0;
    $is_pinned       = $_POST['is_pinned'] ?? 0;
    $meta_title      = $_POST['meta_title'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $reading_time_min = $_POST['reading_time_min'] ?? null;
    
    $author_id = $_SESSION['user_id'] ?? 1; // Utilisateur connecté

    // 2. Validation - Vérifier les champs OBLIGATOIRES
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Le titre est obligatoire";
    }
    
    if (empty($slug)) {
        // Générer un slug automatiquement si vide
        $slug = normalize_slug($title);
    }
    
    if (empty($excerpt)) {
        $errors[] = "Le résumé est obligatoire";
    }
    
    if (strlen($excerpt) > 320) {
        $errors[] = "Le résumé ne doit pas dépasser 320 caractères";
    }
    
    if (empty($content_html) || $content_html === '<p><br data-mce-bogus="1"></p>') {
        $errors[] = "Le contenu de l'article est obligatoire";
    }
    
    if (!empty($errors)) {
        echo json_encode([
            "success" => false,
            "message" => "Champs obligatoires manquants ou invalides",
            "errors" => $errors,
            "received" => array_keys($_POST) // Pour déboguer
        ]);
        return;
    }

    try {
        // 3. Calcul automatique du temps de lecture si non fourni
        if (empty($reading_time_min)) {
            $content_plain = strip_tags($content_html);
            $reading_time_min = max(1, ceil(str_word_count($content_plain) / 200));
        }
        
        // Générer le content_plain pour la recherche
        $content_plain = strip_tags($content_html);

        // 4. Requête adaptée à votre table (version complète)
        $sql = "INSERT INTO articles (
                    author_id,
                    category_id,
                    region_id,
                    title,
                    slug,
                    subtitle,
                    excerpt,
                    content_html,
                    content_plain,
                    status,
                    is_breaking,
                    is_featured,
                    is_premium,
                    is_pinned,
                    meta_title,
                    meta_description,
                    reading_time_min,
                    featured_media_id,
                    published_at,
                    created_at
                ) VALUES (
                    :author_id,
                    :category_id,
                    :region_id,
                    :title,
                    :slug,
                    :subtitle,
                    :excerpt,
                    :content_html,
                    :content_plain,
                    :status,
                    :is_breaking,
                    :is_featured,
                    :is_premium,
                    :is_pinned,
                    :meta_title,
                    :meta_description,
                    :reading_time_min,
                    :featured_media_id,
                    IF(:use_now_for_published = 1, NOW(), :published_at),
                    NOW()
                )";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":author_id"        => $author_id,
            ":category_id"      => !empty($category_id) ? $category_id : null,
            ":region_id"        => !empty($region_id) ? $region_id : null,
            ":title"            => $title,
            ":slug"             => $slug,
            ":subtitle"         => $subtitle,
            ":excerpt"          => $excerpt,
            ":content_html"     => $content_html,
            ":content_plain"    => $content_plain,
            ":status"           => $status,
            ":is_breaking"      => $is_breaking,
            ":is_featured"      => $is_featured,
            ":is_premium"       => $is_premium,
            ":is_pinned"        => $is_pinned,
            ":meta_title"       => !empty($meta_title) ? $meta_title : $title,
            ":meta_description" => !empty($meta_description) ? $meta_description : substr($excerpt, 0, 320),
            ":reading_time_min" => $reading_time_min,
            ":featured_media_id"=> !empty($featured_media_id) ? $featured_media_id : null,
            ":use_now_for_published" => ($status === 'published' && empty($published_at)) ? 1 : 0,
            ":published_at"     => !empty($published_at) ? $published_at : null
        ]);

        $articleId = $pdo->lastInsertId();

        echo json_encode([
            "success" => true,
            "message" => "Article enregistré avec succès (ID: $articleId)",
            "article_id" => $articleId,
            "slug" => $slug
        ]);

    } catch (PDOException $e) {
        error_log("ERREUR SQL: " . $e->getMessage());
        echo json_encode([
            "success" => false,
            "message" => "Erreur base de données: " . $e->getMessage()
        ]);
    }
}
