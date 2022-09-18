<?php
session_start();
$TITLE = "Panier";
if(!isset($_SESSION["id"])){
  header("Location:connexion.php");
  exit();
}

require_once("includes/bd.php");
//Requête préparée
$panier = $_SESSION["panier"] ?? array();
$total_panier = 0.0;
$stmt_produit = mysqli_prepare($db, "SELECT TypeItem.id, TypeItem.name as Nom_produit, Brand.name as marque, quantity, price, picture_path
                            FROM TypeItem JOIN BusinessSell ON (TypeItem.id = BusinessSell.typeItem) JOIN Brand ON (TypeItem.brand = Brand.id)
                            WHERE Typeitem = ? AND Business =  ?");
if(!empty($panier) && isset($_GET["err"])){
  if(strcmp($_GET["err"],"stash") == 0)
    $message = "Vous n'avez pas assez d'argent dans votre cagnotte";
  elseif(strcmp($_GET["err"],"quantity") == 0)
    $message = "Un(Des) produit(s) n'est plus disponible dans les quantités demandées";
}
include("includes/header.php");
?>


<div class="w3-content" style="max-width: 80vw;">
  <h2 class="w3-center w3-margin">Votre panier</h2>
  <?php if(isset($message)) : ?>
  <div class="w3-panel w3-amber w3-center  w3-display-container">
    <span onclick="this.parentElement.style.display='none'" class="w3-button w3-display-topright">X</span>
    <p class="w3-large"><?=$message?></p>
  </div>
  <?php endif ?>
  <?php if(empty($panier)) :?>
    <h2 class="w3-center">Vous n'avez pas de produit dans votre panier</h2>
  <?php else : ?>
  <form action="includes/traitement_panier.php" method="POST">
    <div class="w3-responsive">     
      <table class="w3-table w3-bordered">
        <thead>
          <tr>
            <th colspan="2" class="w3-center">Produit</th>
            <th>Prix unitaire</th>
            <th>Quantité</th>
            <th>Sous-total</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($panier as $cle => $valeur) : 
          $param = explode("_", $cle);
          mysqli_stmt_bind_param($stmt_produit,"ii",$param[0],$param[1]);
          mysqli_stmt_execute($stmt_produit);
          $tuple = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_produit));
          if($tuple === NULL)
            continue;
          $total_panier += $tuple["price"]*$panier[$cle];
          
          if(isset($tuple["picture_path"]) && is_file("img/".$tuple["picture_path"])){
              $imageData = base64_encode(file_get_contents("img/".$tuple["picture_path"]));
              // Format the image SRC:  data:{mime};base64,{data};
              $src = 'data: '.mime_content_type("img/".$tuple["picture_path"]).';base64,'.$imageData;
          }
          else
              $src = 'data:png;base64,img/default_item.jpg';
        ?>
          <tr>
            <td>
              <a href="">
                <img src="<?=$src?>" alt="" class="w3-image" style="width: 15vw;">
              </a>
            </td>
            <td style="vertical-align: middle;">
              <a href="achat.php?page=details&product=<?=$param[0]?>&b=<?=$param[1]?>"><?=$tuple["marque"]?></a><br />
              <a href="achat.php?page=details&product=<?=$param[0]?>&b=<?=$param[1]?>"><?=$tuple["Nom_produit"]?></a>
              <br />
              <a href="includes/traitement_panier.php?remove=<?=$cle?>" class="w3-xlarge"><i class="fa fa-trash"></i></a>
            </td>
            <td class="prix_panier" style="vertical-align: middle;"><?=$tuple["price"]?>€</td>
            <td class="quantite" style="vertical-align: middle;">
              <input type="number" name="<?=$param[0]."_".$param[1]?>" value="<?=$panier[$cle]?>" min=1 max=<?=$tuple["quantity"]?> onchange="updateButton()">
            </td>
            <td class="quantite" style="vertical-align: middle;"><span><?=$tuple["price"]*$panier[$cle]?>€</span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="w3-container w3-margin w3-padding" style="text-align: right;">
      <span>Total :</span>
      <span><?=$total_panier?>€</span>
    </div>
    <div class="w3-container" style="text-align : right;">
      <input class="w3-button w3-theme w3-animate-right" type="submit" name="update" id="update" value="Mettre à jour" style="display:none;">
      <input class="w3-button w3-theme" type="submit" name="buy" value="Payer" style="display:inline;">
    </div>
    <div class="w3-container">
      <input class="w3-button w3-red" type="submit" name="clear" value="Vider le panier" style="display:inline;">
    </div>
  </form>
  <?php endif; ?>
  
      
      
</div>
<button id="backToTop" class="topButton w3-button w3-circle w3-large w3-theme w3-hover-text-theme w3-animate-zoom" onclick="topFunction()">
    <i class="fa fa-arrow-up"></i>
</button>

    <script  src="<?=$JS?>"></script>
    </body>
</html>