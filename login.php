<?php
require_once 'config/database.php';
session_start();

$errors = [];
$message = "";

// Si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération et nettoyage des données
    $email = trim(htmlspecialchars($_POST["email"] ?? ''));
    $password = $_POST["password"] ?? '';

    // Validation
    if (empty($email)) {
        $errors[] = "Email obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide.";
    }

    if (empty($password)) {
        $errors[] = "Mot de passe obligatoire.";
    }

    // Si tout est ok
    if (empty($errors)) {
        $pdo = dbConnexion();

        // Recherche de l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(); // 👈 C’est ici qu'on récupère l'utilisateur

        // DEBUG temporaire
        var_dump($user);       // 👈 Affiche le résultat de la requête
        var_dump($email);      // 👈 Affiche l'email envoyé
        var_dump($password);   // 👈 Affiche le mot de passe envoyé
        exit;                  // 👈 Stoppe le code ici pour tester

        if ($user && password_verify($password, $user["password"])) {
            // Connexion réussie
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit();
        } else {
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
            foreach ($errors as $error) {
                echo "<p style='color:red;'>$error</p>";
            }

            if (!empty($message)) {
                echo "<p style='color:green;'>$message</p>";
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