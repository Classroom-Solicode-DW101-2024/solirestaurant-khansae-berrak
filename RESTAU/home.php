<?php 
require "config.php";
if(isset($_SESSION["client"])){
    echo "le nom de client ".$_SESSION["client"]["nomCl"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div></div>
</body>
</html>