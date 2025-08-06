<?php
// Vérifie si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupère et sécurise les données du formulaire
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $motdepasse = trim($_POST['motdepasse'] ?? '');
    $confirm = trim($_POST['confirm_motdepasse'] ?? '');

    // Vérifie si tous les champs sont remplis
    if (empty($nom) || empty($email) || empty($motdepasse) || empty($confirm)) {
        echo "Tous les champs sont requis.";
        exit;
    }

    // Vérifie si l’email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresse email invalide.";
        exit;
    }

    // Vérifie si les deux mots de passe correspondent
    if ($motdepasse !== $confirm) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    // Tu peux maintenant faire un traitement (comme stocker en base de données)
    // Exemple d'affichage :
    echo "Merci $nom, votre inscription est enregistrée.";
    // Pour un vrai projet, tu hasherais le mot de passe ici
} else {
    // Si la page est accédée sans POST
    echo "Accès non autorisé.";
}
?>