<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditeur d'article - TinyMCE</title>
    <!-- Font Awesome pour l'icône -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .editor-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .editor-header {
            background: linear-gradient(135deg, #da291c 0%, #b71c12 100%);
            padding: 1.5rem 2rem;
            color: white;
        }

        .editor-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .editor-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .editor-form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
            font-size: 0.9rem;
        }

        .form-group label i {
            color: #da291c;
            margin-right: 8px;
        }

        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group select,
        .form-group input[type="number"] {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #da291c;
            box-shadow: 0 0 0 3px rgba(218, 41, 28, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.5rem;
        }

        /* TinyMCE customisation */
        .tox-tinymce {
            border-radius: 12px !important;
            border: 2px solid #e2e8f0 !important;
        }

        .tox-tinymce:focus-within {
            border-color: #da291c !important;
        }

        /* Boutons */
        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-submit {
            background: linear-gradient(135deg, #da291c 0%, #b71c12 100%);
            color: white;
            border: none;
            padding: 0.9rem 2rem;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: inherit;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(218, 41, 28, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-preview {
            background: white;
            color: #1e293b;
            border: 2px solid #da291c;
            padding: 0.9rem 2rem;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: inherit;
        }

        .btn-preview:hover {
            background: #fff5f5;
            transform: translateY(-2px);
        }

        .btn-reset {
            background: #f1f5f9;
            color: #475569;
            border: none;
            padding: 0.9rem 2rem;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: inherit;
        }

        .btn-reset:hover {
            background: #e2e8f0;
        }

        /* Message de statut */
        .status-message {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 12px;
            display: none;
            align-items: center;
            gap: 10px;
        }

        .status-message.success {
            background: #e6f7e6;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
            display: flex;
        }

        .status-message.error {
            background: #fee;
            color: #c62828;
            border-left: 4px solid #c62828;
            display: flex;
        }

        /* Preview modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            max-width: 800px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            padding: 2rem;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .modal-header h2 {
            color: #da291c;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .close-modal:hover {
            color: #da291c;
        }

        .preview-content {
            line-height: 1.6;
        }

        .preview-content h1, .preview-content h2, .preview-content h3 {
            margin-top: 1rem;
        }

        /* Checkbox style */
        .checkbox-group {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-weight: normal;
            margin-bottom: 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            .form-row, .form-row-3 {
                grid-template-columns: 1fr;
            }
            .button-group {
                flex-direction: column;
            }
            .btn-submit, .btn-preview, .btn-reset {
                justify-content: center;
            }
        }

        /* Loading spinner */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Badge styles */
        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-draft { background: #fef3c7; color: #92400e; }
        .badge-published { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="editor-container">
        <div class="editor-header">
            <h1><i class="fas fa-pen-fancy"></i> Rédiger un article</h1>
            <p>Créez et publiez votre contenu avec l'éditeur avancé</p>
        </div>

        <div class="editor-form">
            <form id="articleForm" method="POST" action="/miniprojetSEO/public/save-article"  enctype="multipart/form-data" >
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Titre de l'article *</label>
                    <input type="text" name="title" id="title" placeholder="Ex: Guerre en Iran : analyse complète du conflit" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Catégorie *</label>
                        <select name="category_id" id="category_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            <option value="1">🌍 Afrique</option>
                            <option value="2">🌎 Amériques</option>
                            <option value="3">🇪🇺 Europe</option>
                            <option value="4">🌏 Asie-Pacifique</option>
                            <option value="5">🕌 Moyen-Orient</option>
                            <option value="6">💰 Économie</option>
                            <option value="7">🔬 Science & Tech</option>
                            <option value="8">🎨 Culture</option>
                            <option value="9">✍️ Opinion</option>
                            <option value="10">⚽ Sport</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-globe"></i> Région</label>
                        <select name="region_id" id="region_id">
                            <option value="">Sélectionner une région</option>
                            <option value="1">🌍 Afrique subsaharienne</option>
                            <option value="2">🌍 Afrique du Nord</option>
                            <option value="3">🕌 Moyen-Orient</option>
                            <option value="4">🌍 Europe de l'Ouest</option>
                            <option value="5">🌍 Europe de l'Est</option>
                            <option value="6">🌎 Amérique du Nord</option>
                            <option value="7">🌎 Amérique latine</option>
                            <option value="8">🌏 Asie de l'Est</option>
                            <option value="9">🌏 Asie du Sud</option>
                            <option value="10">🌏 Asie du Sud-Est</option>
                            <option value="11">🌏 Océanie</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Sous-titre / Chapeau</label>
                    <input type="text" name="subtitle" id="subtitle" placeholder="Sous-titre ou accroche de l'article">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-paragraph"></i> Résumé / Chapô *</label>
                    <textarea name="excerpt" id="excerpt" rows="3" placeholder="Brève description de l'article (150-200 caractères)" required></textarea>
                    <small id="charCount" style="color:#64748b;">0 caractères</small>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label><i class="fas fa-flag"></i> Statut</label>
                        <select name="status" id="status">
                            <option value="draft">📝 Brouillon</option>
                            <option value="published">✅ Publié</option>
                            <option value="pending_review">⏳ En attente de relecture</option>
                            <option value="scheduled">📅 Programmé</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Date de publication</label>
                        <input type="datetime-local" name="published_at" id="published_at">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Temps de lecture (min)</label>
                        <input type="number" name="reading_time_min" id="reading_time_min" placeholder="Auto-calculé" readonly style="background:#f1f5f9;">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-image"></i> Image à la une</label>
                    <input type="file" name="featured_image" id="featured_image" accept="image/*">
                    <small style="color:#64748b;">Choisissez une image (jpg, png, webp...)</small>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-check-square"></i> Options de mise en avant</label>
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="is_breaking" value="1"> 🔴 Breaking news
                        </label>
                        <label>
                            <input type="checkbox" name="is_featured" value="1"> ⭐ À la une (Hero)
                        </label>
                        <label>
                            <input type="checkbox" name="is_premium" value="1"> 👑 Premium (réservé abonnés)
                        </label>
                        <label>
                            <input type="checkbox" name="is_pinned" value="1"> 📌 Épinglé
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-file-alt"></i> Contenu de l'article *</label>
                    <textarea id="tinyeditor" name="content_html"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-link"></i> Slug (URL personnalisée)</label>
                        <input type="text" name="slug" id="slug" placeholder="guerre-en-iran-analyse">
                        <small style="color:#64748b;">Laissez vide pour génération automatique</small>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-chart-line"></i> Meta title (SEO)</label>
                        <input type="text" name="meta_title" id="meta_title" placeholder="Titre pour les moteurs de recherche">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-description"></i> Meta description (SEO)</label>
                    <textarea name="meta_description" id="meta_description" rows="2" placeholder="Description pour les moteurs de recherche (max 320 caractères)"></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Publier l'article
                    </button>
                    <button type="button" class="btn-preview" id="previewBtn">
                        <i class="fas fa-eye"></i> Aperçu
                    </button>
                    <button type="button" class="btn-reset" id="resetBtn">
                        <i class="fas fa-undo-alt"></i> Réinitialiser
                    </button>
                </div>

                <div id="statusMessage" class="status-message"></div>
            </form>
        </div>
    </div>

    <!-- Modal de prévisualisation -->
    <div id="previewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-eye"></i> Aperçu de l'article</h2>
                <button class="close-modal" id="closeModalBtn">&times;</button>
            </div>
            <div class="preview-content" id="previewContent">
                <!-- Le contenu prévisualisé apparaîtra ici -->
            </div>
        </div>
    </div>

    <!-- TinyMCE Script -->
    <script src="https://cdn.tiny.cloud/1/tbmydfg7z16q2dhbdfv4bggje0idy7y62rhbf26keaxd7s5t/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

    <script>
        // Variable pour stocker l'image URL (depuis un éventuel upload)
        let imageUrl = '';

        // Initialisation de TinyMCE
        tinymce.init({
            selector: '#tinyeditor',
            height: 500,
            language: 'fr_FR',
            plugins: [
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 
                'link', 'lists', 'media', 'searchreplace', 'table', 
                'visualblocks', 'wordcount', 'checklist', 'mediaembed', 
                'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 
                'tinymcespellchecker', 'permanentpen', 'powerpaste', 
                'advtable', 'advcode', 'advtemplate', 'uploadcare', 
                'mentions', 'tableofcontents', 'footnotes', 'mergetags', 
                'autocorrect', 'typography', 'inlinecss', 'markdown'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            toolbar_mode: 'sliding',
            content_style: 'body { font-family: "Segoe UI", Arial, sans-serif; font-size: 16px; line-height: 1.6; }',
            branding: false,
            promotion: false,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                    calculateReadingTime();
                });
                editor.on('init', function() {
                    calculateReadingTime();
                });
            }
        });

        // Compter les caractères du résumé
        const excerptTextarea = document.getElementById('excerpt');
        const charCountSpan = document.getElementById('charCount');

        function updateCharCount() {
            const count = excerptTextarea.value.length;
            charCountSpan.textContent = count + ' caractères';
            if (count > 320) {
                charCountSpan.style.color = '#c62828';
            } else {
                charCountSpan.style.color = '#64748b';
            }
        }

        excerptTextarea.addEventListener('input', updateCharCount);
        updateCharCount();

        // Calculer le temps de lecture
        function calculateReadingTime() {
            const content = tinymce.get('tinyeditor').getContent();
            const text = content.replace(/<[^>]*>/g, '');
            const wordCount = text.trim().split(/\s+/).filter(w => w.length > 0).length;
            const readingTime = Math.max(1, Math.ceil(wordCount / 200));
            const readingTimeInput = document.getElementById('reading_time_min');
            if (readingTimeInput) {
                readingTimeInput.value = readingTime;
            }
        }

        // Gestionnaire d'événements pour le formulaire
        document.getElementById('articleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Récupération des données
            const title = document.getElementById('title').value.trim();
            const categoryId = document.getElementById('category_id').value;
            const regionId = document.getElementById('region_id').value;
            const subtitle = document.getElementById('subtitle').value.trim();
            const excerpt = document.getElementById('excerpt').value.trim();
            const slug = document.getElementById('slug').value.trim();
            const status = document.getElementById('status').value;
            const publishedAt = document.getElementById('published_at').value;
            const metaTitle = document.getElementById('meta_title').value.trim();
            const metaDescription = document.getElementById('meta_description').value.trim();
            
            // Récupérer les checkbox
            const isBreaking = document.querySelector('input[name="is_breaking"]').checked ? 1 : 0;
            const isFeatured = document.querySelector('input[name="is_featured"]').checked ? 1 : 0;
            const isPremium = document.querySelector('input[name="is_premium"]').checked ? 1 : 0;
            const isPinned = document.querySelector('input[name="is_pinned"]').checked ? 1 : 0;
            
            // Récupérer le contenu TinyMCE
            const contentHtml = tinymce.get('tinyeditor').getContent();
            
            // Validation
            if (!title) {
                showMessage('Veuillez saisir un titre', 'error');
                return;
            }
            if (!categoryId) {
                showMessage('Veuillez sélectionner une catégorie', 'error');
                return;
            }
            if (!excerpt) {
                showMessage('Veuillez saisir un résumé', 'error');
                return;
            }
            if (excerpt.length > 320) {
                showMessage('Le résumé ne doit pas dépasser 320 caractères', 'error');
                return;
            }
            if (!contentHtml || contentHtml === '<p><br data-mce-bogus="1"></p>' || contentHtml.trim() === '') {
                showMessage('Le contenu de l\'article ne peut pas être vide', 'error');
                return;
            }
            
            // Générer un slug automatique si vide
            let finalSlug = slug;
            if (!finalSlug) {
                finalSlug = title
                    .toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '');
            }
            
            // Construction des données à envoyer
            const formData = new URLSearchParams();
            formData.append('title', title);
            formData.append('category_id', categoryId);
            if (regionId) formData.append('region_id', regionId);
            if (subtitle) formData.append('subtitle', subtitle);
            formData.append('excerpt', excerpt);
            formData.append('slug', finalSlug);
            formData.append('status', status);
            if (publishedAt) formData.append('published_at', publishedAt);
            formData.append('content_html', contentHtml);
            formData.append('is_breaking', isBreaking.toString());
            formData.append('is_featured', isFeatured.toString());
            formData.append('is_premium', isPremium.toString());
            formData.append('is_pinned', isPinned.toString());
            if (metaTitle) formData.append('meta_title', metaTitle);
            if (metaDescription) formData.append('meta_description', metaDescription);
            formData.append('reading_time_min', document.getElementById('reading_time_min').value);
            
            // Afficher un indicateur de chargement
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading"></span> Envoi en cours...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch('/miniprojetSEO/public/save-article', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData.toString()
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message || 'Article publié avec succès !', 'success');
                    if (confirm('Article enregistré ! Voulez-vous réinitialiser le formulaire pour rédiger un nouvel article ?')) {
                        resetForm();
                    }
                } else {
                    showMessage(result.message || 'Erreur lors de l\'enregistrement', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showMessage('Erreur de connexion au serveur. Vérifiez que le endpoint est accessible.', 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
        
        // Fonction pour afficher les messages
        function showMessage(message, type) {
            const msgDiv = document.getElementById('statusMessage');
            msgDiv.className = `status-message ${type}`;
            msgDiv.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}`;
            msgDiv.style.display = 'flex';
            
            setTimeout(() => {
                msgDiv.style.display = 'none';
            }, 5000);
        }
        
        // Prévisualisation de l'article
        document.getElementById('previewBtn').addEventListener('click', function() {
            const title = document.getElementById('title').value.trim() || 'Sans titre';
            const categorySelect = document.getElementById('category_id');
            const categoryText = categorySelect.options[categorySelect.selectedIndex]?.text || 'Non catégorisé';
            const subtitle = document.getElementById('subtitle').value.trim();
            const excerpt = document.getElementById('excerpt').value.trim() || 'Résumé non disponible';
            const content = tinymce.get('tinyeditor').getContent();
            const status = document.getElementById('status').value;
            
            let statusBadge = '';
            if (status === 'draft') statusBadge = '<span class="badge badge-draft">Brouillon</span>';
            if (status === 'published') statusBadge = '<span class="badge badge-published">Publié</span>';
            
            const previewDiv = document.getElementById('previewContent');
            previewDiv.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <h1 style="color:#da291c; font-size:1.8rem;">${escapeHtml(title)}</h1>
                    ${subtitle ? `<h3 style="color:#475569; font-weight:400;">${escapeHtml(subtitle)}</h3>` : ''}
                    <div style="background:#f1f5f9; padding:0.5rem 1rem; border-radius:12px; margin:15px 0; display:flex; justify-content:space-between; flex-wrap:wrap;">
                        <div><strong>Catégorie :</strong> ${escapeHtml(categoryText)}</div>
                        <div><strong>Statut :</strong> ${statusBadge}</div>
                        <div><strong>Publié le :</strong> ${new Date().toLocaleDateString('fr-FR')}</div>
                    </div>
                    <div style="font-style:italic; color:#475569; border-left:4px solid #da291c; padding-left:1rem; margin:20px 0;">
                        ${escapeHtml(excerpt)}
                    </div>
                    <div style="border-top:1px solid #e2e8f0; padding-top:20px;">
                        ${content || '<p>Aucun contenu rédigé pour le moment.</p>'}
                    </div>
                </div>
            `;
            
            const modal = document.getElementById('previewModal');
            modal.classList.add('active');
        });
        
        // Fermeture du modal
        document.getElementById('closeModalBtn').addEventListener('click', function() {
            document.getElementById('previewModal').classList.remove('active');
        });
        
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('previewModal');
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
        
        // Réinitialisation du formulaire
        document.getElementById('resetBtn').addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les champs ?')) {
                resetForm();
            }
        });
        
        function resetForm() {
            document.getElementById('title').value = '';
            document.getElementById('category_id').value = '';
            document.getElementById('region_id').value = '';
            document.getElementById('subtitle').value = '';
            document.getElementById('excerpt').value = '';
            document.getElementById('slug').value = '';
            document.getElementById('status').value = 'draft';
            document.getElementById('published_at').value = '';
            document.getElementById('meta_title').value = '';
            document.getElementById('meta_description').value = '';
            document.querySelector('input[name="is_breaking"]').checked = false;
            document.querySelector('input[name="is_featured"]').checked = false;
            document.querySelector('input[name="is_premium"]').checked = false;
            document.querySelector('input[name="is_pinned"]').checked = false;
            tinymce.get('tinyeditor').setContent('<p>Nouvel article...</p>');
            updateCharCount();
            calculateReadingTime();
            showMessage('Formulaire réinitialisé', 'success');
        }
        
        // Fonction utilitaire pour échapper le HTML
        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }
        
        // Génération auto du slug à partir du titre
        document.getElementById('title').addEventListener('blur', function() {
            const slugField = document.getElementById('slug');
            if (!slugField.value.trim()) {
                let generatedSlug = this.value
                    .toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '');
                slugField.value = generatedSlug;
            }
        });
        
        // Auto-fill meta title from title
        document.getElementById('title').addEventListener('blur', function() {
            const metaTitleField = document.getElementById('meta_title');
            if (!metaTitleField.value.trim() && this.value.trim()) {
                metaTitleField.value = this.value.trim();
            }
        });
    </script>
</body>
</html>