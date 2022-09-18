<?php
    session_set_cookie_params(['lifetime' => 3600, 'path' => "/~justiney"]);
    session_start();
    if(isset($_SESSION["id"])){
        header("Location:./index.php");
        exit();
    }
    include_once ("bd.php");
    include_once("fonctions.php");

    if(isset($_COOKIE["remember_me"])){
        $id = token_is_valid($db, htmlspecialchars($_COOKIE["remember_me"]));
        if($id !== null){
            $_SESSION['id'] = $id;
            header("Location:./index.php"); 
            exit();
        }
    }

    if(isset($_POST["login"]) && isset($_POST["password"])) {
        $login = htmlspecialchars($_POST["login"]);
        $password = $_POST["password"];
        //Requête préparée
        $stmt = mysqli_prepare($db, "SELECT id, login, password FROM Customer WHERE login = ?");
        //Liaison des paramètres
        mysqli_stmt_bind_param($stmt,"s", $login);
        //Execution
        mysqli_stmt_execute($stmt);
        //Recuperation des tuples;
        $table = mysqli_stmt_get_result($stmt);
        $tuple = mysqli_fetch_assoc($table);
        //Exploitation des résultats
        if($tuple !== null){
            if(password_verify($password, $tuple['password'])){
                $_SESSION['id'] = $tuple['id'];
                if(isset($_POST['remember_me'])) {
                    remember_me($db, $tuple['id']);
                }
                header("Location:./index.php");
            } else $error = "Mot de passe incorrect";
        } else $error = "Ce login n'existe pas";
    }
    mysqli_close($db);
?>