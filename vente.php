<?php
session_start();
require_once("includes/bd.php");

$TITLE = "Vente";
$TABLE_NAME = "BusinessBuy";
?>

<?php 
    if(isset($_GET["page"]) && (strcmp($_GET["page"], "details") == 0) )
        include ("includes/produit_show.php");
    else
        include ("includes/produit_overview.php"); ?>
<?php
  if(!isset($_GET["page"]) || strcmp($_GET["page"], "details") !== 0)
    include ("includes/footer.php");
  mysqli_close($db);    
?>


