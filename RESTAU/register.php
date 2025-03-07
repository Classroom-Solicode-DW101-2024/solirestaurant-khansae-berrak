<?php
require 'config.php';
$erreurs=[];
if(isset($_POST["btnSubmit"] )){
    $nom=trim($_POST["nom"]);
    $prenom=trim($_POST["prenom"]);
    $tel=trim($_POST["tel"]);
    $tel_is_exist=tel_existe($tel);
    var_dump( $tel_is_exist);
    if(!empty($nom) && !empty($prenom) && !empty($tel) && empty($tel_is_exist)){
     $sql_insert_client="insert into CLIENT  values(:id,:nom,:prenom,:tel)";
     $stmt_insert_client=$pdo->prepare($sql_insert_client);
    $idvalue=getLastIdClient()+1;

     $stmt_insert_client->bindParam(':id',$idvalue);
     $stmt_insert_client->bindParam(':nom',$nom);
     $stmt_insert_client->bindParam(':prenom',$prenom);
     $stmt_insert_client->bindParam(':tel',$tel);

     $stmt_insert_client->execute();
    echo 'Client bien ajouté !!';

    }else {
        if(empty($nom)){
            $erreurs['nom']="remplir le nom";
        }
        if(empty($prenom)){
            $erreurs['prenom']="remplir le prenom";
        }
        if(empty($tel)){
            $erreurs['tel']="remplir le tel";
        }
        if(!empty($tel_is_exist)){
            $erreurs['tel']="tel is duplique";
        }
    }
    
}
 
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form  method="POST" >
        <label for="nom">Entrez votre nom:</label>
        <input type="text" name="nom" id="nom">
        <label for="prenom">Entrez votre prénom</label>
        <input type="text" name="prenom" id="prenom">
        <label for="numTel">Entrez votre numéro de téléphone</label>
        <input type="tel" name="tel" id="numTel" >
        <button name="btnSubmit">Je m'inscris!</button>
    </form>
    <a href="login.php">log in</a>
    <?php
    if(count($erreurs)>0){
        foreach($erreurs as $key=>$erreur){
            echo "<span class='erreur'>".$erreur."</span><br>";
        }
    }
    ?>
</body>
</html>