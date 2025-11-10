<?php

    //Hace que inicie la sesion si no existe
    if (session_status() == PHP_SESSION_NONE) {
            session_start();//Inicia la sesión
    }

    //Conteo de productos en el carrito
    $num_cart = 0;
    #Verifica si existe la sesion
    if(isset($_SESSION['carrito']['productos'])){
        #Se cuenta cuantos productos hay en el carrito de una determinada sesion
        $num_cart = count($_SESSION['carrito']['productos']);
    }
?>
