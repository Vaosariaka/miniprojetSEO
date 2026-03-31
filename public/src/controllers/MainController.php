<?php

function home($pdo) {
    $articles = fetch_published_articles($pdo);

    require __DIR__ . '/../views/home.php';
}

function backend(){
    require __DIR__ . '/../views/backoffice.php';
}

function saveArticle($pdo) {
    header('Content-Type: application/json');
    error_log('=== DONNÉES REÇUES ===');
    error_log(print_r($_POST, true));
    error_log(print_r($_FILES, true));

    // 1. Récupération des données
    $title           = $_POST['title'] ?? '';
    $slug            = $_POST['slug'] ?? '';
    $excerpt         = $_POST['excerpt'] ?? '';
    $content_html    = $_POST['content_html'] ?? '';
    $subtitle        = $_POST['subtitle'] ?? '';
    $category_id     = $_POST['category_id'] ?? null;
    $region_id       = $_POST['region_id'] ?? null;
    $status          = $_POST['status'] ?? 'draft';
    $published_at    = $_POST['published_at'] ?? null;
    $is_breaking     = $_POST['is_breaking'] ?? 0;
    $is_featured     = $_POST['is_featured'] ?? 0;
    $is_premium      = $_POST['is_premium'] ?? 0;
    $is_pinned       = $_POST['is_pinned'] ?? 0;
    $meta_title      = $_POST['meta_title'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $reading_time_min = $_POST['reading_time_min'] ?? null;
    $author_id = $_SESSION['user_id'] ?? 1;

    // 2. Validation
    $errors = [];
    if (empty($title)) $errors[] = "Le titre est obligatoire";
    if (empty($slug)) $slug = normalize_slug($title);
    if (empty($excerpt)) $errors[] = "Le résumé est obligatoire";
    if (strlen($excerpt) > 320) $errors[] = "Le résumé ne doit pas dépasser 320 caractères";
    if (empty($content_html) || $content_html === '<p><br data-mce-bogus="1"></p>') $errors[] = "Le contenu de l'article est obligatoire";
    if (!empty($errors)) {
        echo json_encode([
            "success" => false,
            "message" => "Champs obligatoires manquants ou invalides",
            "errors" => $errors,
            "received" => array_keys($_POST)
        ]);
        return;
    }

    // 3. Gestion de l'upload et insertion dans media
    $featured_media_id = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../assets/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $tmpName = $_FILES['featured_image']['tmp_name'];
        $originalName = basename($_FILES['featured_image']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($ext, $allowed)) {
            $newName = uniqid('img_', true) . '.' . $ext;
            $destPath = $uploadDir . $newName;
            if (move_uploaded_file($tmpName, $destPath)) {
                // Récupérer infos fichier
                $file_size = filesize($destPath);
                $mime_type = mime_content_type($destPath);
                $file_path = 'assets/img/' . $newName;
                $width = null;
                $height = null;
                if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                    $imgInfo = @getimagesize($destPath);
                    if ($imgInfo) {
                        $width = $imgInfo[0];
                        $height = $imgInfo[1];
                    }
                }
                // Insertion dans media
                $sqlMedia = "INSERT INTO media (uploaded_by, type, filename, original_name, mime_type, file_path, file_size, width, height, created_at) VALUES (:uploaded_by, :type, :filename, :original_name, :mime_type, :file_path, :file_size, :width, :height, NOW())";
                $stmtMedia = $pdo->prepare($sqlMedia);
                $stmtMedia->execute([
                    ':uploaded_by' => $author_id,
                    ':type' => 'image',
                    ':filename' => $newName,
                    ':original_name' => $originalName,
                    ':mime_type' => $mime_type,
                    ':file_path' => $file_path,
                    ':file_size' => $file_size,
                    ':width' => $width,
                    ':height' => $height
                ]);
                $featured_media_id = $pdo->lastInsertId();
            }
        }
    }

    try {
        if (empty($reading_time_min)) {
            $content_plain = strip_tags($content_html);
            $reading_time_min = max(1, ceil(str_word_count($content_plain) / 200));
        }
        $content_plain = strip_tags($content_html);

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
                    :published_at,
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
            ":featured_media_id" => $featured_media_id,
            ":published_at"     => !empty($published_at) ? $published_at : ($status === 'published' ? date('Y-m-d H:i:s') : null)
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

// Fonction utilitaire pour normaliser le slug
function normalize_slug($string) {
    $string = strtolower(trim($string));
    $string = str_replace(
        ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'û', 'ü', 'ç', ' ', "'", '"', '?', '!', '.', ','],
        ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'c', '-', '', '', '', '', '', ''],
        $string
    );
    $string = preg_replace('/[^a-z0-9-]+/', '-', $string);
    $string = trim($string, '-');
    $string = preg_replace('/-+/', '-', $string);
    
    return $string === '' ? 'article-sans-titre' : $string;
}