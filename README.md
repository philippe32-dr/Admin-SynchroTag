# Admin-SynchroTag

Dashboard d’administration Laravel 11 avec Tailwind CSS, Heroicons, Toasts.

## Installation

1. Cloner ce dépôt ou copier les fichiers dans un dossier vide.
2. Installer les dépendances PHP :
   ```bash
   composer install
   ```
3. Installer les dépendances front-end :
   ```bash
   npm install
   ```
4. Copier `.env.example` en `.env` et configurer la base MySQL.
5. Générer la clé d’application :
   ```bash
   php artisan key:generate
   ```
6. Lancer les migrations :
   ```bash
   php artisan migrate
   ```
7. Compiler les assets :
   ```bash
   npm run dev
   ```
8. Démarrer le serveur :
   ```bash
   php artisan serve
   ```

## Test

- Tester via [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
- Utiliser Postman pour tester les endpoints API
- Accéder à [http://127.0.0.1:8000/admin/clients/{id}/edit](http://127.0.0.1:8000/admin/clients/{id}/edit) pour tester la modification du mot de passe et l'attribution/désattribution des puces
- Vérifier l'attribution/désattribution dynamique des puces sans rechargement de page

## Vérifications

- Confirmer l'absence d'erreurs dans `storage/logs/laravel.log`
- Vérifier l'état des migrations avec `php artisan migrate:status`

## Structure du projet
- `app/Models/`
- `app/Http/Controllers/`
- `resources/views/layouts/`
- `resources/views/admin/`
- `resources/views/users/`
- `resources/views/clients/`
- `resources/views/puces/`
- `resources/views/kyc/`

## API Documentation

La documentation complète de l'API est disponible dans le fichier [API_DOCUMENTATION.md](API_DOCUMENTATION.md).

### Points d'entrée principaux :
- Authentification (login/register)
- Vérification d'email
- Gestion des mots de passe oubliés
- Profil utilisateur

### Configuration requise
- `Accept: application/json`
- `Content-Type: application/json`
- `Authorization: Bearer <token>` (pour les routes protégées)

## Export SQL
Après migration, exporter la base vide :
```bash
mysqldump -u <user> -p --no-data <database> > export_structure.sql
```

---
Palette : #222651 (texte), #1BB4D8 → #90E0EF (accents).
