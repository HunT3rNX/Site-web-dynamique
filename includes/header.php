<?php
$LOGO = "./img/logo.png";
$PICTURE_PATH = "data:jpg;base64,".base64_encode(file_get_contents("img/default.jpg"));
$TITLE = $TITLE ?? "Page";
$JS = "JS/script.js";
$PAGES = array("Achat" => "./achat.php","Vente" => "./vente.php");
$CONNEXION_PAGE = "connexion.php";
$DECONNEXION_PAGE = "includes/deconnexion.php";

if(isset($_SESSION)){
    //Requête préparée pour récupérer le chemin de l'image
    $stmt = mysqli_prepare($db, "SELECT picture_path FROM CustomerProtectedData WHERE id = ?");
    //Liaison des paramètres
    mysqli_stmt_bind_param($stmt,"i", $_SESSION['id']);
    //Execution
    mysqli_stmt_execute($stmt);
    //Récupération de l'id
    $table = mysqli_stmt_get_result($stmt);
    $tuple = mysqli_fetch_assoc($table);
    //Si l'utilisateur à une photo de profil
    if(isset($tuple["picture_path"]) && is_file("img/".$tuple["picture_path"])){
        $imageData = base64_encode(file_get_contents("img/".$tuple["picture_path"]));
        // Format the image SRC:  data:{mime};base64,{data};
        $PICTURE_PATH = 'data: '.mime_content_type("img/".$tuple["picture_path"]).';base64,'.$imageData;
    }
}
?>
    <!DOCTYPE html>
    <html lang="fr">
        <title> <?= $TITLE ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="CSS/default.css">
        <body>  
            <header class="w3-card">
                <!-- Logo -->
                <div class="w3-center w3-padding w3-theme-l2 w3-border-theme w3-bottombar">
                    <img src="<?='data:png;base64,'.base64_encode(file_get_contents("img/logo.png"));?>" alt="" id="logo" class="w3-image">
                </div>
                <div class="w3-bar w3-theme-l2">
                    <div class=" w3-bar-item w3-xlarge">
                        <!-- Bouton accueil -->
                        <?php if($TITLE != "Accueil") : ?>
                            <a href="./index.php" class="w3-button w3-bar-item w3-hover-white"><i class="fa fa-home w3-xxlarge"></i></a>
                        <?php else : ?>
                            <a href="./index.php" class="w3-button w3-bar-item w3-theme-d2 w3-hover-white"><i class="fa fa-home w3-xxlarge"></i></a>
                        <?php endif;?>
                        <!-- Bouton différentes pages --> 
                        <?php 
                            foreach($PAGES as $nomPage => $lien){
                                if($TITLE != $nomPage) 
                                    echo "<a href='".$lien."' class='w3-button w3-hide-small w3-bar-item w3-hover-white'><b>".$nomPage."</b></a>";
                                else
                                    echo "<a href='".$lien."' class='w3-button w3-hide-small w3-bar-item w3-theme-d2 w3-hover-white'><b>".$nomPage."</b></a>";
                            }
                        ?>  
                    </div>
                    <div class="w3-bar-item w3-right">
                        <!-- Icône Panier --> 
                        <a href="panier.php" class=""> <i class="fa fa-shopping-cart w3-button w3-xxlarge w3-padding-top w3-margin-right w3-hover-white <?php if($TITLE == "Panier") echo "w3-theme-d2"?>" style="position: relative;"> 
                        <?php if(isset($_SESSION["panier"]) || !empty($_SESSION["panier"])) : ?>       
                        <span class="my-badge w3-small w3-sans-serif">
                            <?=array_sum($_SESSION["panier"])?>
                        </span>
                        <?php endif; ?> 
                        </i></a>
                        <!-- Bouton menu burger (mobile uniquement) -->
                        <a href="javascript:void(0)" class="w3-hide-large w3-hide-medium w3-xxlarge w3-hover-none w3-button w3-ripple" onclick="burgerMenu()" style="padding: 8px 0 ;"><i class="fa fa-reorder"></i></a>
                        <!-- Image -->
                        <img src="<?=$PICTURE_PATH?>" style="width: 50px;height:50px;" class="w3-hover-opacity w3-circle w3-hide-small w3-image" alt="Icon Profil" onclick="dropdown('Profil')">
                        <!-- Menu déroulant Image (hors mobile) -->
                        <div id="Profil" class="w3-dropdown-content w3-bar-block w3-card-4 w3-right w3-hide-small" style="right:1%">
                            <?php
                                if(isset($_SESSION["id"])){
                                    if($TITLE != "Profil")
                                        echo '<a href="./profil.php" class="w3-bar-item w3-button"><i class="fa fa-user"></i> Profil</a>';
                                    echo '<a href="'.$DECONNEXION_PAGE.'" class="w3-bar-item w3-button"><i class="fa fa-sign-out"></i> Se déconnecter</a>';
                                }
                                    
                                else if (isset($CONNEXION_PAGE))
                                    echo '<a href="'.$CONNEXION_PAGE.'" class="w3-bar-item w3-button"><i class="fa fa-sign-in"></i> Se connecter</a>';
                            ?>
                        </div>
                        
                    </div>
                    
                </div>
                <!--Menu déroulant mobile -->
                <div id="burgerMenu" class="w3-bar-block w3-hide w3-large w3-hide-large w3-hide-medium ">
                    <?php foreach($PAGES as $nomPage => $lien) : ?>
                        <?php if($TITLE != $nomPage) : ?>
                            <a href="<?php echo $lien?>" class="w3-bar-item w3-button w3-border-bottom w3-padding"><?php echo $nomPage?></a>
                        <?php else : ?>
                            <a href="<?php echo $lien?>" class="w3-bar-item w3-button w3-border-bottom w3-padding w3-theme-d2"><?php echo $nomPage?></a>
                        <?php endif ?>
                    <?php endforeach ?>
                    <a class ="w3-bar-item w3-button w3-padding" onclick="dropdown('ProfilBurger')"> Profil </a>
                    <div id="ProfilBurger" class="w3-bar-block w3-hide w3-padding-large w3-medium">
                        <?php
                            
                            if(isset($_SESSION["id"])){
                                if($TITLE != "Profil")
                                    echo '<a href="./profil.php" class="w3-bar-item w3-button"><i class="fa fa-user"></i>Profil</a>';
                                echo '<a href="'.$DECONNEXION_PAGE.'" class="w3-bar-item w3-button"> <i class="fa fa-sign-out"></i> Se déconnecter</a>';
                            }
                            else if (isset($CONNEXION_PAGE))
                                echo '<a href="'.$CONNEXION_PAGE.'" class="w3-bar-item w3-button"><i class="fa fa-sign-in"></i> Se connecter</a>';
                            
                        ?>
                    </div>
                </div>
            </header>