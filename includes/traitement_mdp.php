<?php
    include_once("fonctions.php");
    //vérification de session déjà dans profil.php
    if(isset($_POST["password"]) && isset($_POST["new_password"]) && isset($_POST["cnew_password"])) {
        $errors_password = array();
        //Requete pour le mot de passe
        $stmt = mysqli_prepare($db, "SELECT password  
                                    FROM Customer 
                                    WHERE id = ?");
        //Liaison des paramètres
        mysqli_stmt_bind_param($stmt,"i", $_SESSION['id']);
        //Execution
        mysqli_stmt_execute($stmt);
        //Mise en tampon des résultats
        $table = mysqli_stmt_get_result($stmt);
        //Récupération des résultats
        $result = mysqli_fetch_assoc($table);

        if(!password_verify($_POST["password"], $result["password"]))
          $errors_password[] = "Mot de passe incorrect";

        $errors_pwd = checkPassword($_POST["new_password"]);
        if(!empty($errors_pwd))
          $errors_password = array_merge($errors_password, $errors_pwd);

        //Mots de passe identiques ? 
        if(strcmp($_POST["new_password"],$_POST["cnew_password"]) !== 0){
          $errors_password[] = "Les mots de passe ne sont pas identiques";
        }

        //Mot de passe identique au précédent? 
        if(password_verify($_POST["new_password"], $result["password"])){
          $errors_password[] = "Le nouveau mot de passe ne peut pas être identique au précédent";
        }

        if(empty($errors_password)){
          $hash = password_hash($_POST["new_password"],PASSWORD_DEFAULT);
          //Requête préparée
          $stmt = mysqli_prepare($db, "UPDATE Customer SET password = ? WHERE id = ? ");
          //Liaison des paramètres
          mysqli_stmt_bind_param($stmt,"si",$hash, $_SESSION["id"]);
          //Execution
          mysqli_stmt_execute($stmt);
          if(mysqli_affected_rows($db) > 0)
            $success = "Mot de passe modifié avec succès";
        }
    }
?>