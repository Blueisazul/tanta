<?php

require 'Config/config.php';
require 'database.php';
$db = new Database();
$con = $db->conectar();
// Realizar consulta para obtener datos de productos activos junto con la descripción de su categoría
    $sql = $con->prepare("
        SELECT 
            p.Id_Producto,
            p.Nombre_Producto,
            p.Descripcion_Producto,
            p.Stock,
            p.Precio_Actual,
            p.Imagen,
            p.Disponibilidad,
            p.Estado_Producto,
            c.Id_Categoria,
            c.Descripcion_Categoria
        FROM tb_producto p
        INNER JOIN tb_categoria c ON p.Id_Categoria = c.Id_Categoria
        WHERE p.Estado_Producto = ?
    ");

    $activo = 1;
    $sql->bindParam(1, $activo, PDO::PARAM_INT);
    $sql->execute();
    $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

    // Convertir los resultados en un array
    $productos = [];

    foreach ($resultado as $fila) {
        $productos[] = $fila;
    }

    // Convertir el array de productos a formato JSON
    header('Content-Type: application/json');
    echo json_encode($productos);

    ?>
    