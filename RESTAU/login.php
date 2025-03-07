<?php 

require "config.php";
if(isset($_POST["submit"])){
    $tel = $_POST["tel"];
    $rusult=tel_existe($tel);
    if(empty($rusult)){
        header("Location:register.php");
    }else{
        $_SESSION["client"]=$rusult;
        header("Location:index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<style>
      
</style>
</head>
<body>
    <form method="POST">
        <label for="tel">entre tel:</label>
        <input type="tel" id="tel" name="tel" required>
        <button name="submit">log in</button>
    </form>
</body>
</html>