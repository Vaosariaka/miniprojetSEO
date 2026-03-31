<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Admin - ActuMonde') ?></title>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            overflow-x: hidden;
        }

        /* ========== BACK OFFICE LAYOUT ========== */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0f1722 0%, #0a0f1a 100%);
            color: #e2e8f0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.08);
        }

        .sidebar-header {
            padding: 1.8rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1.5rem;
        }

        .sidebar-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .sidebar-header h2 span {
            color: #da291c;
        }

        .sidebar-header p {
            font-size: 0.7rem;
            opacity: 0.7;
            margin-top: 0.3rem;
        }

        .sidebar-nav {
            padding: 0 1rem 1.5rem;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.85rem 1rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .nav-item a i {
            width: 24px;
            font-size: 1.2rem;
        }

        .nav-item a:hover {
            background: rgba(218, 41, 28, 0.15);
            color: #da291c;
        }

        .nav-item.active a {
            background: #da291c;
            color: white;
            box-shadow: 0 4px 10px rgba(218, 41, 28, 0.3);
        }

        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
            margin: 1.5rem 1rem;
        }

        .nav-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7e8b9c;
            padding: 0 1rem;
            margin: 1rem 0 0.5rem;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: all 0.3s ease;
        }

        /* Top Bar */
        .top-navbar {
            background: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #1e293b;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #f1f5f9;
            border-radius: 40px;
            padding: 0.5rem 1rem;
            width: 300px;
        }

        .search-bar i {
            color: #94a3b8;
        }

        .search-bar input {
            border: none;
            background: transparent;
            padding: 0 0.8rem;
            outline: none;
            font-size: 0.85rem;
            width: 100%;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
        }

        .badge-notif {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #da291c;
            color: white;
            font-size: 0.65rem;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: #da291c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        /* ========== PAGE CONTENT ========== */
        .page-container {
            padding: 2rem;
        }

        /* Dashboard Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #eef2f6;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            margin: 0.5rem 0;
            color: #0f172a;
        }

        .stat-icon {
            float: right;
            font-size: 2rem;
            color: #da291c20;
        }

        /* Tables et formulaires */
        .card-panel {
            background: white;
            border-radius: 24px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #eef2f6;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .panel-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .btn-primary {
            background: #da291c;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: #b71c12;
        }

        .btn-outline {
            background: transparent;
            border: 1.5px solid #da291c;
            color: #da291c;
            padding: 0.5rem 1rem;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-outline:hover {
            background: #da291c;
            color: white;
        }

        /* Table stylisée */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eef2f6;
        }

        .data-table th {
            font-weight: 700;
            color: #475569;
            font-size: 0.85rem;
        }

        .data-table tr:hover {
            background: #f8fafc;
        }

        .status-badge {
            background: #e6f7e6;
            color: #2e7d32;
            padding: 0.2rem 0.7rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        .action-icons i {
            margin: 0 5px;
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .action-icons i:hover {
            color: #da291c;
        }

        /* Formulaire */
        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            font-family: inherit;
            transition: border 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #da291c;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .menu-toggle {
                display: block;
            }
            .search-bar {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .top-navbar {
                flex-wrap: wrap;
                gap: 1rem;
            }
            .page-container {
                padding: 1rem;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #e2e8f0;
        }
        ::-webkit-scrollbar-thumb {
            background: #da291c;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>ACTU<span>MONDE</span></h2>
            <p>Espace Administration</p>
        </div>
        <div class="sidebar-nav">
            <div class="nav-item active">
                <a href="#">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de bord</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#">
                    <i class="fas fa-newspaper"></i>
                    <span>Articles</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvel article</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#">
                    <i class="fas fa-folder"></i>
                    <span>Catégories</span>
                </a>
            </div>
            <div class="nav-divider"></div>
            <div class="nav-label">Gestion</div>
            <div class="nav-item">
                <a href="#">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#">
                    <i class="fas fa-comment-dots"></i>
                    <span>Commentaires</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#">
                    <i class="fas fa-chart-line"></i>
                    <span>Statistiques</span>
                </a>
            </div>
            <div class="nav-divider"></div>
            <div class="nav-item">
                <a href="#">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="top-navbar">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Rechercher dans l'admin...">
            </div>
            <div class="admin-profile">
                <div class="notification-icon">
                    <i class="far fa-bell"></i>
                    <span class="badge-notif">3</span>
                </div>
                <div class="user-info">
                    <div class="avatar">AD</div>
                    <span>Admin</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <div class="page-container">
            <!-- DYNAMIC CONTENT RENDERED HERE -->
            <?= $content ?? '' ?>

            <!-- Exemple de contenu par défaut si $content est vide (dashboard demo) -->
            <?php if(empty($content)): ?>
                <!-- Dashboard stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="stat-title">Articles publiés</div>
                        <div class="stat-value">1,284</div>
                        <small style="color: #22c55e;">↑ +12 ce mois</small>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-eye"></i></div>
                        <div class="stat-title">Vues totales</div>
                        <div class="stat-value">342,890</div>
                        <small>+18% vs mois dernier</small>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-title">Utilisateurs</div>
                        <div class="stat-value">156</div>
                        <small>4 administrateurs</small>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-comments"></i></div>
                        <div class="stat-title">Commentaires</div>
                        <div class="stat-value">2,341</div>
                        <small>en attente: 23</small>
                    </div>
                </div>

                <!-- Derniers articles -->
                <div class="card-panel">
                    <div class="panel-header">
                        <h3><i class="fas fa-clock"></i> Derniers articles</h3>
                        <a href="#" class="btn-primary" style="text-decoration: none;"><i class="fas fa-plus"></i> Nouvel article</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr><th>Titre</th><th>Catégorie</th><th>Date</th><th>Statut</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Guerre en Iran : l'escalade se poursuit</td><td>International</td><td>2026-03-30</td><td><span class="status-badge">Publié</span></td><td class="action-icons"><i class="fas fa-edit"></i> <i class="fas fa-trash-alt"></i></td></tr>
                            <tr><td>Crise humanitaire au Soudan : l'ONU alerte</td><td>Afrique</td><td>2026-03-29</td><td><span class="status-badge">Publié</span></td><td class="action-icons"><i class="fas fa-edit"></i> <i class="fas fa-trash-alt"></i></td></tr>
                            <tr><td>Paris 2026 : les nouveaux défis écologiques</td><td>Sport</td><td>2026-03-28</td><td><span class="status-badge">Brouillon</span></td><td class="action-icons"><i class="fas fa-edit"></i> <i class="fas fa-trash-alt"></i></td></tr>
                            <tr><td>Sommet européen : accord historique sur la défense</td><td>Europe</td><td>2026-03-27</td><td><span class="status-badge">Publié</span></td><td class="action-icons"><i class="fas fa-edit"></i> <i class="fas fa-trash-alt"></i></td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Formulaire d'ajout rapide (démonstration) -->
                <div class="card-panel">
                    <div class="panel-header">
                        <h3><i class="fas fa-pen-fancy"></i> Ajouter un article (rapide)</h3>
                    </div>
                    <form action="#" method="POST">
                        <div class="form-group">
                            <label>Titre de l'article</label>
                            <input type="text" placeholder="Titre..." required>
                        </div>
                        <div class="form-group">
                            <label>Résumé</label>
                            <textarea rows="3" placeholder="Résumé de l'article..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Catégorie</label>
                            <select>
                                <option>International</option><option>Afrique</option><option>Europe</option><option>Asie</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Image (URL)</label>
                            <input type="text" placeholder="https://...">
                        </div>
                        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Publier</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Scripts pour sidebar mobile et notifications simulées -->
<script>
    // Toggle sidebar on mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if(menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
    // Close sidebar when clicking outside on mobile (optionnel)
    document.addEventListener('click', function(event) {
        const isClickInside = sidebar.contains(event.target) || menuToggle.contains(event.target);
        if (!isClickInside && window.innerWidth <= 992 && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });

    // Petite notification console pour démonstration
    console.log('Back office prêt — interface moderne pour ACTU MONDE');
</script>

<!-- Intégration possible de fontawesome via CDN déjà en place -->
</body>
</html>