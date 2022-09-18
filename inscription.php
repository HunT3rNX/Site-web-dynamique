<?php 
include_once("includes/traitement_inscription.php");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-colors-2017.css">
</head>
<body class="w3-content" style="max-width:500px;padding-top:100px;">
    <?php if(!empty($errors)) :  ?>
        <div class="w3-panel w3-red">
            <h3>Erreur</h3>
            <?php foreach($errors as $message) : ?>
                <p><?php echo $message ?></p>
            <?php endforeach?>
        </div>
    <?php endif;?>
   
    <div class="w3-container">
        <div class="w3-card-4">
            <div class="w3-container w3-2017-navy-peony">
                <h2 class="w3-center">Inscription</h2>
            </div>
            <form class="w3-container w3-padding-16 " method="POST">
                <div>
                <p> <input class="w3-input w3-border w3-round" type="text" name="login" id="login" placeholder="Identifiant" required="required"> </p>
                <p> <input class="w3-input w3-border w3-round" type="text" name="name" id="name" placeholder="Nom" required="required"> </p>
                <p> <input class="w3-input w3-border w3-round" type="text" name="firstname" id="firstname" placeholder="Prénom" required="required"> </p>
                <p> <input class="w3-input w3-border w3-round" type="email" name="email" id="email" placeholder="Email" required="required"> </p>
                <p> <input class="w3-input w3-border w3-round" type="password" name="password" id="password" placeholder="Mot de passe" required="required"> </p>
                <p> <input class="w3-input w3-border w3-round" type="password" name="rpassword" id="rpassword" placeholder="Confirmer le mot de passe" required="required"> </p>
                </div>
                <div>
                    <label>Vous êtes déjà inscrit ? </label> <br>
                    <a class="w3-btn w3-2017-navy-peony w3-ripple w3-left " href="connexion.php">Se connecter</a>
                    <input class="w3-btn w3-2017-navy-peony w3-ripple w3-right" type="submit" value="Soumettre"> 
                </div>
            </form>
        </div>
    </div>
</body>
</html>