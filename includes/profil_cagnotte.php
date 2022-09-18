<?php
//Requete pour les éléments  extraits
$result = mysqli_query($db, "SELECT name AS Element, quantity AS Quantité 
                            FROM Mendeleiev LEFT OUTER JOIN (SELECT * FROM CustomerExtraction WHERE Customer = ".$_SESSION["id"]." ) C ON (Mendeleiev.Z = C.element) 
                            ORDER BY Z;");
$tableauElement = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="w3-card w3-white w3-round w3-padding info" id="Cagnotte">
  <div class="w3-container w3-center">
      <h2>Votre cagnotte</h2>
      <br>
      <div class="w3-light-grey w3-round">
          <div class="w3-container w3-round w3-black w3-text-red" style="width:<?= $Pourcentage ?>%"><?= $Pourcentage ?>%</div>
      </div>
      <p><?=$Cagnotte ?> €</p>
  </div>
  <br><hr>
  <!-- Elements extraits -->
  <div class="w3-container w3-padding">
    <h2 class="w3-center">Vos éléments extraits</h2>
    <br>
    <div class="w3-responsive">
        <table class="w3-table-all">
            <!-- Titre des colonnes -->
            <thead>
                <tr>
                    <th>Element</th>
                    <th>Quantité (en mg)</th>
                </tr>
            </thead>
            <tbody>
            <!-- Lignes -->
            <?php foreach($tableauElement as  $ligne) : ?>
                <tr>
                    <td><?= ucfirst($ligne["Element"]) ?></td>
                    <td><?php 
                            if($ligne["Quantité"] === NULL) echo 0; 
                            else echo $ligne["Quantité"]; ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        </table>
    </div>
  </div>
</div>