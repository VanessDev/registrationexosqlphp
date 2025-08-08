<?php
require_once 'config/database.php';
session_start();

$errors = [];
$message = "";

// Valeurs par défaut pour éviter les notices
$email = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1) Récupération + normalisation
    $email = strtolower(trim($_POST["email"] ?? ''));
    $password = $_POST["password"] ?? '';

    // 2) Validation
    if ($email === '') {
        $errors[] = "Email obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide.";
    }
    if ($password === '') {
        $errors[] = "Mot de passe obligatoire.";
    }

    // 3) DB si pas d'erreurs
    if (empty($errors)) {
        try {
            $pdo = dbConnexion();

            // Recherche insensible à la casse
            $stmt = $pdo->prepare("
                SELECT id, nom, prenom, email, password
                FROM utilisateurs
                WHERE LOWER(email) = ?
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            $isValid = false;

            if ($user) {
                $dbPass = (string) ($user['password'] ?? '');

                // Si le mdp en BDD est un hash connu -> password_verify
                if (password_get_info($dbPass)['algo'] !== 0) {
                    $isValid = password_verify($password, $dbPass);
                } else {
                    // Sinon: mot de passe stocké en clair (temporaire pour compat)
                    $isValid = hash_equals(trim($dbPass), trim($password));
                }

                if ($isValid) {
                    // Upgrade auto en hash si c'était en clair
                    if (password_get_info($dbPass)['algo'] === 0) {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $up = $pdo->prepare("UPDATE utilisateurs SET password = ? WHERE id = ?");
                        $up->execute([$newHash, $user['id']]);
                    }

                    // Session minimale
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'email' => $user['email'],
                    ];

                    header("Location: index.php");
                    exit;
                }
            }

            // Si on arrive ici: login invalide
            $errors[] = "Email ou mot de passe incorrect.";

        } catch (PDOException $e) {
            // Message neutre (sécurité). Décommente pour debug local.
            $errors[] = "Une erreur interne est survenue. Veuillez réessayer.";
            // $errors[] = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/style.css" />
</head>

<body>
    <main>
        <form action="" method="POST" class="mon-formulaire">
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <p style="color:green;"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required value="<?= htmlspecialchars($email) ?>" />

            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required />

            <button type="submit">Se connecter</button>
        </form>
    </main>
</body>

</html>