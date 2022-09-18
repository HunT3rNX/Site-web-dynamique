<?php
    include_once("bd.php");
    include_once("fonctions.php");
    session_start();
    //Suppression du token dans la base et des variables de session (client)
    if(isset($_SESSION['id'])) {
        delete_user_token($db,$_SESSION['id']);
        unset($_SESSION);
    }
    // Suppression du cookie
    if (isset($_COOKIE['remember_me'])) { 
        unset($_COOKIE['remember_me']);   
        setcookie('remember_me',"",1, '/~justiney');
    }
    //Destruction de la session (serveur)
    session_destroy();
    //Redirection
    header("Location:../index.php");
    mysqli_close($db);
?>