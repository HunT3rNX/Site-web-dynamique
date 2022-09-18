<?php
//Requete pour les commandes
$result = mysqli_query($db, "SELECT order_id, price, buyOrSell, DATE_FORMAT(order_date,'%d/%m/%Y %H:%i:%s') as order_date
                            FROM Orders WHERE Customer_id = ".$_SESSION["id"]."
                            ORDER BY order_date");
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

if(isset($_GET["order"])){
    //Requete pour les commandes
    $result = mysqli_prepare($db, "SELECT OrdersDetails.*, name, buyOrSell, order_date
                            FROM OrdersDetails JOIN Orders ON (OrdersDetails.order_id = Orders.order_id)
                            JOIN TypeItem ON (OrdersDetails.product_id = TypeItem.id) WHERE OrdersDetails.order_id = ? AND Customer_id = ".$_SESSION["id"]);
    mysqli_stmt_bind_param($result,"i",$_GET["order"]);
    mysqli_stmt_execute($result);
    $order_details = mysqli_fetch_all(mysqli_stmt_get_result($result), MYSQLI_ASSOC);
}
?>

<div class="w3-card w3-white w3-round info">
  <div class="w3-container">
      <h1 class="w3-center">Votre historique de commandes</h1>
      <hr>
  </div>
  <div class="w3-container w3-padding">
      <?php if(!isset($order_details) && !empty($orders)) : ?>
      <div class="w3-responsive">
          <table class="w3-table-all w3-centered">
              <thead>
                  <tr>
                      <th>N° de la commande</th>
                      <th>Prix</th>
                      <th colspan="2">Date</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach($orders as $order) : ?>
                  <tr>
                      <td><?=dechex($order["order_id"])?></td>
                      <?php 
                          if($order["buyOrSell"]) 
                              echo "<td class='w3-text-green'>+".$order["price"]."€"; 
                          else
                              echo "<td class='w3-text-red'>-".$order["price"]."€"; 
                      ?>
                      </td>
                      <td><?=$order["order_date"]?></td>
                      <td><a href="?page=history&order=<?=$order["order_id"]?>"><i class="fa fa-eye"></i></a></td>
                  </tr>
                  <?php endforeach ?>
              </tbody>
          </table>
      </div>
      <?php elseif(isset($order_details)) : ?>
      
          <?php if(empty($order_details)) : ?>
              <h2>Cette commande n'existe pas</h2>
          <?php else : ?>
          <h4 class="w3-center"><?php if($order_details[0]["buyOrSell"]) echo "Vente"; else echo "Achat";?> N° <?=dechex($order_details[0]["order_id"])?></h4>
          <div class="w3-responsive">
              <table class="w3-table-all w3-centered">
                  <thead>
                      <tr>
                          <th>Produit</th>
                          <th>Prix Unitaire</th>
                          <th>Quantité</th>
                          <th>Prix Total</th>
                      </tr>
                  </thead>
                  <!-- Vente -->
                  <?php if($order_details[0]["buyOrSell"]) : //Il n'y as qu'un seul produit lors d'une vente?>
                  <tbody>
                      <tr>
                          <td><a href="vente.php?page=details&product=<?=$order_details[0]["product_id"]?>&b=<?=$order_details[0]["business_id"]?>"><?=$order_details[0]["name"]?></a></td>
                          <td><?=$order_details[0]["price"]?>€</td>
                          <td><?=$order_details[0]["quantity"]?></td>
                          <td><?=$order_details[0]["price"]*$order_details[0]["quantity"]?>€</td>
                      </tr>
                  </tbody>
              </table>
                  </br>
                  <h4>Eléments extraits par cette vente</h4>
                  <?php
                      //Requete pour les éléments extraits (PHP dans HTML je sais mais je n'ai pas le temps)
                      $result = mysqli_query($db, "SELECT name AS Element, quantity AS Quantité 
                      FROM Mendeleiev LEFT OUTER JOIN (SELECT * FROM ExtractionFromTypeItem 
                      WHERE typeItem = ".$order_details[0]["product_id"]." ) C ON (Mendeleiev.Z = C.element) 
                      ORDER BY Z;");
                      $tableauElement = mysqli_fetch_all($result, MYSQLI_ASSOC);
                  ?>
                  <div class="w3-responsive">
                      <table class="w3-table-all">
                          <!-- Titre des colonnes -->
                          <tr> 
                              <th>Element</th>
                              <th>Quantité (en mg)</th>
                          </tr>
                          <!-- Lignes -->
                          
                          <?php 
                              $max_element = max(array_column($tableauElement, "Quantité")); 
                              foreach($tableauElement as  $ligne) : ?>
                              <tr>
                                  <td><?= ucfirst($ligne["Element"]) ?></td>
                                  <td class="<?php if($ligne["Quantité"] == $max_element) echo 'w3-green'?>"><?php 
                                          if($ligne["Quantité"] === NULL) echo 0; 
                                          else echo $order_details[0]["quantity"]*$ligne["Quantité"]; ?>
                                  </td>
                              </tr>
                          <?php endforeach ?>
                      </table>
                  </div>
                  <!-- Achat -->
                  <?php else : ?>
                  <tbody>
                      <?php foreach($order_details as $order) : ?>
                      <tr>
                          <td><a href="achat.php?page=details&product=<?=$order["product_id"]?>&b=<?=$order["business_id"]?>"><?=$order["name"]?></a></td>
                          <td><?=$order["price"]?>€</td>
                          <td><?=$order["quantity"]?></td>
                          <td><?=$order["price"]*$order["quantity"]?>€</td>
                      </tr>
                      <?php endforeach ?>
                  </tbody>
              </table>
              <?php endif;?>
          </div>
          <?php if(strtotime($order_details[0]["order_date"]) + 14 * 86400 > time() && !$order_details[0]["buyOrSell"]) : ?>
              <button class="w3-button w3-theme-d2" disabled>Se faire rembourser (ne fonctionne pas encore)</button>
          <?php endif ?>
          <?php endif; ?>
      <?php else : ?>
          <h3 class="w3-center">Vous n'avez pas encore effectué d'achat</h3>
      <?php endif?>
  </div>
</div>