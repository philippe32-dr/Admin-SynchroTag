# Documentation de l'API Admin-SynchroTag

Cette documentation décrit les endpoints de l'API Admin-SynchroTag, construite avec Laravel 11 et Laravel Sanctum pour l'authentification par token.

## Configuration requise

- **URL de base** : `http://votre-domaine.com/api`
- **En-têtes requis** : 
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>` (pour les routes protégées)

## Table des matières
1. [Authentification](#authentification)
   - [Inscription](#inscription)
   - [Vérification d'email](#vérification-demail)
   - [Connexion](#connexion)
   - [Déconnexion](#déconnexion)
   - [Mot de passe oublié](#mot-de-passe-oublié)
   - [Vérification du code de réinitialisation](#vérification-du-code-de-réinitialisation)
   - [Réinitialisation du mot de passe](#réinitialisation-du-mot-de-passe)
2. [Profil utilisateur](#profil-utilisateur)
3. [Gestion des puces](#gestion-des-puces)
   - [Lister les puces](#lister-les-puces)
   - [Associer un objet à une puce](#associer-un-objet-à-une-puce)
   - [Mettre à jour un objet](#mettre-à-jour-un-objet)
4. [Gestion des KYC](#gestion-des-kyc)
   - [Soumettre un KYC](#soumettre-un-kyc)
   - [Vérifier le statut KYC](#vérifier-le-statut-kyc)
5. [Gestion des erreurs](#gestion-des-erreurs)

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

## Profil utilisateur

### Mettre à jour le profil

Met à jour les informations du profil de l'utilisateur connecté.

- **URL** : `/profile`
- **Méthode** : `PUT`
- **Accès** : Authentifié
- **En-têtes** :
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
  - `Content-Type: multipart/form-data`
- **Corps de la requête** :
  - `nom` (string, optionnel) : Nouveau nom
  - `prenom` (string, optionnel) : Nouveau prénom
  - `profile_photo` (file, optionnel) : Nouvelle photo de profil (max 2MB)

- **Réponse de succès (200)** :
  ```json
  {
    "user": {
      "id": 1,
      "nom": "Dupont",
      "prenom": "Jean",
      "email": "jean.dupont@example.com",
      "profile_photo": "/storage/profiles/photo.jpg",
      "statut_kyc": "EnCours"
    }
  }
  ```

## Gestion des puces

### Lister les puces

Récupère la liste des puces attribuées à l'utilisateur connecté.

- **URL** : `/puces`
- **Méthode** : `GET`
- **Accès** : Authentifié
- **En-têtes** :
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

- **Réponse de succès (200)** :
  ```json
  {
    "puces": [
      {
        "id": 1,
        "numero": "P123456789",
        "status": "Attribuee",
        "object_name": "Vélo électrique",
        "object_photo": "/storage/objects/photo.jpg",
        "object_photo_url": "http://example.com/storage/objects/photo.jpg",
        "object_range": 100,
        "client_id": 1
      }
    ]
  }
  ```

### Associer un objet à une puce

Associe un objet à une puce attribuée à l'utilisateur connecté.

- **URL** : `/puces/{puce}/assign-object`
- **Méthode** : `POST`
- **Accès** : Authentifié
- **En-têtes** :
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
  - `Content-Type: multipart/form-data`
- **Paramètres de chemin** :
  - `puce` (integer, requis) : ID de la puce
- **Corps de la requête** :
  - `object_name` (string, requis) : Nom de l'objet
  - `object_photo` (file, requis) : Photo de l'objet (max 2MB)
  - `object_range` (integer, requis) : Portée en mètres

- **Réponse de succès (200)** :
  ```json
  {
    "puce": {
      "id": 1,
      "numero_puce": "P123456789",
      "status": "Attribuee",
      "object_name": "Vélo électrique",
      "object_photo": "/storage/objects/photo.jpg",
      "object_photo_url": "http://example.com/storage/objects/photo.jpg",
      "object_range": 100
    }
  }
  ```

- **Erreurs possibles** :
  - `403` : Accès non autorisé (la puce n'appartient pas à l'utilisateur)
  - `422` : Validation échouée ou la puce n'est pas attribuée

### Mettre à jour un objet

Met à jour les informations d'un objet associé à une puce.

- **URL** : `/puces/{puce}/update-object`
- **Méthode** : `PUT`
- **Accès** : Authentifié
- **En-têtes** :
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
  - `Content-Type: multipart/form-data`
- **Paramètres de chemin** :
  - `puce` (integer, requis) : ID de la puce
- **Corps de la requête** :
  - `object_name` (string, optionnel) : Nouveau nom de l'objet
  - `object_photo` (file, optionnel) : Nouvelle photo de l'objet (max 2MB)
  - `object_range` (integer, optionnel) : Nouvelle portée en mètres

- **Réponse de succès (200)** : Similaire à la réponse de l'endpoint d'assignation

## Gestion des KYC

### Soumettre un KYC

Soumet une demande de vérification d'identité (KYC).

- **URL** : `/kyc`
- **Méthode** : `POST`
- **Accès** : Authentifié
- **En-têtes** :
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Corps de la requête** :
  ```json
  {
    "nom": "Dupont",
    "prenom": "Jean",
    "nationalite": "Française",
    "telephone": "0612345678",
    "adresse_postale": "123 rue de l'exemple, 75000 Paris",
    "numero_npi": "1234567890",
    "type_document": "CNI",
    "numero_document": "12AB34567",
    "date_emission": "2020-01-01",
    "date_expiration": "2030-01-01"
  }
  ```

- **Réponse de succès (200)** :
  ```json
  {
    "message": "KYC soumis avec succès"
  }
  ```

- **Erreurs possibles** :
  - `422` : Validation échouée ou demande déjà en cours

### Vérifier le statut KYC

Récupère le statut de la demande KYC de l'utilisateur connecté.

- **URL** : `/kyc/status`
- **Méthode** : `GET`
- **Accès** : Authentifié
- **En-têtes** :
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

- **Réponse de succès (200) - KYC trouvé** :
  ```json
  {
    "kyc": {
      "id": 1,
      "status": "EnCours",
      "user_id": 1,
      "numero_npi": "1234567890",
      "nom": "Dupont",
      "prenom": "Jean",
      "nationalite": "Française",
      "telephone": "0612345678",
      "adresse_postale": "123 rue de l'exemple, 75000 Paris",
      "type_document": "CNI",
      "numero_document": "12AB34567",
      "date_emission": "2020-01-01",
      "date_expiration": "2030-01-01"
    }
  }
  ```

- **Réponse de succès (200) - Aucun KYC** :
  ```json
  {
    "message": "Aucun KYC soumis"
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
