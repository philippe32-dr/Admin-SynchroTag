# Documentation de l'API Admin-SynchroTag

Cette documentation décrit les endpoints d'authentification de l'API Admin-SynchroTag, construite avec Laravel 11 et Laravel Sanctum pour l'authentification par token.

## Configuration requise

- **URL de base** : `http://votre-domaine.com/api`
- **En-têtes requis** : 
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>` (pour les routes protégées)

## Table des matières
1. [Inscription](#inscription)
2. [Vérification d'email](#vérification-demail)
3. [Connexion](#connexion)
4. [Déconnexion](#déconnexion)
5. [Mot de passe oublié](#mot-de-passe-oublié)
6. [Vérification du code de réinitialisation](#vérification-du-code-de-réinitialisation)
7. [Réinitialisation du mot de passe](#réinitialisation-du-mot-de-passe)
8. [Profil utilisateur](#profil-utilisateur)
9. [Gestion des erreurs](#gestion-des-erreurs)
10. [Notes importantes](#notes-importantes)

## Inscription

Crée un nouveau compte utilisateur.

- **URL** : `/register`
- **Méthode** : `POST`
- **Accès** : Public
- **Corps de la requête** :
  ```json
  {
    "nom": "Dupont",
    "prenom": "Jean",
    "email": "jean.dupont@example.com",
    "password": "votreMotDePasse123",
    "password_confirmation": "votreMotDePasse123"
  }
  ```

- **Réponse de succès (200)** :
  ```json
  {
    "message": "Utilisateur enregistré avec succès. Veuillez vérifier votre email pour le code de vérification.",
    "verification_code": "123456",
    "user": {
      "nom": "Dupont",
      "prenom": "Jean",
      "email": "jean.dupont@example.com",
      "updated_at": "2025-06-24T19:00:00.000000Z",
      "created_at": "2025-06-24T19:00:00.000000Z",
      "id": 1
    }
  }
  ```

- **Erreurs possibles** :
  - 422 : Validation échouée (email déjà pris, champs manquants, etc.)
  ```json
  {
    "message": "The email has already been taken.",
    "errors": {
      "email": ["The email has already been taken."]
    }
  }
  ```

## Vérification d'email

Vérifie l'adresse email avec le code reçu.

- **URL** : `/verify-email`
- **Méthode** : `POST`
- **Accès** : Public
- **Corps de la requête** :
  ```json
  {
    "email": "jean.dupont@example.com",
    "code": "123456"
  }
  ```

- **Réponse de succès (200)** :
  ```json
  {
    "message": "Email vérifié avec succès"
  }
  ```

- **Erreurs possibles** :
  - 422 : Code invalide ou expiré
  ```json
  {
    "message": "Code de vérification invalide"
  }
  ```

## Connexion

Connecte un utilisateur et retourne un token d'authentification.

- **URL** : `/login`
- **Méthode** : `POST`
- **Accès** : Public
- **Corps de la requête** :
  ```json
  {
    "email": "jean.dupont@example.com",
    "password": "votreMotDePasse123"
  }
  ```

- **Réponse de succès (200)** :
  ```json
  {
    "user": {
      "id": 1,
      "nom": "Dupont",
      "prenom": "Jean",
      "email": "jean.dupont@example.com",
      "statut_kyc": null
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz"
  }
  ```

- **Erreurs possibles** :
  - 403 : Email non vérifié
  ```json
  {
    "message": "Veuillez vérifier votre adresse email avant de vous connecter."
  }
  ```
  - 401 : Identifiants invalides
  ```json
  {
    "message": "Les informations d'identification sont incorrectes."
  }
  ```

## Déconnexion

Déconnecte l'utilisateur et invalide le token.

- **URL** : `/logout`
- **Méthode** : `POST`
- **Accès** : Protégé (token requis)
- **En-têtes** :
  ```
  Authorization: Bearer votre_token_ici
  ```

- **Réponse de succès (200)** :
  ```json
  {
    "message": "Déconnexion réussie"
  }
  ```

## Mot de passe oublié

Envoie un code de réinitialisation à l'email fourni.

- **URL** : `/password/forgot`
- **Méthode** : `POST`
- **Accès** : Public
- **Corps de la requête** :
  ```json
  {
    "email": "jean.dupont@example.com"
  }
  ```

- **Réponse de succès (200)** :
  ```json
  {
    "message": "Si votre email existe dans notre système, vous recevrez un code de réinitialisation.",
    "reset_code": "654321",
    "note": "En production, seul le message générique serait affiché."
  }
  ```

## Vérification du code de réinitialisation

Vérifie si le code de réinitialisation est valide.

- **URL** : `/password/verify-code`
- **Méthode** : `POST`
- **Accès** : Public
- **Corps de la requête** :
  ```json
  {
    "email": "jean.dupont@example.com",
    "code": "654321"
  }
  ```

- **Réponse de succès (200)** :
  ```json
  {
    "message": "Code valide",
    "email": "jean.dupont@example.com",
    "code": "654321"
  }
  ```

- **Erreurs possibles** :
  - 422 : Code invalide
  ```json
  {
    "message": "Code de réinitialisation invalide",
    "email": "jean.dupont@example.com",
    "code_attendu": "654321",
    "code_reçu": "123456",
    "type_code_attendu": "string",
    "type_code_reçu": "string",
    "est_egal": "non"
  }
  ```

## Réinitialisation du mot de passe

Réinitialise le mot de passe avec un code valide.

- **URL** : `/password/reset`
- **Méthode** : `POST`
- **Accès** : Public
- **Corps de la requête** :
  ```json
  {
    "email": "jean.dupont@example.com",
    "code": "654321",
    "password": "nouveauMotDePasse123",
    "password_confirmation": "nouveauMotDePasse123"
  }
  ```

- **Réponse de succès (200)** :
  ```json
  {
    "message": "Mot de passe réinitialisé"
  }
  ```

## Profil utilisateur

Récupère les informations du profil de l'utilisateur connecté.

- **URL** : `/user`
- **Méthode** : `GET`
- **Accès** : Protégé (token requis)
- **En-têtes** :
  ```
  Authorization: Bearer votre_token_ici
  ```

- **Réponse de succès (200)** :
  ```json
  {
    "id": 1,
    "nom": "Dupont",
    "prenom": "Jean",
    "email": "jean.dupont@example.com",
    "statut_kyc": null
  }
  ```

## Gestion des erreurs

L'API utilise les codes HTTP standards pour indiquer le statut des réponses :

- **200** : Requête traitée avec succès
- **400** : Mauvaise requête
- **401** : Non autorisé (authentification requise ou échouée)
- **403** : Accès refusé (permissions insuffisantes)
- **404** : Ressource non trouvée
- **422** : Erreur de validation
- **500** : Erreur serveur interne

## Notes importantes

1. **Environnement de développement** :
   - Les codes de vérification et de réinitialisation sont affichés dans la réponse en environnement de développement.
   - En production, seul un message générique est renvoyé.

2. **Journaux** :
   - Les emails envoyés (codes de vérification, réinitialisation) sont enregistrés dans `storage/logs/laravel.log`.
   - Le mailer est configuré avec `MAIL_MAILER=log` pour les environnements de développement.

3. **Sécurité** :
   - Les mots de passe doivent contenir au moins 8 caractères.
   - Les tokens d'authentification doivent être stockés de manière sécurisée côté client.
   - Utilisez toujours HTTPS en production.

4. **Déconnexion** :
   - Le token est automatiquement révoqué lors de la déconnexion.
   - En cas de problème, le client doit gérer la suppression locale du token.

5. **Code de statut** :
   - Toutes les réponses incluent un code de statut HTTP approprié.
   - Les erreurs de validation incluent des messages détaillés dans le corps de la réponse.

6. **Tests** :
   - Utilisez Postman ou cURL pour tester les endpoints.
   - Assurez-vous d'envoyer les en-têtes `Content-Type: application/json` et `Accept: application/json`.

Pour toute question ou problème, veuillez contacter l'équipe de développement.
