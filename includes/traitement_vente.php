<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if(!isset($_SESSION["id"])){
  header("Location:../connexion.php");
  exit();
}

require_once("bd.php");
//Requête préparée pour savoir si il reste des éléments
$stmt = mysqli_prepare($db, "SELECT Quantity,Price FROM BusinessBuy WHERE typeItem = ? AND Business = ?");
//Liaison des paramètres
mysqli_stmt_bind_param($stmt,"ii", $_POST["product_id"], $_POST["business_id"]);
//Execution
mysqli_stmt_execute($stmt);
//Récupération de l'id
$tableBusiness = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));


//Requête préparée pour récupérer les éléments extraits du produit
$stmt = mysqli_prepare($db, "SELECT element,quantity FROM ExtractionFromTypeItem WHERE typeItem = ?");
//Liaison des paramètres
mysqli_stmt_bind_param($stmt,"i", $_POST["product_id"]);
//Execution
mysqli_stmt_execute($stmt);
$tableElement = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

if($tableBusiness["Quantity"] >= $_POST["quantity"] && $_POST["quantity"] > 0){
  //Requête préparée pour mettre à jour le nombre d'éléments restants
  $stmt = mysqli_prepare($db, "UPDATE BusinessBuy SET Quantity = Quantity - ? 
                    WHERE typeItem = ? AND Business  = ? ");
  //Liaison des paramètres
  mysqli_stmt_bind_param($stmt,"iii",$_POST["quantity"], $_POST["product_id"], $_POST["business_id"]);
  //Execution
  mysqli_stmt_execute($stmt);

  //Requête préparée pour mettre à jour les éléments extraits de l'utilisateur
  $stmt = mysqli_prepare($db,"INSERT INTO CustomerExtraction VALUES (".$_SESSION["id"].",?,?)
                        ON DUPLICATE KEY UPDATE Quantity = Quantity + ? ");
  foreach($tableElement as $element){
    //Liaison des paramètres
    mysqli_stmt_bind_param($stmt,"iii", $element["element"], $element["quantity"], $element["quantity"]);
    //Execution
    for($i = 0; $i < $_POST["quantity"]; $i++)
      mysqli_stmt_execute($stmt);
  }

  $total_vente = $tableBusiness["Price"]*$_POST["quantity"];
  //Requête préparée pour mettre à jour la cagnotte
  $stmt = mysqli_prepare($db,"UPDATE Customer SET Stash = Stash + ? WHERE id = ".$_SESSION["id"]);
  //Liaison des paramètres
  mysqli_stmt_bind_param($stmt,"d",$total_vente);
  //Execution
  mysqli_stmt_execute($stmt);
  
  //Historique commande
  $stmt_order = mysqli_prepare($db, "INSERT INTO Orders(customer_id, price, order_date,buyOrSell) VALUES (?,?,now(),1)");
  mysqli_stmt_bind_param($stmt_order,"ii",$_SESSION["id"],$total_vente);
  mysqli_stmt_execute($stmt_order);
  $order_id = mysqli_insert_id($db);

  $stmt_order_details = mysqli_prepare($db, "INSERT INTO OrdersDetails(order_id, product_id, business_id,quantity,price) VALUES ($order_id,?,?,?,?)");
  mysqli_stmt_bind_param($stmt_order_details,"iiii",$_POST["product_id"], $_POST["business_id"],$_POST["quantity"],$tableBusiness["Price"]);
  mysqli_stmt_execute($stmt_order_details);
  header("Location:confirmation_vente.php?m=confirm");
}
else{
  header("Location:confirmation_vente.php?m=err_count");
}
mysqli_close($db);
?>
