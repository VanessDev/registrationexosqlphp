<?php
require_once 'config/database.php';

$errors = [];
$message = "";
// =========================================
// condition qui contient la logique de traitement du formulaire quand on recoit une request POST
// ==========================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //recuperation des données du formulaire
    //nettoyage des données du formulaire
    $username = trim(htmlspecialchars($_POST["username"]) ?? '');
    $email = trim(htmlspecialchars($_POST["email"]) ?? '');
    $password = $_POST["password"] ?? '';
    $confirmPassword = $_POST["confirmPassword"] ?? '';

    //validation username
    //valide que le champ soit remplis
    if (empty($username)) {
        $errors[] = "nom obligatoire !";
        //valide avec la function strlen si la string est de plus de 3 carac
    } elseif (strlen($username) < 3) {
        $errors[] = "mini 3 carac";
        //valide avec la function strlen si la string est de moins de 55 carac
    } elseif (strlen($username) > 55) {
        $errors[] = "max 55 carac";
    }
    //validation email
    if (empty($email)) {
        $errors[] = "email obligatoire ! ( connard )";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "votre adresse ne correspond au format mail classique";
    }

    //validation password
    if (empty($password)) {
        $errors[] = "password obligatoire";
    } elseif (strlen($password) < 3) {
        $errors[] = "password trop juste";
        // normalement ici on met un pattern pour le mdp
    } elseif ($password !== $confirmPassword) {
        $errors[] = "mot de passe doivent etre identique";
    }

    if (empty($errors)) {
        //logique de traitement en db
        $pdo = dbConnexion();

        //verifier si l'adresse mail est utilisé ou non
        $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");

        //la methode execute de mon objet pdo execute la request préparée
        $checkEmail->execute([$email]);

        //une condition pour vérifier si je recupere quelque chose
        if ($checkEmail->rowCount() > 0) {
            $errors[] = "email déja utilisé";
        } else {
            //dans le cas ou tout va bien ! email pas utilisé

            //hashage du mdp avec la fonction password_hash
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            //insertion des données en db
            // INSERT INTO users (username, email, password)VALUES ("atif","atif@gmail.com","lijezfoifjerlkjf")
            $insertUser = $pdo->prepare("
                INSERT INTO users (nom, prenom, email, password) 
                VALUES (?, ?, ?)
                ");

            $insertUser->execute([$nom, $prenom, $email, $hashPassword]);

            $message = "super mega cool vous êtes enregistré $nom";
        }
        // try {

        // } catch () {

        // }
    }


}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/style/style.css">
</head>

<body>
    <section>
        <form action="" method="POST">
            <?php
            foreach ($errors as $error) {
                echo $error;
            }
            if (!empty($message)) {
                echo $message;
            }
            ?>
            <div>
                <label for="username">Pseudo</label>
                <input type="text" id="username" name="username" placeholder="Entrez votre pseudo" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required placeholder="Entrez votre email">
            </div>
            <div>
                <label for="password">password</label>
                <input type="password" name="password" id="password" required placeholder="entrer votre mdp">
            </div>
            <div>
                <label for="confirmPassword">confirmer password</label>
                <input type="password" name="confirmPassword" id="confirmPassword" required
                    placeholder="confirmer votre mdp">
            </div>
            <div>
                <input type="submit" value="envoyer">
            </div>
        </form>
    </section>
</body>

</html>