<?php
session_start();
require_once("includes/bd.php");

$TITLE = "Achat";
$TABLE_NAME = "BusinessSell";

?>
<?php 
    if(isset($_GET["page"]) && (strcmp($_GET["page"], "details") == 0))
        include ("includes/produit_show.php");
    else
        include ("includes/produit_overview.php");
?>
<?php if(isset($_GET["err"]) && (strcmp($_GET["err"], "quantity") == 0)): ?>
<div id="panneau" class="w3-modal" style="display: block;">
    <div class="w3-modal-content">
      <div class="w3-panel w3-pale-red w3-center">
        <span onclick="document.getElementById('panneau').style.display='none'" class="w3-button w3-display-topright">&times;</span>
        <p>La quantité que vous voulez ajouter dépasse le stock, allez dans le panier pour modifier la quantité plus précisement</p>
      </div>
    </div>
  </div>
<?php endif?>
<?php
  if(!isset($_GET["page"]) || strcmp($_GET["page"], "details") !== 0)
    include ("includes/footer.php");
  mysqli_close($db);    
?>


