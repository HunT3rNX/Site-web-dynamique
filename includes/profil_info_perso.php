<div class="w3-card w3-white w3-round info">
  <div class="w3-container">
      <h1 class="w3-center">Informations Personnelles</h1>
      <hr>
  </div>
  <div class="w3-row">
    <?php if(!empty($errors_password)) :  ?>
        <div class="w3-panel w3-2017-grenadine w3-margin">
            <h3 >Erreur</h3>
            <?php foreach($errors_password as $message) : ?>
                <p><?=$message ?></p>
            <?php endforeach?>
        </div>
    <?php endif;?>
    <?php if(isset($success)) :  ?>
        <div class="w3-panel w3-green w3-margin">
            <h3 >Modification réussie</h3>
            <p><?=$success ?></p>
        </div>
    <?php endif;?>
    <div class="w3-col m5 w3-container w3-padding-16">
        <form class="w3-large" >
            <p>
                <label>Votre login : </label>
                <input class="w3-input w3-border w3-round" type="text" value="<?=$Login ?>" disabled>
            </p>
            <p>
                <label>Votre nom : </label>
                <input class="w3-input w3-border w3-round" type="text" value="<?=$Nom ?>" disabled>
            </p>
            <p>
                <label>Votre prénom : </label>
                <input class="w3-input w3-border w3-round" type="text" value="<?=$Prenom ?>" disabled>
            </p>
            <p>
                <label>Votre email : </label>
                <input class="w3-input w3-border w3-round" type="text" value="<?=$email ?>" disabled>
            </p>
        </form>
        <br>
        <button class="w3-button w3-2017-navy-peony w3-round-large w3-center" onclick="document.getElementById('mdp').style.display='block'">Changer votre mot de passe</button>
        <div id="mdp" class="w3-modal">
          <div class="w3-modal-content w3-animate-zoom w3-container">
              <form class="w3-large" action="?page=info" method="POST">
                  <div class="w3-content w3-section" style="max-width: 400px;" >
                      <p>
                          <input class="w3-input w3-border w3-round" name="password" type="password" placeholder="Mot de passe actuel" required="required">
                      </p>
                      <p>
                          <input class="w3-input w3-border w3-round" name="new_password" type="password" placeholder="Nouveau mot de passe" required="required">
                      </p>
                      <p>
                          <input class="w3-input w3-border w3-round" name="cnew_password" type="password" placeholder="Confirmer le nouveau mot de passe" required="required">
                      </p>
                  </div>
                  <div class="w3-container">
                      <button class="w3-button w3-red w3-round-large w3-ripple w3-left" onclick="document.getElementById('mdp').style.display='none'"> Annuler</button>
                      <input class="w3-button w3-2017-navy-peony w3-hover-light-blue w3-round-large w3-ripple w3-right" type="submit" value="Changer mon mot de passe">
                  </div>
              </form>
          </div>
        </div>
    </div>
  </div>
</div>