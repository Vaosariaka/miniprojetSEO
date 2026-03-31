# Document technique - Mini projet FO/BO

## 1. Contexte

- Sujet: Site d informations sur la guerre en Iran
- Stack: PHP 8.2 + MySQL 8.0 + Apache (Docker)

## 2. Captures ecran a fournir

Placer vos captures dans `docs/screenshots/`:
- FrontOffice accueil
- FrontOffice detail article
- BackOffice login
- BackOffice liste contenus
- BackOffice formulaire edition

## 3. Modelisation base de donnees

### Table `articles`
- id (PK)
- title
- slug (UNIQUE)
- summary
- content
- image_path
- image_alt
- meta_title
- meta_description
- is_published
- published_at
- created_at
- updated_at

### Table `users`
- id (PK)
- username (UNIQUE)
- password_hash
- created_at

## 4. Compte BO par defaut

- URL login: /admin/login
- User: admin
- Pass: admin123

## 5. Reponse aux points de verification

1. URL normalisee rewriting:
- .htaccess public redirige toutes les URL vers index.php.
- URL article: /article/guerre-en-iran

2. Structure titres Hn:
- h1 sur chaque page (entete principal)
- contenu enrichi en h2, h3 via TinyMCE

3. Utilisation des titres:
- titres explicites par page et section

4. Balises meta:
- meta title dynamique
- meta description dynamique
- canonical
- robots index,follow

5. Alt images:
- image_alt obligatoire en BO
- img avec attribut alt en FO

6. Lighthouse local (mobile et desktop):
- Lancer test local sur http://localhost:8080
- Conseille: Chrome DevTools Lighthouse
- Captures a joindre dans ce dossier

## 6. Conteneurisation

- `docker compose up --build -d` pour lancer
- Service `web` (Apache + PHP)
- Service `db` (MySQL)

## 7. Livraison

- Zip du projet complet
- Depot GitHub/GitLab public
- Ce document technique complete
