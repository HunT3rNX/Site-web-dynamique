<?php
//On inclut pas bd.php car cette page est inclut dans Achat.php ou Vente.php
//Même fichier pour page achat et vente, seule la table change

if($TITLE == "Achat")
    $PAGE_NAME = "achat.php";
else   
    $PAGE_NAME = "vente.php";

$conditions = ""; //Chaine de caractères contenant les filtres sous formes de ? pour la requête préparée (ex : AND Price BETWEEN ? AND ?)
$filtres = ""; //Chaine de caractères au format d'une URL pour passer les filtres avec la pagination (ex : entreprise[]=nvidia&min=0&max=500)
$param = "";   //Chaine de caractères contenant les types des arguments pour la méthode bind_param (ex : 'ssii')
$value_array = array(); //Tableau associatif pour les arguments (filtres) à bind pour la requête préparée (ex : [0] => ("NVIDIA") [1] => (0) [2] => (500) )

//-----------------Récupération des filtres dans le tableau GET-------------------------
if(isset($_GET["s"]) && !empty($_GET["s"])) {
    array_push($value_array,$_GET["s"]);
    $param .= "s";
    $conditions .= "AND TypeItem.name LIKE CONCAT('%',?,'%') ";
    $filtres .= "s=".$_GET["s"];
}
if(isset($_GET["e"])) {
    foreach($_GET["e"] as $entreprise) array_push($value_array,$entreprise); //Array_merge ne fonctionne pas ni +=
    $param .= str_repeat("s", count($_GET["e"]));
    $conditions .= "AND Business.name IN (".implode(',', array_fill(0, count($_GET["e"]),'?')).") ";
    $filtres .= "&".http_build_query($_GET["e"], "e[]");
}
if(isset($_GET["pays"])) {
    foreach($_GET["pays"] as $pays) array_push($value_array,$pays); //Array_merge ne fonctionne pas ni +=
    $param .= str_repeat("s", count($_GET["pays"]));
    $conditions .= "AND Business.country IN (".implode(',', array_fill(0, count($_GET["pays"]),'?')).") ";
    $filtres .= "&".http_build_query($_GET["pays"], "pays[]");
}
if(isset($_GET["marque"])) {
    foreach($_GET["marque"] as $marque) array_push($value_array,$marque); //Array_merge ne fonctionne pas ni +=
    $param .= str_repeat("i", count($_GET["marque"]));
    $conditions .= "AND TypeItem.brand IN (".implode(',', array_fill(0, count($_GET["marque"]),'?')).") ";
    $filtres .= "&".http_build_query($_GET["marque"], "marque[]");
}
if(isset($_GET["min"]) && isset($_GET["max"]) && is_numeric($_GET["min"]) && is_numeric($_GET["max"]) && $_GET["min"] >= 0  && $_GET["max"] <= 3000 && $_GET["min"] < $_GET["max"] ){
    $min_price = $_GET["min"];
    $max_price = $_GET["max"];
    $param .= "ii";
    array_push($value_array,$min_price);
    array_push($value_array,$max_price);
    $conditions .= "AND Price BETWEEN ? AND ?";
    $filtres .= "&min=".$_GET["min"];
    $filtres .= "&max=".$_GET["max"];
}
else{
    $min_price = "0";
    $max_price = "3000";
}
//-------------------------------------------------------------------------------------

//Récupération du nom des entreprises
$requete = mysqli_query($db,"SELECT id,name FROM Business ORDER BY name");
$tableauEntreprise = mysqli_fetch_all($requete,MYSQLI_ASSOC);

//Récupération des Pays
$requete = mysqli_query($db,"SELECT DISTINCT country FROM Business ORDER BY country");
$tableauPays= mysqli_fetch_all($requete,MYSQLI_ASSOC);

//Récupération des Marques
$requete = mysqli_query($db,"SELECT id,name FROM Brand ORDER BY name");
$tableauMarque= mysqli_fetch_all($requete,MYSQLI_ASSOC);

//Nombre d'éléments par page
$array_element_par_page = array(8,16,24);
if(isset($_GET["nb_p"]) && is_numeric($_GET["nb_p"]) && in_array($_GET["nb_p"],$array_element_par_page)){
    $element_par_page = (int) $_GET["nb_p"];
    $filtres .= "&nb_p=".$element_par_page;
}
else 
    $element_par_page = $array_element_par_page[0];

