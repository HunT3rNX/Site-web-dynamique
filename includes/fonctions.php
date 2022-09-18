<?php 
  function checkPassword($pwd) {
    $errors = array();

    if (strlen($pwd) < 8) {
        $errors[] = "Mot de passe trop court (< 8) !";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "Le mot de passe doit inclure au moins un chiffre !";
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors[] = "Le mot de passe doit inclure au moins une lettre !";
    }     

    return ($errors);
  }
  
  //Fonctions trouvée sur internet que j'ai modifié pour qu'elle fonctionne avec ma configuration

  // Génère aléatoirement une clé et un sélecteur
  function generate_tokens(): array {
      $selector = bin2hex(random_bytes(16));
      $validator = bin2hex(random_bytes(32));
      return [$selector, $validator, $selector . ':' . $validator];
  }

  //Sépare la clé et le sélecteur
  function parse_token(string $token): ?array {
    $parts = explode(':', $token);  
    if ($parts && count($parts) == 2) {
        return [$parts[0], $parts[1]];
    }
    return null;
  }

  //Insère un nouveau tuple pour l'identifiant donné
  function insert_user_token(mysqli $db, int $id, string $selector, string $hashed_validator, string $expiry_time): bool {
      $sql = 'INSERT INTO Customers_tokens(customer_id, selector, hashed_validator, expiry) VALUES(?, ?, ?, ?)';
      $stmt = mysqli_prepare($db,$sql);
      mysqli_stmt_bind_param($stmt,"isss", $id,$selector,$hashed_validator,$expiry_time);

      return mysqli_stmt_execute($stmt);
  }

  //Cherche un tuple pour le sélecteur donné
  function find_user_token_by_selector(mysqli $db, string $selector){

    $sql = 'SELECT selector, hashed_validator, customer_id
            FROM Customers_tokens
            WHERE selector = ? AND expiry >= now()
            LIMIT 1';

    $stmt = mysqli_prepare($db,$sql);   
    mysqli_stmt_bind_param($stmt,"s",$selector);
    mysqli_stmt_execute($stmt);
    //Récupération de la table
    $table = mysqli_stmt_get_result($stmt);
    return $tuple = mysqli_fetch_assoc($table);;
  }

//Supprime tous les tuplesde la table Customers_tokens pour un utilisateur
function delete_user_token(mysqli $db, int $id): bool {
    $sql = 'DELETE FROM Customers_tokens WHERE customer_id = ?';
    $stmt = mysqli_prepare($db,$sql); 
    mysqli_stmt_bind_param($stmt,"i",$id);
    return mysqli_stmt_execute($stmt);
}

//Vérifie si le token est valide
function token_is_valid(mysqli $db, string $token): ?int { // parse the token to get the selector and validator [$selector, $validator] = parse_token($token);
  [$selector, $validator] = parse_token($token);
  $tokens = find_user_token_by_selector($db, $selector);
  if (!$tokens) {
      return null;
  }

  if(password_verify($validator, $tokens['hashed_validator'])){
    return $tokens['customer_id'];
  }

  return null;
}

//Crée un cookie et insère un nouveau tuple dans la base si l'utilisateur souhaite être reconnecter automatiquement
function remember_me(mysqli $db, int $id, int $day = 30) {
    [$selector, $validator, $token] = generate_tokens();

    // remove all existing token associated with the user id
    delete_user_token($db, $id);

    // set expiration date
    $expired_seconds = time() + 60 * 60 * 24 * $day;

    // insert a token to the database
    $hash_validator = password_hash($validator, PASSWORD_DEFAULT);
    $expiry = date('Y-m-d H:i:s', $expired_seconds);

    if (insert_user_token($db, $id, $selector, $hash_validator, $expiry)) {
        setcookie('remember_me', $token, $expired_seconds, '/~justiney');
    }
}

?>