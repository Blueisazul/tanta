<?php 
    session_start();
    //Cierra la sesión
    session_destroy();
    //Redirige a la página de inicio
    header("location: ../index.php");

?>