//Page actuelle
$page_courante = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
//Calcul du nombre d'élément déjà affichés
$start = ($page_courante > 1) ? ($page_courante - 1) * $element_par_page : 0;
//Requete pour les produits
$stmt = mysqli_prepare($db,"SELECT SQL_CALC_FOUND_ROWS Business.name BusinessName, Business.id as BusinessID,TypeItem.name as TypeItemName, TypeItem.id as TypeItemID, Quantity, Price, picture_path, Brand.name as brand 
                            FROM ".$TABLE_NAME." JOIN Business ON (".$TABLE_NAME.".business = Business.id) 
                            JOIN TypeItem ON (".$TABLE_NAME.".typeItem = TypeItem.id) JOIN Brand ON (TypeItem.brand = Brand.id)
                            WHERE Quantity > 0 $conditions
                            ORDER BY Brand.name,TypeItem.name ASC
                            LIMIT $element_par_page OFFSET $start;");
//Si il n'y a pas de filtre, pas de bind de paramètres
if(!empty($param))
    mysqli_stmt_bind_param($stmt,$param,...$value_array);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tableauProduit = mysqli_fetch_all($result,MYSQLI_ASSOC);

//Calcul du nombre de page
$result2 = mysqli_query($db,"SELECT FOUND_ROWS() AS nombre;");
$total_produit = mysqli_fetch_assoc($result2)['nombre'];

//Nombre de pages
$nombre_de_page = ceil(($total_produit/$element_par_page));
//Si jamais l'utilisateur change le numéro de la page dans l'URL directement
if($page_courante > $nombre_de_page)
    header("Location:./$PAGE_NAME?$filtres&p=$nombre_de_page");

include ("includes/header.php");
?>

<!-- Container -->
<div class="w3-main">
    <!-- Grille -->
    <div class="w3-row-padding">
        <!-- Colonne de gauche -->
        <div class="w3-col l3">
            <!-- Filtres -->
            <div class="w3-card w3-round">
                <h3 class="w3-center w3-theme w3-padding" style="margin-top: 0 ;">Filtres</h3>
                <div id="filterColumn" class="w3-container w3-padding w3-white">
                    <form id="formid" method="GET">
                        <!-- Barre de recherche --> 
                        <div id="recherche" class=" w3-padding">
                            <?php 
                                if(isset($_GET["s"])) $text = htmlentities($_GET["s"]);
                                else $text = "";
                            ?>
                            <input type="search" class="w3-input" name="s" id="s" placeholder="Rechercher un produit" value="<?=$text?>">
                        </div>
                        <br>
                        <!-- Entreprises --> 
                        <a id="entrepriseButton" class="w3-block w3-button w3-border w3-hover-theme" onclick="dropdown('entreprise',this.id)">Entreprise </a>
                        <div id="entreprise" class="w3-hide w3-border w3-padding">
                            <?php foreach($tableauEntreprise as $entreprise) : ?>
                                <?php 
                                    if(isset($_GET["e"]) && in_array($entreprise['name'],$_GET["e"])) $checked = "checked";
                                    else $checked = "";
                                ?>
                                <input id="entreprise<?=$entreprise['id']?>" class="w3-check w3-large" type="checkbox" name="e[]" value="<?=$entreprise['name']?>" <?=$checked?>>
                                <label class="w3-large" for="entreprise<?=$entreprise['id']?>"> <?=ucfirst($entreprise['name'])?></label><br>
                            <?php endforeach ?>
                        </div>
                        <!-- Pays --> 
                        <a id="paysButton" class="w3-block w3-button w3-border w3-hover-theme" onclick="dropdown('pays',this.id)">Localisation de l'entreprise </a>
                        <div id="pays" class="w3-hide w3-border w3-padding">
                            <?php foreach($tableauPays as $pays) : ?>
                                <?php 
                                    if(isset($_GET["pays"]) && in_array($pays['country'],$_GET["pays"])) $checked = "checked";
                                    else $checked = "";
                                ?>
                                <input id="<?=str_replace(' ','_',$pays['country'])?>" class="w3-check w3-large" type="checkbox" name="pays[]" value="<?=$pays['country']?>" <?=$checked?>>
                                <label class="w3-large" for="<?=str_replace(' ','_',$pays['country'])?>"> <?=ucfirst($pays['country'])?></label><br>
                            <?php endforeach ?>
                        </div>
                        <!-- Marques --> 
                        <a id="marqueButton" class="w3-block w3-button w3-border w3-hover-theme" onclick="dropdown('marque',this.id)">Marque du produit </a>
                        <div id="marque" class="w3-hide w3-border w3-padding">
                            <?php foreach($tableauMarque as $marque) : ?>
                                <?php 
                                    if(isset($_GET["marque"]) && in_array($marque['id'],$_GET["marque"])) $checked = "checked";
                                    else $checked = "";
                                ?>
                                <input id="marque<?=$marque['id']?>" class="w3-check w3-large" type="checkbox" name="marque[]" value="<?=$marque['id']?>" <?=$checked?>>
                                <label class="w3-large" for="marque<?=$marque['id']?>"> <?=ucfirst($marque['name'])?></label><br>
                            <?php endforeach ?>
                        </div>
                        <br>
                        <!-- Prix --> 
                        <div class="wrapper">
                            <h5 class="w3-center">Prix</h5>
                            <div class="price-input">
                                <div class="field">
                                    <input type="number" class="input-min" value="<?=$min_price?>">
                                </div>
                                <div class="separator">-</div>
                                <div class="field">
                                    <input type="number" class="input-max" value="<?=$max_price?>">
                                </div>
                            </div>
                            <div class="slider">
                                <div class="progress" style="left: <?=($min_price/3000)*100?>%;right: <?=100-($max_price/3000)*100?>%;"></div> 
                                <?php // CSS dans HTML car impossible de mettre des calculs dans le CSS. Possible de calculer avec le Javacript mais je ne suis pas doué avec le JS ?>
                            </div>
                            <div class="range-input">
                                <input type="range" name="min" class="range-min" min="0" max="3000" value="<?=$min_price?>" step="5">
                                <input type="range" name="max" class="range-max" min="0" max="3000" value="<?=$max_price?>" step="5">
                            </div>
                        </div>
                    </form>
                    <input class="w3-button w3-theme-l1 w3-right" form="formid" type="submit" value="Rechercher"/>
                    <a class="w3-button w3-2017-grenadine" href="?">Supprimer les filtres</a>
                </div>
            </div>
         </div>
        <!-- Colonne centrale -->
        <div class="w3-col l9" >
            <!-- Produits --> 
            <div class="w3-row-padding "> 
                <?php foreach($tableauProduit as $produit) : ?>
                    <?php $lien_produit = '?page=details&product='.$produit["TypeItemID"].'&b='.$produit["BusinessID"] ?>
                    <div class="w3-col l3 m4 s6 w3-animate-zoom" style="margin-bottom:20px">
                        <div class="w3-card-2">
                            <div class="w3-center w3-padding image-container">
                                <?php 
                                if(isset($produit["picture_path"]) && is_file("img/".$produit["picture_path"])){
                                    $imageData = base64_encode(file_get_contents("img/".$produit["picture_path"]));
                                    // Format the image SRC:  data:{mime};base64,{data};
                                    $src = 'data: '.mime_content_type("img/".$produit["picture_path"]).';base64,'.$imageData;
                                }
                                else
                                    $src = 'data:png;base64,img/default_item.jpg';
                                ?>
                                <a href="<?=$lien_produit?>"> <img class="w3-image w3-hover-opacity" src="<?=$src?>" alt="">  </a>
                            </div>
                            <div class="w3-container w3-card-4 product-description w3-padding-24">
                                <a href="<?=$lien_produit?>" class="w3-text-theme-d2 w3-center">
                                    <p>
                                        <?=$produit["brand"]?><br><?=$produit["TypeItemName"]?>
                                    </p>
                                </a>
                                <table class="table-description">
                                    <tbody>
                                        <tr>
                                            <td class="w3-small"> <b><?= (strcmp($PAGE_NAME,"achat.php") == 0) ? "Vendeur" : "Acheteur"?>: </b></td>
                                            <td class="w3-small w3-right-align"><?=$produit["BusinessName"]?></td>
                                        </tr>
                                        <tr>
                                            <td class="w3-small"><b>Prix :</b></td>
                                            <td class="w3-small w3-right-align"><?=$produit["Price"]?>€</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                 </div>
                <?php endforeach ?>
            </div>
            <!-- Pagination --> 
            <?php if($nombre_de_page > 0) : ?>
            <div class="w3-container w3-center w3-padding w3-margin-top">
                <div class="w3-bar w3-border w3-round">
                    <!-- Bouton < -->
                    <?php if($page_courante > 1) : ?>
                        <a href="?p=<?=$page_courante - 1?>" class="w3-button w3-bar-item">&laquo;</a>
                    <?php else : ?>
                        <a class="w3-button w3-bar-item ">&laquo;</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $nombre_de_page; $i++) : ?>
                        <?php if($page_courante == $i) : ?>
                            <a class="w3-button w3-theme-d2 w3-bar-item"><?=$i?></a>
                        <?php else : ?>
                            <a href="?<?=$filtres?>&p=<?=$i?>" class="w3-button w3-bar-item"><?=$i?></a>
                        <?php endif;?>
                    <?php endfor;?>
                    <?php if($page_courante < $nombre_de_page) : ?>
                        <a href="?p=<?=$page_courante + 1?>" class="w3-button w3-bar-item">&raquo;</a>
                    <!-- Bouton > -->
                    <?php else : ?>
                        <a class="w3-button w3-bar-item ">&raquo;</a>
                    <?php endif; ?>
                </div>
                <select class="w3-round w3-right w3-margin-top w3-margin-bottom" name="nb_p" form="formid" onchange="document.getElementById('formid').submit()">
                    <?php foreach($array_element_par_page as $i) : ?>
                        <option value="<?=$i?>" <?php if($element_par_page == $i) echo "selected"?>><?=$i?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <?php else : ?>
            <div class="w3-panel w3-center">
                <h1 class="w3-text-theme"><b>Pas de produit trouvé</b></h1>
            </div>
            <?php endif ?>
        </div>
    </div>
</div>