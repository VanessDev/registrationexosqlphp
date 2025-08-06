<?php
$name = $email = $password = "";
$erreur = "";

// Vérifie si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Vérifications
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse email invalide.";
    } elseif (empty($name) || empty($email) || empty($password)) {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        echo "Merci $name, votre email a été reçu.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire exo Mysql php</title>
</head>

<body>
    <section>
        <?php if (!empty($erreur)) : ?>
            <p style="color: red;"><?= $erreur ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="name">Nom :</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Envoyer</button>
        </form>
    </section>
</body>

</html>
