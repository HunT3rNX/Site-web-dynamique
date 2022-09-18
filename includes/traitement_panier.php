<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if(!isset($_SESSION["id"])){
  header("Location:../connexion.php");
  exit();
}
require_once("bd.php");

//Si on appuie sur supprimer un produit du panier
if(isset($_GET["remove"]) && isset($_SESSION["panier"]) && isset($_SESSION["panier"][$_GET["remove"]])){
  unset($_SESSION["panier"][$_GET["remove"]]);
  if(array_sum($_SESSION["panier"]) == 0){
    unset($_SESSION["panier"]);
  }
  header("Location:../panier.php");
  exit();
}

//Si on appuie sur vider le panier
if(isset($_POST["clear"]) && isset($_SESSION["panier"])){
  unset($_SESSION["panier"]);
  header("Location:../panier.php");
  exit();
}

if(isset($_POST["update"]) && isset($_SESSION["panier"])){
  foreach($_POST as $cle => $valeur){
    if(strpos($cle,"_") !== false){
      $quantite = (int) $valeur;
      if(isset($_SESSION["panier"][$cle]))
        $_SESSION["panier"][$cle] = $quantite;
    } 
  }
  header("Location:../panier.php");
  exit();
}

if(isset($_POST["buy"])){
  $total_panier = 0.0;
  $stmt = mysqli_prepare($db, "SELECT quantity,price
                              FROM  BusinessSell 
                              WHERE Typeitem = ? AND Business =  ?");
  foreach($_SESSION["panier"] as $cle => $valeur) {
    $param = explode("_", $cle);
    mysqli_stmt_bind_param($stmt,"ii",$param[0],$param[1]);
    mysqli_stmt_execute($stmt);
    $tuple = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if($tuple === NULL)
      die("erreur panier");
    if($tuple["quantity"] < $_SESSION["panier"][$cle]){
      $_SESSION["panier"][$cle] = $tuple["quantity"];
      $err = "quantity";
    }
    $total_panier += $tuple["price"]*$_SESSION["panier"][$cle];
  }

  if(isset($err)){
    header("Location:../panier.php?err=".$err);
    exit();
  }

  $query = mysqli_query($db,"SELECT stash FROM Customer WHERE id = ".$_SESSION["id"]);
  $tuple = mysqli_fetch_assoc($query);
  if($tuple["stash"] < $total_panier){
    $err = "stash";
  }

  if(isset($err)){
    header("Location:../panier.php?err=".$err);
    exit();
  }
  //Mise à jour de la quantité dans la table BusinessSell
  $stmt = mysqli_prepare($db, "UPDATE BusinessSell
                              SET quantity = quantity - ?
                              WHERE Typeitem = ? AND Business =  ?");
  //Historique commande
  $stmt_order = mysqli_prepare($db, "INSERT INTO Orders(customer_id, price, order_date, buyOrSell) VALUES (?,?,now(),0)");
  mysqli_stmt_bind_param($stmt_order,"ii",$_SESSION["id"],$total_panier);
  mysqli_stmt_execute($stmt_order);
  $order_id = mysqli_insert_id($db);

  $stmt_order_details = mysqli_prepare($db, "INSERT INTO OrdersDetails(order_id, product_id, business_id, quantity,price) VALUES ($order_id,?,?,?,?)");

  foreach($_SESSION["panier"] as $cle => $quantite){
    $param = explode("_", $cle);
    mysqli_stmt_bind_param($stmt,"iii",$quantite,$param[0],$param[1]);
     //Sélection du prix (Oui cela est redondant car déjà fait prédédemment mais je n'ai pas d'idée pour ne éviter ce problème)
    $stmt_price = mysqli_query($db, "SELECT price
                                    FROM  BusinessSell 
                                    WHERE Typeitem = $param[0] AND Business =  $param[1]");
    $price = mysqli_fetch_assoc($stmt_price)["price"];
    mysqli_stmt_bind_param($stmt_order_details,"iiii",$param[0],$param[1],$quantite,$price);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_execute($stmt_order_details);
  }
  

  $query = mysqli_query($db,"UPDATE Customer SET stash = stash - ".$total_panier." WHERE id = ".$_SESSION["id"]);
  unset($_SESSION["panier"]);

  header("Location:confirmation_achat.php?m=t");
}


mysqli_close($db);

?>
