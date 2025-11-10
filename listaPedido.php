<?php

session_start();

require 'Config/config.php';
require 'database.php';
$db = new Database();
$con = $db->conectar();
//Si es que aún no has cerrado sesion te va enviar la página correspondiente, aunque cierres la página
// Para usar estas lineas de código se debe crear un apartado de cerrar sesión.

if (isset($_SESSION['usuario'])) {
    // Si ya hay una sesión iniciada, redirige al usuario según su tipo
    if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'admin') {
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

$pedidos_completos = [];

if (isset($_SESSION['usuario'])) {

    $sql = $con->prepare("SELECT * FROM tb_pedido WHERE Id_Cliente = :id");
    $sql->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $sql->execute();
    $pedidos = $sql->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pedidos as $pedido) {
        $pedido_id = $pedido['Id_Pedido'];
        $fecha = $pedido['Fecha_Pedido'];
        $estado_num = $pedido['Estado_Pedido'];
        $costo_envio = $pedido['Costo_Envio'];
        $total_pedido = $pedido['Total_Pedido'];
        $estado_texto = '';

        switch ($estado_num) {
            case 0: $estado_texto = 'Pendiente'; break;
            case 1: $estado_texto = 'En proceso'; break;
            case 2: $estado_texto = 'Entregado'; break;
            case 3: $estado_texto = 'Cancelado'; break;
        }

        // Obtener productos de ese pedido
        $sql_productos = $con->prepare("
            SELECT dp.Cantidad_Pedido, dp.Precio_Venta, dp.Descuento, pr.Nombre_Producto, pr.Imagen
            FROM tb_detalle_pedido dp 
            INNER JOIN tb_producto pr ON dp.Id_Producto = pr.Id_Producto 
            WHERE dp.Id_Pedido = :pedido_id
        ");
        $sql_productos->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $sql_productos->execute();
        $productos = $sql_productos->fetchAll(PDO::FETCH_ASSOC);

        // Calcular subtotal
        $total = 0;
        foreach ($productos as $producto) {
            $producto['Subtotal'] = $producto['Cantidad_Pedido'] * $producto['Precio_Venta'];
            $total += $producto['Subtotal'];
        }

        // Agregar al arreglo final
        $pedidos_completos[] = [
            'id' => $pedido_id,
            'fecha' => $fecha,
            'estado' => $estado_texto,
            'estado_num' => $estado_num,
            'total' => $total_pedido,
            'productos' => $productos,
            'costo_envio' => $costo_envio
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanta</title>
    
    <!-- Asegúrate de incluir Font Awesome en tu proyecto -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!--Para el manejo de los scripts que envian datos-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><!--POPUPS de validación-->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>
<body class="blurred bg-gradient-to-bl from-white to-slate-50">
    <?php 
    // Verifica si el usuario está logueado (por ejemplo, si existe una variable de sesión "usuario")
    if (isset($_SESSION['usuario'])) {
        include 'components/Navbar_cliente.php'; // Navbar para usuarios logueados
    } else {
        include 'components/Navbar.php'; // Navbar para usuarios no logueados
    }
    
    include 'Cliente/Compras.php';
    include 'components/Footer.php';
    ?>


<script src="assets/api/products.json"></script>
<script src="assets/js/category.js"></script>
<!--<script src="assets/js/pagination.js"></script>-->
<!--<script src="assets/cart.js" defer></script>-->
</body>
</html>