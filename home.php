<?php
// =========================
// 1) DÉMARRAGE DE LA SESSION
// =========================

// Démarre ou reprend une session PHP (permet d'utiliser $_SESSION)
session_start();


// =========================
// 2) PROTECTION DE LA PAGE
// =========================

// On vérifie si la clé 'user' existe dans la session et qu'elle contient bien un tableau
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    // Si l'utilisateur n'est pas connecté, on le renvoie vers la page de connexion
    header('Location: login.php');
    // On arrête le script (sinon le code en dessous continuerait à s'exécuter)
    exit;
}


// =========================
// 3) GESTION DE LA DÉCONNEXION
// =========================

// Si on a "logout=1" dans l'URL (ex : home.php?logout=1)
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    // On vide toutes les données de la session
    $_SESSION = [];

    // Si des cookies de session existent, on les supprime
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), // nom du cookie
            '',             // valeur vide
            time() - 42000, // date passée pour le rendre invalide
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Détruit la session côté serveur
    session_destroy();

    // Redirige vers la page de connexion
    header('Location: login.php');
    exit;
}


// =========================
// 4) RÉCUPÉRATION DES INFOS UTILISATEUR
// =========================

// On récupère le tableau 'user' stocké en session
$user = $_SESSION['user'];

// On prend chaque champ avec une valeur par défaut si jamais il manque
$userId = $user['id'] ?? 'inconnu';
$userNom = $user['nom'] ?? '';
$userPren = $user['prenom'] ?? '';
$userMail = $user['email'] ?? 'pas d\'email';

// On prépare un nom complet à afficher
$displayName = trim($userNom . ' ' . $userPren) ?: 'Utilisateur';
?>
<!DOCTYPE html>
<html lang="fr"> <!-- Déclare la langue principale du document -->

<head>
    <!-- Indique l'encodage des caractères -->
    <meta charset="UTF-8">
    <!-- Permet au site d'être responsive sur mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Titre affiché dans l'onglet du navigateur -->
    <title>Espace membre</title>
    <!-- Lien vers la feuille de styles CSS -->
    <link rel="stylesheet" href="assets/style/style.css">
</head>

<body>
    <!-- HEADER : zone d'en-tête du site -->
    <header>
        <!-- Titre principal du site -->
        <h1>Mon site d'auth en PHP</h1>

        <!-- Paragraphe de bienvenue personnalisé avec les infos de session -->
        <p>
            <?php
            // htmlspecialchars pour éviter les failles XSS si un nom contient du HTML
            echo 'Bienvenue ' . htmlspecialchars($displayName) .
                ' — connecté avec ' . htmlspecialchars($userMail);
            ?>
        </p>

        <!-- Menu de navigation -->
        <nav>
            <ul>
                <!-- Lien vers la page d'accueil -->
                <li><a href="home.php">Accueil</a></li>
                <!-- Lien vers une autre page (exemple vide) -->
                <li><a href="#">Lien vers une page</a></li>
                <!-- Encore un lien exemple -->
                <li><a href="#">Un autre lien</a></li>
                <!-- Lien pour se déconnecter (ajoute ?logout=1 à l'URL) -->
                <li><a href="home.php?logout=1">Se déconnecter</a></li>
            </ul>
        </nav>
    </header>

    <!-- MAIN : contenu principal de la page -->
    <main>
        <!-- Section réservée aux utilisateurs connectés -->
        <section>
            <h2>Zone protégée</h2>
            <p>Votre identifiant interne est :
                <strong><?= htmlspecialchars((string) $userId) ?></strong>
            </p>
            <p>Vous êtes bien connecté. Ajoutez ici le contenu réservé aux membres.</p>
        </section>
    </main>
</body>

</html>