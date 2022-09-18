<?php
session_start();
if(!isset($_GET["m"]) || !empty($_SESSION["panier"]))
    header("Location:../index.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation vente</title>
    <link rel="stylesheet" href="../CSS/default.css">
</head>
<body class="w3-content" style="max-width:500px;padding-top:100px;">
        <div class="w3-card-4 w3-center ">
            <?php if(isset($_GET["m"])) : ?>
                <?php if(strcmp($_GET["m"], "t") == 0) : ?>
                    <div class="w3-panel w3-green w3-padding-none">
                        <h3 >Achat réussie</h3>
                        <p>Vous recevrez bientôt vos produits.</p>
                    </div>
                <?php endif ?>
            <?php endif ?>
            <div class="w3-padding">
                <a href="../index.php" class="w3-button w3-theme ">Revenir à l'accueil</a>
            </div>
        </div>
</body>
</html>




