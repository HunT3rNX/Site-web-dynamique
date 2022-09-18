<div class="w3-card w3-white w3-round info">
  <div class="w3-container">
      <h1 class="w3-center">Changer votre photo de profil</h1>
      <hr>
  </div>
  <div class="w3-container w3-center">
      <?php if(isset($error_picture)) : ?>
          <div class="w3-panel w3-red w3-margin">
              <p><?= $error_picture ?></p>
          </div>
      <?php endif ?>
      <form enctype="multipart/form-data" method="POST">
          <p><input type="file" accept=".png, .jpg, .jpeg" name="picture" id="picture" required></p>
          <input class="w3-button w3-round-large w3-2017-navy-peony" name="envoyer" type="submit" value="Envoyer">
      </form>
      <form method="POST">
          <input class="w3-button w3-round-large w3-red" name="supprimer" type="submit" value="Supprimer PdP">
      </form>
  </div>
</div>