<?php
    session_start();
    if(isset($_SESSION["id"])){
        header("Location:./profil.php");
        exit();
    }
    include_once ("bd.php");
    include_once ("fonctions.php");
    if(isset($_POST["login"]) && isset($_POST["name"]) && isset($_POST["firstname"]) && isset($_POST["password"]) && isset($_POST["rpassword"])) {
        $login = htmlspecialchars(trim($_POST["login"]));
        $name = trim($_POST["name"]);
        $firstname = trim($_POST["firstname"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $rpassword = trim($_POST["rpassword"]);   
        $errors = array(); 

        //Requête préparée
        $stmt = mysqli_prepare($db, "SELECT id FROM Customer WHERE login = ?");
        //Liaison des paramètres
        mysqli_stmt_bind_param($stmt,"s", $login);
        //Execution
        mysqli_stmt_execute($stmt);
        //Mise en tampon des résultats
        mysqli_stmt_store_result($stmt);
        //Login déjà existant ?
        if(mysqli_stmt_num_rows($stmt) !== 0)
            $errors[] = "Ce login existe déjà";

        //Requête préparée
        $stmt = mysqli_prepare($db, "SELECT id FROM CustomerProtectedData WHERE email = ?");
        //Liaison des paramètres
        mysqli_stmt_bind_param($stmt,"s", $email);
        //Execution
        mysqli_stmt_execute($stmt);
        //Mise en tampon des résultats
        mysqli_stmt_store_result($stmt);
        //Login déjà existant ?
        if(mysqli_stmt_num_rows($stmt) !== 0)
            $errors[] = "Ce adresse mail est déjà utilisée";
            
        //Login <= 100 ? 
        if(strlen($login) > 100 || !preg_match("/^[a-zA-Z0-9 - _]+$/",$login))
            $errors[] = "Ce login trop long ou contient des caractères spéciaux";
        //Nom <= 100 ? 
        if(strlen($name) > 100 || !preg_match("/^[a-zA-Z -]+$/",$name))
            $errors[] = "Le nom ne doit contenir que des lettres et être inférieur à 100 caractères";
        //Prénom <= 100 ? 
        if(strlen($firstname) > 100 || !preg_match("/^[a-zA-Z -]+$/",$firstname))
            $errors[] = "Le prénom ne doit contenir que des lettres et être inférieur à 100 caractères";
        //Mot de passe fort ?
        $errors_pwd = checkPassword($password);
        if(!empty($errors_pwd))
            $errors = array_merge($errors,$errors_pwd);
        //Mots de passe identique ? 
        if($password !== $rpassword)
            $errors[] = "Les mots de passes ne sont pas identiques";

        if(empty($errors)){
            //Préparation insertion dans Customers
            $stmt = mysqli_prepare($db, "INSERT INTO Customer(login, password, stash) VALUES (?,?,0)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt,"ss",$login,$hashed_password);
            mysqli_stmt_execute($stmt);
            //Si insertion réussi
            if(mysqli_stmt_affected_rows($stmt) === 1){
                //Requête préparée pour récupérer l'id
                $stmt = mysqli_prepare($db, "SELECT id FROM Customer WHERE login = ?");
                //Liaison des paramètres
                mysqli_stmt_bind_param($stmt,"s", $login);
                //Execution
                mysqli_stmt_execute($stmt);
                //Récupération de l'id
                $table = mysqli_stmt_get_result($stmt);
                $tuple = mysqli_fetch_assoc($table);
                $id = $tuple['id'];
                
                //Préparation insertion dans CustomersProtectedData
                $stmt = mysqli_prepare($db, "INSERT INTO CustomerProtectedData(id, surname, firstname, email) VALUES (?,?,?,?)");
                mysqli_stmt_bind_param($stmt,"isss",$id,$name,$firstname,$email);
                mysqli_stmt_execute($stmt);
                $_SESSION['id'] = $id;
                header("Location:./profil.php");
            }
            else
                $errors[] = "Erreur interne";
        }
            
        mysqli_close($db);
    
    }
?>