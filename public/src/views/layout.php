<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= esc($pageTitle ?? 'ActuMonde - Lactualité internationale en continu') ?></title>

    <!-- Font Awesome 6 (gratuit, style moderne) -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fb;
            color: #1e2a3e;
            line-height: 1.5;
        }

        /* Bande rouge supérieure (rappel France 24) */
        .top-bar {
            background-color: #da291c;
            height: 5px;
            width: 100%;
        }

        /* Header principal */
        .main-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo-area h1 {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #0b1e2e;
        }

        .logo-area span {
            color: #da291c;
            font-weight: 800;
        }

        .logo-area p {
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #5a6874;
        }

        .nav-links {
            display: flex;
            gap: 1.8rem;
            font-weight: 600;
        }

        .nav-links a {
            text-decoration: none;
            color: #1e2a3e;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #da291c;
        }

        .header-icons {
            display: flex;
            gap: 1.2rem;
            font-size: 1.2rem;
            color: #4a5568;
        }

        .header-icons i {
            cursor: pointer;
            transition: color 0.2s;
        }

        .header-icons i:hover {
            color: #da291c;
        }

        /* Date & météo dynamique */
        .info-strip {
            background: #ffffff;
            border-bottom: 1px solid #e9edf2;
            padding: 0.6rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            font-size: 0.85rem;
            font-weight: 500;
            color: #2c3e50;
            max-width: 1300px;
            margin: 0 auto;
        }

        .date-box i,
        .weather-box i {
            margin-right: 8px;
            color: #da291c;
        }

        /* Container principal */
        .container {
            max-width: 1300px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        /* Grille à la une (hero) */
        .hero-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.8rem;
            margin-bottom: 3rem;
        }

        .hero-main {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .hero-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .hero-main:hover img {
            transform: scale(1.02);
        }

        .hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, #000000cc, transparent);
            padding: 2rem 1.8rem;
            color: white;
        }

        .category-tag {
            background: #da291c;
            display: inline-block;
            padding: 0.2rem 0.8rem;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 30px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.8rem;
        }

        .hero-content h2 {
            font-size: 1.9rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .hero-content p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .hero-side {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .side-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .side-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08);
        }

        .side-card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }

        .side-text {
            padding: 1rem;
        }

        .side-text h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0.3rem 0;
        }

        .side-text p {
            font-size: 0.85rem;
            color: #4b5563;
        }

        /* Section actualités en temps réel */
        .section-title {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            border-left: 5px solid #da291c;
            padding-left: 1rem;
            margin: 2.5rem 0 1.5rem 0;
        }

        .section-title h3 {
            font-size: 1.7rem;
            font-weight: 800;
        }

        .section-title a {
            color: #da291c;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .article-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.25s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border: 1px solid #eef2f6;
        }

        .article-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
        }

        .article-img {
            height: 190px;
            background-size: cover;
            background-position: center;
        }

        .article-info {
            padding: 1.2rem;
        }

        .article-cat {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #da291c;
            letter-spacing: 0.5px;
        }

        .article-info h4 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .article-info p {
            font-size: 0.85rem;
            color: #3e4a5b;
        }

        .meta-date {
            font-size: 0.7rem;
            color: #7e8b9c;
            margin-top: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Bannière live */
        .live-banner {
            background: #0b1e2e;
            border-radius: 24px;
            margin: 3rem 0;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            color: white;
        }

        .live-text {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .live-dot {
            width: 12px;
            height: 12px;
            background-color: #da291c;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.4;
                transform: scale(0.8);
            }

            100% {
                opacity: 1;
                transform: scale(1.2);
            }
        }

        .live-banner button {
            background: #da291c;
            border: none;
            padding: 0.7rem 1.8rem;
            border-radius: 40px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s;
        }

        .live-banner button:hover {
            background: #b71c12;
        }

        /* Footer */
        footer {
            background: #0f1722;
            color: #cbd5e1;
            padding: 2.5rem 2rem;
            margin-top: 3rem;
        }

        .footer-inner {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2rem;
        }

        .footer-col h4 {
            color: white;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .footer-col p,
        .footer-col a {
            font-size: 0.85rem;
            color: #9ca3af;
            text-decoration: none;
            display: block;
            margin: 0.5rem 0;
        }

        .copyright {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #1e2a3a;
            font-size: 0.75rem;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }

            .header-container {
                flex-direction: column;
                text-align: center;
            }

            .info-strip {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }
        }


        /* ========== SECTION GRID ========== */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    max-width: 1300px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

/* ========== CARTE ARTICLE ========== */
.card {
    background: #ffffff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    border: 1px solid #f0f2f5;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 32px rgba(0, 0, 0, 0.12);
}

/* Image */
.card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.card:hover img {
    transform: scale(1.02);
}

/* Contenu texte */
.card h2 {
    font-size: 1.35rem;
    font-weight: 700;
    line-height: 1.4;
    margin: 1rem 1.2rem 0.75rem;
    color: #1a2c3e;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card p {
    font-size: 0.9rem;
    line-height: 1.5;
    color: #4a5568;
    margin: 0 1.2rem 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Bouton Lire */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: transparent;
    color: #da291c;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-decoration: none;
    padding: 0.6rem 1.2rem;
    margin: 0 1.2rem 1.2rem;
    border-radius: 40px;
    border: 1.5px solid #da291c;
    transition: all 0.2s ease;
    width: fit-content;
}

.btn:hover {
    background: #da291c;
    color: white;
    border-color: #da291c;
    transform: scale(1.02);
}

.btn::after {
    content: "→";
    font-size: 1rem;
    transition: transform 0.2s;
}

.btn:hover::after {
    transform: translateX(4px);
}

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
    .grid {
        gap: 1.5rem;
        padding: 0 1rem;
    }
    
    .card h2 {
        font-size: 1.2rem;
        margin: 0.9rem 1rem 0.5rem;
    }
    
    .card p {
        font-size: 0.85rem;
        margin: 0 1rem 0.8rem;
    }
    
    .btn {
        margin: 0 1rem 1rem;
        padding: 0.5rem 1rem;
    }
}

