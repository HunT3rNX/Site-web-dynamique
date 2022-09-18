<?php
session_start();

if(!isset($_SESSION["id"])){
  header("Location:../connexion.php");
  exit();
}
require_once("bd.php");

if(is_numeric($_POST["product_id"]) && is_numeric($_POST["business_id"]) && is_numeric($_POST["quantity"]) && $_POST["quantity"] > 0){
 
  $stmt = mysqli_prepare($db, "SELECT quantity
                            FROM TypeItem JOIN BusinessSell ON (TypeItem.id = BusinessSell.typeItem)
                            WHERE Typeitem = ? AND Business =  ?");
   mysqli_stmt_bind_param($stmt,"ii",$_POST["product_id"],$_POST["business_id"]);
   mysqli_stmt_execute($stmt);
   $tuple = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
  if($tuple === NULL){ 
    header("Location:../achat.php");
    mysqli_close($db); 
    exit();
  }
  if(isset($_SESSION["panier"]) && array_key_exists($_POST["product_id"]."_".$_POST["business_id"],$_SESSION["panier"]) ){
    if($_SESSION["panier"][$_POST["product_id"]."_".$_POST["business_id"]] + $_POST["quantity"] <=  $tuple["quantity"])
      $_SESSION["panier"][$_POST["product_id"]."_".$_POST["business_id"]] += $_POST["quantity"];
    else{
      header("Location:../achat.php?err=quantity");
      mysqli_close($db); 
      exit();
    }
      
  }
  elseif($_POST["quantity"] <= $tuple["quantity"])
    $_SESSION["panier"][$_POST["product_id"]."_".$_POST["business_id"]] = $_POST["quantity"];
  else{
    header("Location:../achat.php?err=quantity");
    mysqli_close($db); 
    exit();
  }
}
header("Location:../achat.php");
mysqli_close($db); 
?>
