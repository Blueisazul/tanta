<?php
 session_start();

 require 'Config/config.php';
 require 'database.php';
 $db = new Database();
 $con = $db->conectar();

if (isset($_SESSION['usuario'])) {

    if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Administrador'){
        // Si ya hay una sesión iniciada, redirige al usuario según su tipo
        header("Location: Admin/admin-page.php");
        exit(); 
    }
}
    
if(isset($_SESSION['usuario'])){

    
    // Consulta SQL para seleccionar los datos del usuario utilizando su ID al LOGUEARSE
    $sql = $con->prepare("SELECT Nombre, Apellido, DNI, Telefono FROM tb_persona WHERE Id_Persona = :id");
    $sql->bindParam(':id', $_SESSION['id'] , PDO::PARAM_INT);
    $sql->setFetchMode(PDO::FETCH_ASSOC); // Establecer el modo de recuperación de datos
    $sql->execute();
    $datos_persona = $sql->fetch(PDO::FETCH_ASSOC);
    
    // Asignar los datos del usuario a variables individuales
    $nombre = $datos_persona['Nombre'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><!--POPUPS de validación-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/whatsappFlotante.css">
</head>
<body>
    <?php 

    // Verifica si el usuario está logueado (por ejemplo, si existe una variable de sesión "usuario")
    if (isset($_SESSION['usuario'])) {
        include 'components/Navbar_cliente.php'; // Navbar para usuarios logueados
    } else {
        include 'components/Navbar.php'; // Navbar para usuarios no logueados
    }
    include 'components/Productos.php';
    
    include 'components/Carrito-flot.php';
    include 'components/Footer.php';
    include 'components/icono_flotante_whatsapp.php';
    ?>
    <!--<script src="assets/js/cerrar_sesion.js"></script>-->
    <script src="assets/api/products.json"></script>
    <script src="assets/js/category.js"></script>
    <script src="assets/js/pagination.js"></script>
    <script src="assets/cart.js" defer></script>
    <script>
    (function(d){
      var s = d.createElement("script");
      /* uncomment the following line to override default position*/
      s.setAttribute("data-position", 5);
      /* uncomment the following line to override default size (values: small, large)*/
      /* s.setAttribute("data-size", "small");*/
      /* uncomment the following line to override default language (e.g., fr, de, es, he, nl, etc.)*/
      s.setAttribute("data-language", "es");
      /* uncomment the following line to override color set via widget (e.g., #053f67)*/
      /* s.setAttribute("data-color", "#053e67");*/
      /* uncomment the following line to override type set via widget (1=person, 2=chair, 3=eye, 4=text)*/
      /* s.setAttribute("data-type", "1");*/
      /* s.setAttribute("data-statement_text:", "Our Accessibility Statement");*/
      /* s.setAttribute("data-statement_url", "http://www.example.com/accessibility")";*/
      /* uncomment the following line to override support on mobile devices*/
      /* s.setAttribute("data-mobile", true);*/
      /* uncomment the following line to set custom trigger action for accessibility menu*/
      /* s.setAttribute("data-trigger", "triggerId")*/
      /* uncomment the following line to override widget's z-index property*/
      /* s.setAttribute("data-z-index", 10001);*/
      /* uncomment the following line to enable Live site translations (e.g., fr, de, es, he, nl, etc.)*/
      /* s.setAttribute("data-site-language", "null");*/
      s.setAttribute("data-widget_layout", "full")
      s.setAttribute("data-account", "bLLEVPcx9p");
      s.setAttribute("src", "https://cdn.userway.org/widget.js");
      (d.body || d.head).appendChild(s);
    })(document)
    </script>
    <noscript>Please ensure Javascript is enabled for purposes of <a href="https://userway.org">website accessibility</a></noscript>
</body>
</body>
</html>