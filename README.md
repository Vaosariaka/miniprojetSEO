# Mini-projet: Site Guerre en Iran (FO + BO)

Projet PHP + MySQL avec Docker, URL rewriting, FrontOffice et BackOffice.

## 1) Lancer le projet

```bash
docker compose up --build -d
```

## 2) Acces

- FrontOffice: http://localhost:8080/
- Article SEO demande: http://localhost:8080/article/guerre-en-iran
- BackOffice: http://localhost:8080/admin/login

Identifiants par defaut BackOffice:
- user: admin
- pass: admin123

## 3) Fonctions livrees

- Creation base MySQL + seed article + tables utilisateurs/contenus
- URL rewriting via Apache + .htaccess
- FrontOffice avec structure titres (h1-h6), meta tags, alt image
- BackOffice: login + CRUD contenu + TinyMCE pour edition texte
- Slug normalise pour URL SEO

## 4) Arborescence principale

- public/: point d entree web
- src/: logique applicative PHP
- docker/: config Apache + SQL init
- docs/document-technique.md: dossier technique de livraison

## 5) Arreter

```bash
docker compose down
```
