<?php
if(!isset($_SESSION["id"])){
    exit("Pas de session ouverte");
}
if(isset($_FILES["picture"]) && isset($_POST["envoyer"])) {
    $upload_file = $_FILES["picture"]["tmp_name"];
    $extension = pathinfo($_FILES["picture"]["name"], PATHINFO_EXTENSION);
    $file_name = $_SESSION["id"].".".$extension;
    //Verification de la taille de l'image
    if($_FILES["picture"]["size"] > 1049000) {
        $error_picture = "Fichier trop grand (Max 1Mio)";
    }
          
    if(!isset($error_picture)){
        $stmt = mysqli_prepare($db, "SELECT picture_path FROM CustomerProtectedData WHERE id = ? ");
        //Liaison des paramètres
        mysqli_stmt_bind_param($stmt,"i",$_SESSION["id"]);
        mysqli_stmt_execute($stmt);
        //Récupération des résultats
        $table = mysqli_stmt_get_result($stmt);
        $tuple = mysqli_fetch_assoc($table);
        if($tuple["picture_path"] !== null)
            unlink("img/".$tuple["picture_path"]);
        move_uploaded_file($upload_file,"img/".$file_name);
        $stmt = mysqli_prepare($db, "UPDATE CustomerProtectedData SET picture_path = ? WHERE id = ? ");
        //Liaison des paramètres
        mysqli_stmt_bind_param($stmt,"si",$file_name, $_SESSION["id"]);
        //Execution
        mysqli_stmt_execute($stmt);
        $_FILES = array();
    }
}

if(isset($_POST["supprimer"])){
    $stmt = mysqli_prepare($db, "SELECT picture_path FROM CustomerProtectedData WHERE id = ? ");
        //Liaison des paramètres
        mysqli_stmt_bind_param($stmt,"i",$_SESSION["id"]);
        mysqli_stmt_execute($stmt);
        //Récupération des résultats
        $table = mysqli_stmt_get_result($stmt);
        $tuple = mysqli_fetch_assoc($table);
        if($tuple["picture_path"] !== null){
            unlink("img/".$tuple["picture_path"]);
            $stmt = mysqli_prepare($db, "UPDATE CustomerProtectedData SET picture_path = NULL WHERE id = ? ");
            //Liaison des paramètres
            mysqli_stmt_bind_param($stmt,"i", $_SESSION["id"]);
            //Execution
            mysqli_stmt_execute($stmt);
            $_FILES = array();
        }
}

?>