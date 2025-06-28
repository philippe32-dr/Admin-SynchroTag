<?php
// Vérifier si on est en ligne de commande
if (php_sapi_name() !== 'cli') {
    die('Ce script doit être exécuté en ligne de commande');
}

// Créer le fichier .env s'il n'existe pas
if (!file_exists(__DIR__ . '/.env')) {
    if (!copy(__DIR__ . '/.env.example', __DIR__ . '/.env')) {
        die("Impossible de créer le fichier .env\n");
    }
    echo "Fichier .env créé à partir de .env.example\n";
}

// Lire le contenu actuel du fichier .env
$envPath = __DIR__ . '/.env';
$envContent = file_get_contents($envPath);

// Configuration de la base de données
$dbConfig = [
    'DB_CONNECTION=mysql',
    'DB_HOST=127.0.0.1',
    'DB_PORT=3306',
    'DB_DATABASE=admin_synchrotag',
    'DB_USERNAME=root',
    'DB_PASSWORD=',
];

// Mettre à jour la configuration de la base de données
foreach ($dbConfig as $setting) {
    list($key) = explode('=', $setting);
    $envContent = preg_replace(
        "/^" . preg_quote($key, '/') . "=.*$/m",
        $setting,
        $envContent,
        1,
        $count
    );
    
    if ($count === 0) {
        $envContent .= "\n" . $setting;
    }
}

// Écrire les modifications dans le fichier .env
file_put_contents($envPath, $envContent);
echo "Configuration de la base de données mise à jour dans .env\n";

// Générer une clé d'application si elle n'existe pas
$envContent = file_get_contents($envPath);
if (strpos($envContent, 'APP_KEY=') === false || strpos($envContent, 'APP_KEY=base64:') === false) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $envContent = preg_replace(
        "/^APP_KEY=.*$/m",
        "APP_KEY=$key",
        $envContent
    );
    file_put_contents($envPath, $envContent);
    echo "Clé d'application générée\n";
}

// Supprimer complètement la base de données et toutes ses tables
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Désactiver temporairement la vérification des clés étrangères
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    // Supprimer la base de données si elle existe
    $pdo->exec('DROP DATABASE IF EXISTS admin_synchrotag');
    
    // Créer une nouvelle base de données
    $pdo->exec('CREATE DATABASE admin_synchrotag CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    
    // Réactiver la vérification des clés étrangères
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    // Sélectionner la nouvelle base de données
    $pdo->exec('USE admin_synchrotag');
    
    echo "Base de données 'admin_synchrotag' réinitialisée avec succès\n";
} catch (PDOException $e) {
    die("Erreur lors de la réinitialisation de la base de données: " . $e->getMessage() . "\n");
}

// Exécuter les migrations et les seeders
echo "\nExécution des migrations...\n";

// D'abord exécuter les migrations sans les seeders
$output = [];
exec('php artisan migrate:fresh --force', $output, $returnCode);
echo implode("\n", $output) . "\n";

if ($returnCode !== 0) {
    die("Erreur lors de l'exécution des migrations. Code de sortie : $returnCode\n");
}

// Ensuite exécuter les seeders
$output = [];
exec('php artisan db:seed --force', $output, $returnCode);
echo implode("\n", $output) . "\n";

if ($returnCode !== 0) {
    die("Erreur lors de l'exécution des seeders. Code de sortie : $returnCode\n");
}

// Créer le lien de stockage
echo "\nCréation du lien de stockage...\n";
exec('php artisan storage:link', $output, $returnCode);
echo implode("\n", $output) . "\n";

echo "\nConfiguration terminée avec succès !\n";
echo "Vous pouvez maintenant accéder à votre application.\n\n";
echo "Identifiants de test :\n";
echo "- Email: admin@example.com\n";
echo "- Mot de passe: password\n\n";