@media (max-width: 480px) {
    .grid {
        grid-template-columns: 1fr;
    }
    
    .card img {
        height: 180px;
    }
}

/* ========== VARIANTE AVEC CATEGORIE (optionnelle) ========== */
.card {
    position: relative;
}

/* Badge catégorie optionnel - à ajouter si vous avez une catégorie dans vos données */
.card-category {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: #da291c;
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 30px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    z-index: 2;
    backdrop-filter: blur(2px);
}

/* Animation au chargement */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}

/* Stagger effect pour les cartes */
.card:nth-child(1) { animation-delay: 0.05s; }
.card:nth-child(2) { animation-delay: 0.1s; }
.card:nth-child(3) { animation-delay: 0.15s; }
.card:nth-child(4) { animation-delay: 0.2s; }
.card:nth-child(5) { animation-delay: 0.25s; }
.card:nth-child(6) { animation-delay: 0.3s; }

    </style>
</head>

<body>
    <div class="top-bar"></div>
    <header class="main-header">
        <div class="header-container">
            <div class="logo-area">
                <h1>ACTU<span>MONDE</span></h1>
                <p>L'information internationale, en continu</p>
            </div>
            <div class="nav-links">
                <a href="#" class="active">Accueil</a>
                <a href="#">Afrique</a>
                <a href="#">Amériques</a>
                <a href="#">Europe</a>
                <a href="#">Asie-Pacifique</a>
                <a href="#">Moyen-Orient</a>
            </div>
            <div class="header-icons">
                <i class="fas fa-search"></i>
                <i class="far fa-user-circle"></i>
            </div>
        </div>
    </header>

    <div class="info-strip" id="infoStrip">
        <div class="date-box"><i class="far fa-calendar-alt"></i> <span id="currentDate"></span></div>
        <div class="weather-box"><i class="fas fa-map-marker-alt"></i> Paris · <span id="weatherTemp">--</span>°C <i class="fas fa-cloud-sun"></i></div>
    </div>

    <main class="container">
        <!-- Section Hero avec actualités mises en avant -->
        <div class="hero-grid" id="heroGrid">
            <!-- le contenu sera généré dynamiquement via JS, mais on peut laisser le HTML de fallback -->
        </div>
    <div class="live-banner">
            <div class="live-text">
                <div class="live-dot"></div>
                <span><strong>ACTU MONDE LIVE</strong> | Suivez notre direct vidéo 24h/24</span>
            </div>
            <button id="liveBtn"><i class="fas fa-play"></i> Regarder le direct</button>
        </div>
        <!-- Section dernières actualités (actualisables) -->
        <div class="section-title">
            <h3><i class="fas fa-newspaper"></i> Dernières actualités</h3>
            <a href="#">Toute l'actualité <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="articles-grid" id="articlesContainer">
            <main>
                <?= $content ?>
            </main>
        </div>

        <!-- Bannière live TV / direct -->
    
    </main>

    <footer>
        <div class="footer-inner">
            <div class="footer-col">
                <h4>ACTUMONDE</h4>
                <p>Un journal inspiré par l'exigence de France 24. Toute l'actualité mondiale, décryptages et reportages.</p>
            </div>
            <div class="footer-col">
                <h4>Suivez-nous</h4>
                <a href="#"><i class="fab fa-facebook-f"></i> Facebook</a>
                <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                <a href="#"><i class="fab fa-youtube"></i> YouTube</a>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
            </div>
            <div class="footer-col">
                <h4>Services</h4>
                <a href="#">Newsletter</a>
                <a href="#">Applications mobiles</a>
                <a href="#">Météo mondiale</a>
            </div>
        </div>
        <div class="copyright">
            &copy; 2025 ACTUMONDE — Tous droits réservés. Informations certifiées par une rédaction indépendante.
        </div>
    </footer>