<?php
if($TITLE == "Achat")
    $BUTTON_TEXT = "Ajouter au panier";
else   
    $BUTTON_TEXT = "Vendre";

if(isset($_GET["product"]) && isset($_GET["b"]) && is_numeric($_GET["product"]) && is_numeric($_GET["b"])) {
    //Requête préparée caractéristiques du produit
    $stmt = mysqli_prepare($db, "SELECT TypeItem.id as id, TypeItem.name as name, picture_path, business, quantity, price, Brand.name as brand 
                                FROM TypeItem JOIN ".$TABLE_NAME." ON (TypeItem.id = ".$TABLE_NAME.".typeItem) JOIN Brand ON (TypeItem.brand = Brand.id)
                                WHERE TypeItem.id = ? AND Business =  ? AND quantity > 0");
    //Liaison des paramètres
    mysqli_stmt_bind_param($stmt,"ii", $_GET["product"], $_GET["b"]);
    mysqli_stmt_execute($stmt);
    $produit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if(!$produit)
        $err = "Ce produit n'existe pas ou n'est plus disponible";

    //Requête préparée cracatéristiques techniques du produit
    $stmt = mysqli_prepare($db, "SELECT attribute, value 
                                FROM TypeItemDetails WHERE typeItem = ?");
    //Liaison des paramètres
    mysqli_stmt_bind_param($stmt,"i", $_GET["product"]);
    mysqli_stmt_execute($stmt);
    $carac = mysqli_fetch_all(mysqli_stmt_get_result($stmt),MYSQLI_ASSOC);

    //Requête préparée cracatéristiques de l'entreprise
    $stmt = mysqli_prepare($db, "SELECT name, country 
                                FROM Business WHERE id = ?");
    //Liaison des paramètres
    mysqli_stmt_bind_param($stmt,"i", $_GET["b"]);
    mysqli_stmt_execute($stmt);
    $entreprise = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    

} 
else {
    $err = "Ce produit n'existe pas";
}

include ("includes/header.php");
?>
<div class="w3-main">
    <div class="w3-content w3-container" id="w3-product-show">
        <?php 
        if(isset($produit["picture_path"]) && is_file("img/".$produit["picture_path"])){
            $imageData = base64_encode(file_get_contents("img/".$produit["picture_path"]));
            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data: '.mime_content_type("img/".$produit["picture_path"]).';base64,'.$imageData;
        }
        else
            $src = 'data:png;base64,img/default_item.jpg';
        ?>
        <div class="w3-row-padding">
            <?php if(!isset($err)) : ?>
            <div  class="w3-col l5 w3-center ">
            <img class="w3-image" src="<?=$src?>" id="img-produit" alt="">
            </div>
            <div class="w3-col l7">
                <h2><?=$produit["brand"]?><br/><?=$produit["name"]?></h2>
                <span class="w3-text-grey"> <?=$produit["price"]?>€ </span> <br/>
                <form action="includes/traitement_<?= strcmp($TITLE,"Achat") == 0 ? "achat" : "vente" ?>.php" method="POST">
                    <p><input class="w3-input w3-border" type="number" name="quantity" value="1" min="1" max="<?=$produit['quantity']?>" placeholder="Quantité" style="width:75%;" required> </p>
                    <input class="w3-input" type="hidden" name="product_id" value="<?=$produit['id']?>">
                    <input class="w3-input" type="hidden" name="business_id" value="<?=$produit['business']?>">
                    <input class="w3-button w3-border w3-round-large w3-2017-navy-peony w3-large w3-hover-black" type="submit" value="<?=$BUTTON_TEXT?>" style="width:75%;">
                </form>
                <div>
                    <h4>Caractéristiques du vendeur</h4>
                    <table>
                        <tr>
                            <td><b>Entreprise :</b></td>
                            <td><?=ucfirst($entreprise["name"])?></td>
                        </tr>
                        <tr>
                            <td><b>Localisation : </b></td>
                            <td><?=ucfirst($entreprise["country"])?></td>
                        </tr>
                    </table>
                    <h4>Caractéristiques techniques</h4>
                    <table>
                    <?php foreach($carac as $produit) : ?>
                        <tr>
                        <td><b><?=ucfirst($produit["attribute"])?> :</b></td>
                        <td class="w3-right-align"><?=$produit["value"]?></td>
                        </tr>
                    <?php endforeach ?>
                    </table>
                </div>
            </div>
            <?php else : ?>
                <h1 class="w3-text-red w3-center"><b><?=$err?></b></h1>
                <div class="w3-margin w3-center">
                    <a href="<?= strcmp($TITLE,"Achat") == 0 ? "achat" : "vente" ?>.php" class="w3-button w3-theme">Revenir en arrière</a>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<button id="backToTop" class="topButton w3-button w3-circle w3-large w3-theme w3-hover-text-theme w3-animate-zoom" onclick="topFunction()">
    <i class="fa fa-arrow-up"></i>
</button>

<script  src="<?=$JS?>"></script>
</body>
</html>