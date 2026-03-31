<?php

require __DIR__ . '/../src/bootstrap.php';

// charger controller
require __DIR__ . '/src/controllers/MainController.php';

// recuperer URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$uri = rtrim($uri, '/') ?: '/';

// base dynamique (ex: '' en Docker, '/miniprojetSEO2/public' en sous-dossier)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$base = rtrim($scriptDir, '/');
if ($base === '/.') {
    $base = '';
}

$homeRoutes = ['/', ($base !== '' ? $base : '/')];
$backRoutes = ['/back'];
$loginRoutes = ['/login'];
$saveRoutes = ['/save-article'];
$articleSlug = null;
if ($base !== '') {
    $backRoutes[] = $base . '/back';
    $loginRoutes[] = $base . '/login';
    $saveRoutes[] = $base . '/save-article';
}

if (preg_match('#^/article/([a-z0-9-]+)$#', $uri, $matches)) {
    $articleSlug = $matches[1];
}
if ($articleSlug === null && $base !== '' && preg_match('#^' . preg_quote($base, '#') . '/article/([a-z0-9-]+)$#', $uri, $matches)) {
    $articleSlug = $matches[1];
}

// buffer HTML
ob_start();

// choix du layout par défaut
$layout = 'layout.php';

if (in_array($uri, $homeRoutes, true)) {

    $pageTitle = "Accueil"; 
    $pageHeading = "Bienvenue";

    home($pdo);

} elseif (in_array($uri, $loginRoutes, true)) {
    $pageTitle = "Login";
    $layout = 'layoutLogin.php';
    $loginError = '';
    $loginUsername = $_POST['username'] ?? 'admin';
    $loginPassword = $_POST['password'] ?? 'admin123';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($loginUsername === 'admin' && $loginPassword === 'admin123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'admin';
            $_SESSION['user_role_slug'] = 'admin';
            redirect_to(($base ?: '') . '/back');
        }

        $loginError = 'Identifiants invalides.';
    }
    require __DIR__ . '/src/views/login.php';
} elseif ($articleSlug !== null) {
    $pageTitle = 'Article';
    showArticle($pdo, $articleSlug);
} elseif (in_array($uri, $backRoutes, true)) {
    if (!is_logged_in()) {
        redirect_to(($base ?: '') . '/login');
    }

    $pageTitle = "BackOffice";
    $layout = 'layoutBackOffice.php'; // ✅ changement layout

    backend();

} 
elseif (in_array($uri, $saveRoutes, true) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_logged_in()) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Non autorise. Veuillez vous connecter.'
        ]);
        exit;
    }

    header('Content-Type: application/json');

    saveArticle($pdo);

    exit; // 🔥 obligatoire
} else {

    $pageTitle = "404";
    echo "<h2>404 - Page non trouvée</h2>";
}

// récupérer contenu
$content = ob_get_clean();

// charger UN SEUL layout
require __DIR__ . '/src/views/' . $layout;
