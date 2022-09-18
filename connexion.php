<?php 
include_once("includes/traitement_connexion.php"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="CSS/default.css">
</head>
<body class="w3-content" style="max-width:500px;padding-top:100px;">
    <?php if(isset($error)) :  ?>
        <div class="w3-panel w3-red">
            <h3>Erreur</h3>
                <p><?php echo $error ?></p>
        </div>
    <?php endif;?>
    <div class="w3-container">
        <div class="w3-card-4">
            <div class="w3-container w3-2017-navy-peony">
                <h2 class="w3-center">Connexion</h2>
            </div>

            <form class="w3-container w3-padding-16" method="POST" >
                <p>
                    <label for="login">Identifiant</label>
                    <input class="w3-input w3-border w3-round" type="text" name="login" id="login" required="required">
                </p>
                <p> 
                    <label for="password">Mot de passe</label>
                    <input class="w3-input w3-border w3-round" type="password" name="password" id="password" required="required">
                </p>
                <p>
                        <input class="w3-check" type="checkbox" name="remember_me" id="remember_me" value="checked" />
                        <label for="remember_me">Se souvenir de moi </label>
                </p>
                <p class="w3-center"> <a href="inscription.php">S'inscrire</a> </p>
                <input class="w3-btn w3-2017-navy-peony w3-right" type="submit" value="Sousmettre">
                
            </form>
        </div>
    </div>
</body>
</html>