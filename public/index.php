<?php

require __DIR__ . '/../src/bootstrap.php';

// charger controller
require __DIR__ . '/src/controllers/MainController.php';

// récupérer URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$base = '/miniprojetSEO/public';

// buffer HTML
ob_start();

// choix du layout par défaut
$layout = 'layout.php';

if ($uri === '/' || $uri === $base . '/') {

    $pageTitle = "Accueil"; 
    $pageHeading = "Bienvenue";

    home($pdo);

} elseif ($uri === $base . '/back') {

    $pageTitle = "BackOffice";
    $layout = 'layoutBackOffice.php'; // ✅ changement layout

    backend();

} 
elseif ($uri === $base . '/save-article' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $layout = 'layoutBackOffice.php'; // optionnel
    saveArticle($pdo);
} else {

    $pageTitle = "404";
    echo "<h2>404 - Page non trouvée</h2>";
}

// récupérer contenu
$content = ob_get_clean();

// charger UN SEUL layout
require __DIR__ . '/src/views/' . $layout;