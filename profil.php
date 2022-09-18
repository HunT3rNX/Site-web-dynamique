<?php
session_start();
if(!isset($_SESSION["id"]))
    header("Location:./connexion.php");
require_once("includes/bd.php");
include("includes/traitement_mdp.php");
include("includes/traitement_image.php");

if(isset($_GET["page"]))
    $sousmenu = htmlentities($_GET["page"]);

$TITLE = "Profil";
//Requete pour nom et prénom
$stmt = mysqli_prepare($db, "SELECT Surname, Firstname, Email  
                            FROM CustomerProtectedData 
                            WHERE id = ?");
//Liaison des paramètres
mysqli_stmt_bind_param($stmt,"i", $_SESSION['id']);
//Execution
mysqli_stmt_execute($stmt);
//Mise en tampon des résultats
$table = mysqli_stmt_get_result($stmt);
//Récupération des résultats
$result = mysqli_fetch_assoc($table);

//ucfirst permet de mettre en majuscule la première lettre uniquement
$Nom = ucfirst($result["Surname"]);
$Prenom = ucfirst($result["Firstname"]);
$email = $result["Email"];
//Requete pour la cagnotte
$result = mysqli_query($db, "SELECT Login, Stash  
                            FROM Customer 
                            WHERE id = ".$_SESSION['id']);
//Récupération des résultats
$result = mysqli_fetch_assoc($result);
$Login = $result["Login"];
$Cagnotte = $result["Stash"];
$Pourcentage =  round($Cagnotte * 100/65000, 2, PHP_ROUND_HALF_DOWN);

?>

<?php include ("includes/header.php");?>

<!-- Container -->
<div class="w3-container w3-content" style="max-width:1600px;margin-top:80px">    
    <!-- Grille -->
    <div class="w3-row-padding">
        <!-- Colonne de gauche -->
        <div class="w3-col l2">
            <!-- Profil -->
            <div class="w3-card w3-round w3-white">
                <div class="w3-container">
                    <h4 class="w3-center"><?="Bienvenue ".$Login?></h4>
                    <p class="w3-center"><img src="<?=$PICTURE_PATH?>" class="w3-image" id="img-profil" alt="Avatar"></p>
                    <hr>
                    <p><?= $Nom ?></p> 
                    <p><?=$Prenom ?></p>
                </div>
            </div>
            <br><br>
            <div class="w3-card w3-round w3-white">
                <div class="w3-bar-block w3-white">
                    <a href="./profil.php" class="w3-bar-item w3-button <?php if(!isset($sousmenu)) echo 'w3-theme-d2';?>">Cagnotte</a>
                    <a href="?page=history" class="w3-bar-item w3-button <?php if(isset($sousmenu) && strcmp($sousmenu,"history") == 0) echo 'w3-theme-d2';?>">Historique des commandes</a>
                    <a href="?page=info" class="w3-bar-item w3-button <?php if(isset($sousmenu) && strcmp($sousmenu,"info") == 0) echo 'w3-theme-d2';?>">Informations Personnelles</a>
                    <a href="?page=picture" class="w3-bar-item w3-button <?php if(isset($sousmenu) && strcmp($sousmenu,"picture") == 0) echo 'w3-theme-d2';?>">Photo de profil</a>
                </div>
            </div>
        </div>
        <!-- Colonne centrale -->
        <div class="w3-col l10 w3-margin-top w3-margin-bottom">
            <!-- Cagnotte (Visible par défaut -->
            <?php 
                if(!isset($sousmenu)) 
                    include("includes/profil_cagnotte.php"); 
                //Historique des commandes
                else if(isset($sousmenu) && strcmp($sousmenu,"history") == 0) 
                    include("includes/profil_commande.php");
                //Informations Personnelles (Non visible par défaut 
                else if(isset($sousmenu) && strcmp($sousmenu,"info") == 0) 
                    include("includes/profil_info_perso.php");
                //Modification de la photo de profil
                else if(isset($sousmenu) && strcmp($sousmenu,"picture") == 0)
                    include("includes/profil_pdp.php"); 
            ?>
        </div>
    </div>
</div>
<button id="backToTop" class="topButton w3-button w3-circle w3-large w3-theme w3-hover-text-theme w3-animate-zoom" onclick="topFunction()">
    <i class="fa fa-arrow-up"></i>
</button>
<script  src="<?=$JS?>"></script>
</body>
</html>
<?php
    mysqli_close($db);
?>