<?php
function conectarDB():mysqli{
    $db=mysqli_connect('localhost','root','1234','bienesraices');
    if(!$db){
       echo "Eror, no se pudo conectar";
        exit;   
    }
    return $db;
}
    ?>