<?php

if(!isset($_GET["m"]))
    header("Location:../vente.php");
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
                <?php if(strcmp($_GET["m"], "confirm") == 0) : ?>
                    <div class="w3-panel w3-green w3-padding-none">
                        <h3 >Vente réussie</h3>
                        <p>Si le produit reçu ne correspond pas il vous sera renvoyé et votre cagnotte ne sera pas augmenté.</p>
                    </div>
                <?php elseif(strcmp($_GET["m"], "err_count") == 0) : ?>
                    <div class="w3-panel w3-red w3-margin">
                        <h3 >Erreur</h3>
                        <p>Vous ne pouvez plus vendre autant de produit</p>
                    </div>
                <?php endif ?>
            <?php endif ?>
            <div class="w3-padding">
                <a href="../vente.php" class="w3-button w3-theme ">Revenir à la vente</a>
            </div>
        </div>
</body>
</html>




