<?php
// 📦 Importe une seule fois le fichier de connexion à la base de données (évite les doublons)
require_once 'config/database.php';

// 🚪 Démarre la session PHP pour pouvoir stocker des infos (comme l'utilisateur connecté)
session_start();

// 🗂 Initialise un tableau vide pour stocker les messages d'erreurs éventuels
$errors = [];

// ✅ Initialise une variable vide pour stocker un éventuel message de succès ou d'information
$message = "";


// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération et nettoyage des données
    $email = trim(htmlspecialchars($_POST["email"] ?? ''));
    $password = $_POST["password"] ?? '';

    // Validation
    // ✅ Vérifie si le champ email est vide
    if (empty($email)) {
        // Si oui, on ajoute un message d'erreur dans le tableau $errors[]
        $errors[] = "Email obligatoire.";
    }

    // ✅ Sinon, vérifie si l'email n'est pas au bon format (exemple : pas de @, ou mauvaise syntaxe)
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Si le format est invalide, on ajoute aussi une erreur
        $errors[] = "Format d'email invalide.";
    }

    // ✅ Vérifie si le champ mot de passe est vide
    if (empty($password)) {
        // Si le mot de passe est vide, on ajoute une erreur
        $errors[] = "Mot de passe obligatoire.";
    }


    // Si tout est ok
    if (empty($errors)) {
        $pdo = dbConnexion();

        // Recherche de l'utilisateur
// 🔧 Prépare une requête SQL pour sélectionner un utilisateur selon son email (évite les injections SQL)
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");

        // ▶️ Exécute la requête en remplaçant le point d'interrogation "?" par la valeur réelle de $email
        $stmt->execute([$email]);

        // 📥 Récupère le premier résultat trouvé (ou false si aucun utilisateur avec cet email)
// Le résultat est un tableau associatif contenant les colonnes de la table (email, password, etc.)
        $user = $stmt->fetch();


        // DEBUG temporaire
        var_dump($user);       // 👈 Affiche le résultat de la requête
        var_dump($email);      // 👈 Affiche l'email envoyé
        var_dump($password);   // 👈 Affiche le mot de passe envoyé
        exit;                  // 👈 Stoppe le code ici pour tester

        // Vérifie si l'utilisateur existe et si le mot de passe entré correspond au mot de passe hashé dans la base
        if ($user && password_verify($password, $user["password"])) {

            // ✅ Connexion réussie : on stocke les infos de l'utilisateur dans la session
            $_SESSION['user'] = $user;

            // 🔁 Redirige l'utilisateur vers la page d'accueil (protégée)
            header("Location: index.php");

            // ⛔ Stoppe le script (important après une redirection)
            exit();

        } else {
            // ❌ Si l'email n'existe pas OU que le mot de passe est incorrect
            // On ajoute un message d'erreur dans le tableau $errors[]
            $errors[] = "Email ou mot de passe incorrect.";
        }

    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <main>
        <form action="" method="POST" class="mon-formulaire">
            <?php
            // ✅ Vérifie si le champ email est vide
            if (empty($email)) {
                // Si oui, on ajoute un message d'erreur dans le tableau $errors[]
                $errors[] = "Email obligatoire.";
            }

            ?>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Se connecter</button>
        </form>
    </main>
</body>

</html>