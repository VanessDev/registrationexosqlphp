<?php
// Initialisation des variables
$name = $email = $password = $confirmPassword = "";
$errors = []; // Tableau pour stocker les erreurs

// Vérifie si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupère les données du formulaire
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_motdepasse'] ?? '');

    // Vérifie que tous les champs sont remplis
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    // Vérifie que l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

    // Vérifie la longueur du nom
    if (strlen($name) < 3) {
        $errors[] = "Le nom doit contenir au moins 3 caractères.";
    } elseif (strlen($name) > 55) {
        $errors[] = "Le nom ne doit pas dépasser 55 caractères.";
    }

    // Vérifie la longueur du mot de passe
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    // Vérifie que les mots de passe correspondent
    if ($password !== $confirmPassword) {
        $errors[] = "Les mots de passe doivent être identiques.";
    }

    // Si aucune erreur, afficher un message de confirmation
    if (empty($errors)) {
        echo "<p style='color: green;'>Merci $name, votre inscription est réussie.</p>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Formulaire d'inscription</title>
</head>
<body>
    <h2>Formulaire d'inscription</h2>

    <!-- Affichage des erreurs -->
    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Formulaire -->
    <form action="" method="post">
        <label for="name">Nom :</label><br>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($name) ?>"><br><br>

        <label for="email">Email :</label><br>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>"><br><br>

        <label for="password">Mot de passe :</label><br>
        <input 
            type="password" 
            id="password" 
            name="password" 
            required
            pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
            title="Au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial."><br><br>

        <label for="confirm_motdepasse">Confirmer le mot de passe :</label><br>
        <input type="password" id="confirm_motdepasse" name="confirm_motdepasse" required><br><br>

        <input type="submit" value="Envoyer">
    </form>
</body>
</html>
