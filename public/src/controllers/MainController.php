<?php

function home($pdo) {
    $articles = fetch_published_articles($pdo);

    require __DIR__ . '/../views/home.php';
}

function backend(){
    require __DIR__ . '/../views/backoffice.php';
}


function saveArticle($pdo) {

    // 1. Récupération des données
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $summary = $_POST['summary'] ?? '';
    $content = $_POST['content'] ?? '';
    $image_url = $_POST['image_url'] ?? '';

    // 2. Validation simple
    if (empty($title) || empty($slug) || empty($summary) || empty($content)) {
        echo json_encode([
            "success" => false,
            "message" => "Tous les champs obligatoires doivent être remplis"
        ]);
        return;
    }

    try {

        // 3. Préparer la requête SQL
        $sql = "INSERT INTO articles 
                (title, slug, summary, content, image_path, image_alt, meta_title, meta_description) 
                VALUES 
                (:title, :slug, :summary, :content, :image_path, :image_alt, :meta_title, :meta_description)";

        $stmt = $pdo->prepare($sql);

        // 4. Valeurs SEO automatiques
        $image_path = !empty($image_url) ? $image_url : "assets/img/iran.png";
        $image_alt = $title;
        $meta_title = $title;
        $meta_description = substr($summary, 0, 300);

        // 5. Exécution
        $stmt->execute([
            ":title" => $title,
            ":slug" => $slug,
            ":summary" => $summary,
            ":content" => $content,
            ":image_path" => $image_path,
            ":image_alt" => $image_alt,
            ":meta_title" => $meta_title,
            ":meta_description" => $meta_description
        ]);

        // 6. Réponse succès
        echo json_encode([
            "success" => true,
            "message" => "Article enregistré avec succès"
        ]);

    } catch (PDOException $e) {

        // 7. Gestion erreur
        echo json_encode([
            "success" => false,
            "message" => "Erreur DB: " . $e->getMessage()
        ]);
    }
}