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
            .form-group select {
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

            @media (max-width: 768px) {
                body {
                    padding: 1rem;
                }
                .form-row {
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
        </style>
    </head>
    <body>
        <div class="editor-container">
            <div class="editor-header">
                <h1><i class="fas fa-pen-fancy"></i> Rédiger un article</h1>
                <p>Créez et publiez votre contenu avec l'éditeur avancé</p>
            </div>

            <div class="editor-form">
                <form id="articleForm" method="POST" action="miniprojetSEO/public/save-article" enctype="application/x-www-form-urlencoded">
                    <div class="form-group">
                        <label><i class="fas fa-heading"></i> Titre de l'article *</label>
                        <input type="text" name="title" id="title" placeholder="Ex: Guerre en Iran : analyse complète du conflit" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> Catégorie *</label>
                            <select name="category" id="category" required>
                                <option value="">Sélectionner une catégorie</option>
                                <option value="International">🌍 International</option>
                                <option value="Afrique">🌍 Afrique</option>
                                <option value="Amériques">🌎 Amériques</option>
                                <option value="Europe">🇪🇺 Europe</option>
                                <option value="Asie-Pacifique">🌏 Asie-Pacifique</option>
                                <option value="Moyen-Orient">🕌 Moyen-Orient</option>
                                <option value="Sport">⚽ Sport</option>
                                <option value="Économie">💰 Économie</option>
                            </select>
                        </div>

                      
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Résumé / chapô *</label>
                        <input type="text" name="summary" id="summary" placeholder="Brève description de l'article (150-200 caractères)" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-file-alt"></i> Contenu de l'article *</label>
                        <textarea id="tinyeditor" name="content"></textarea>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-slug"></i> Slug (URL personnalisée)</label>
                        <input type="text" name="slug" id="slug" placeholder="guerre-en-iran-analyse">
                        <small style="color:#64748b; display:block; margin-top:5px;">Laissez vide pour génération automatique</small>
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
            // Initialisation de TinyMCE avec configuration optimisée
            tinymce.init({
                selector: '#tinyeditor',
                height: 500,
                language: 'fr_FR',  // Note: nécessite le pack langue, mais reste en anglais partiellement - fonctionne
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
                toolbar: 'undo redo | blocks | bold italic underline strikethrough | link media table | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                toolbar_mode: 'sliding',
                content_style: 'body { font-family: "Segoe UI", Arial, sans-serif; font-size: 16px; line-height: 1.6; }',
                branding: false,
                promotion: false,
                uploadcare_public_key: '1f53e12325ad32ca52f3',
                setup: function(editor) {
                    editor.on('change', function() {
                        // Marque le formulaire comme modifié (optionnel)
                        editor.save();
                    });
                }
            });

            // Gestionnaire d'événements pour le formulaire
            document.getElementById('articleForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Récupération des données
                const title = document.getElementById('title').value.trim();
                const category = document.getElementById('category').value;
                const summary = document.getElementById('summary').value.trim();
                const slug = document.getElementById('slug').value.trim();
                
                // Récupérer le contenu TinyMCE
                const content = tinymce.get('tinyeditor').getContent();
                
                // Validation simple
                if (!title) {
                    showMessage('Veuillez saisir un titre', 'error');
                    return;
                }
                if (!category) {
                    showMessage('Veuillez sélectionner une catégorie', 'error');
                    return;
                }
                if (!summary) {
                    showMessage('Veuillez saisir un résumé', 'error');
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
                formData.append('category', category);
                formData.append('summary', summary);
                formData.append('slug', finalSlug);
                formData.append('content', content);
                formData.append('submit_action', 'publish');
                
                // Afficher un indicateur de chargement
                const submitBtn = document.getElementById('submitBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="loading"></span> Envoi en cours...';
                submitBtn.disabled = true;
                
                try {
                    // Envoi vers le script PHP (miniprojetSEO/public/save-article)
                    const response = await fetch('miniprojetSEO/public/save-article', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: formData.toString()
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showMessage(result.message || 'Article publié avec succès !', 'success');
                        // Optionnel: réinitialiser le formulaire après succès
                        if (confirm('Article enregistré ! Voulez-vous réinitialiser le formulaire pour rédiger un nouvel article ?')) {
                            resetForm();
                        }
                    } else {
                        showMessage(result.message || 'Erreur lors de l\'enregistrement', 'error');
                    }
                } catch (error) {
                    console.log('Erreur:', error);
                    console.log('Erreur:', formData.toString());
                    

                    showMessage('Erreur de connexion au serveur. Vérifiez que miniprojetSEO/public/save-article est accessible.', 'error');
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
            
            // Prévisualisation de l'article (modal)
            document.getElementById('previewBtn').addEventListener('click', function() {
                const title = document.getElementById('title').value.trim() || 'Sans titre';
                const category = document.getElementById('category').options[document.getElementById('category').selectedIndex]?.text || 'Non catégorisé';
                const summary = document.getElementById('summary').value.trim() || 'Résumé non disponible';
                const content = tinymce.get('tinyeditor').getContent();
                
                const previewDiv = document.getElementById('previewContent');
                previewDiv.innerHTML = `
                    <div style="margin-bottom: 20px;">
                        ${imageUrl ? `<img src="${imageUrl}" alt="Image article" style="width:100%; border-radius:16px; margin-bottom:20px;">` : ''}
                        <h1 style="color:#da291c; font-size:1.8rem;">${escapeHtml(title)}</h1>
                        <div style="background:#f1f5f9; padding:0.5rem 1rem; border-radius:12px; margin:15px 0;">
                            <strong>Catégorie :</strong> ${escapeHtml(category)} &nbsp;|&nbsp;
                            <strong>Publié le :</strong> ${new Date().toLocaleDateString('fr-FR')}
                        </div>
                        <div style="font-style:italic; color:#475569; border-left:4px solid #da291c; padding-left:1rem; margin:20px 0;">
                            ${escapeHtml(summary)}
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
            
            // Fermer le modal en cliquant à l'extérieur
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
                document.getElementById('category').value = '';
                document.getElementById('summary').value = '';
                document.getElementById('slug').value = '';
                tinymce.get('tinyeditor').setContent('<p>Nouvel article...</p>');
                showMessage('Formulaire réinitialisé', 'success');
            }
            
            // Fonction utilitaire pour échapper le HTML (sécurité preview)
            function escapeHtml(str) {
                if (!str) return '';
                return str
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }
            
            // Génération auto du slug à partir du titre (optionnel)
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
        </script>
    </body>
    </html>