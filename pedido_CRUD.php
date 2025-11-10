<?php
session_start();

require 'Config/config.php';
require 'database.php';

$db = new Database();
$con = $db->conectar(); // objeto PDO

$data = json_decode(file_get_contents("php://input"), true);
header('Content-Type: application/json');
// Validar datos
if (!isset($data['carrito'], $data['distrito'], $data['direccion'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$carrito = $data['carrito'];
$distrito = $data['distrito'];
$direccion = $data['direccion'];
//$total = $data['precioTotal'];
$cliente_id = $_SESSION['id']; // Asegúrate que la sesión está activa y tiene este valor

try {
    $con->beginTransaction();

    // 1. Insertar pedido
    $sql_pedido = "INSERT INTO tb_pedido (Id_Cliente, Id_Distrito, Direccion_Entrega) 
                   VALUES (?, ?, ?)";
    $stmt_pedido = $con->prepare($sql_pedido);
    $stmt_pedido->execute([$cliente_id, $distrito, $direccion]);

    $pedido_id = $con->lastInsertId(); // Obtener el ID del pedido recién insertado

    $productos_actualizados = [];

    
    $total_pedido2 = 0.00;

    // 2. Insertar detalle por cada producto
    foreach ($carrito as $item) {
        // Se captura el id de los productos elegidos
        $producto_id = $item['id'];

        //Se obtiene el precio actual del producto/ unitario
        $sql = $con->prepare("SELECT Precio_Actual FROM tb_producto WHERE Id_Producto = :id");
        $sql->bindParam(':id', $producto_id , PDO::PARAM_INT);
        $sql->setFetchMode(PDO::FETCH_ASSOC); // Establecer el modo de recuperación de datos
        $sql->execute();
        $precio_unitario = $sql->fetch(PDO::FETCH_ASSOC);
        
        // Asignar los datos del usuario a variables individuales
        $precio_unitario= (float)$precio_unitario['Precio_Actual'];


        $cantidad = (int)$item['quantity'];
        //$precio_unitario = $item['precio']; // asegúrate que este campo esté en el JSON
        $subtotal = $precio_unitario * $cantidad;

        $total_pedido2 = $total_pedido2 + $subtotal;
        // Insertar en TB_DETALLE_PEDIDO
        $sql_detalle = "INSERT INTO tb_detalle_pedido (Id_Pedido, Id_Producto, Cantidad_Pedido, Precio_Venta, SubTotal)
                        VALUES (?, ?, ?, ?, ?)";
        $stmt_detalle = $con->prepare($sql_detalle);
        $stmt_detalle->execute([$pedido_id, $producto_id, $cantidad, $precio_unitario, $subtotal]);

        // Actualizar stock del producto
        $sql_stock = "UPDATE tb_producto SET Stock = Stock - ? WHERE Id_Producto = ?";
        $stmt_stock = $con->prepare($sql_stock);
        $stmt_stock->execute([$cantidad, $producto_id]);

        $productos_actualizados[] = $producto_id;
    }

    // Obtener el valor de Costo_Envio de la tabla tb_pedido
    $sql_costo_envio = "SELECT Costo_Envio FROM tb_pedido WHERE Id_Pedido = ?";
    $stmt_costo_envio = $con->prepare($sql_costo_envio);
    $stmt_costo_envio->execute([$pedido_id]);
    $costo_envio = $stmt_costo_envio->fetchColumn();

    //Costo total
    $total_pedido2 = $total_pedido2 + $costo_envio;

    // Actualizar el total de pedido
    $sql_total = "UPDATE tb_pedido SET Total_Pedido = ? WHERE Id_Pedido = ?";
    $stmt_total = $con->prepare($sql_total);
    $stmt_total->execute([$total_pedido2, $pedido_id]);

    //Obtener el Stock_Actual y el Stock_Min de la tabla tb_producto
    $sql_stock_actual = "SELECT Stock, Stock_Min FROM tb_producto WHERE Id_Producto = ?";
    $stmt_stock_actual = $con->prepare($sql_stock_actual);
    $stmt_stock_actual->execute([$producto_id]);
    $stock_data = $stmt_stock_actual->fetch(PDO::FETCH_ASSOC);
    $stock_actual = (int)$stock_data['Stock'];
    $stock_min = (int)$stock_data['Stock_Min'];

    $disponibilidad = 0;

    if($stock_actual == 0){
    $disponibilidad = 0;//Agotado
    }else if($stock_actual <= $stock_min){
        $disponibilidad = 2;//Escaso
    }else{
        $disponibilidad = 1;//Disponible
    }

    //Actualizar la disponibilidad de los productos

    $sql_disponibilidad = "UPDATE tb_producto SET Disponibilidad = ? WHERE Stock <= Stock_Min";
    $stmt_disponibilidad = $con->prepare($sql_disponibilidad);
    $stmt_disponibilidad->execute([$disponibilidad]);

    // Supón que tienes $_SESSION['id']
    $sql = $con->prepare("SELECT p.Nombre, p.Apellido, u.Correo, p.Telefono 
                        FROM tb_persona p 
                        INNER JOIN tb_usuario u ON p.Id_Persona = u.Id_Usuario 
                        WHERE p.Id_Persona = ?");
    $sql->execute([$_SESSION['id']]);
    $cliente = $sql->fetch(PDO::FETCH_ASSOC);

    $con->commit();

    echo json_encode([
        'success' => true,
        'pedido_id' => $pedido_id,
        'updatedProducts' => $productos_actualizados,
        'nombre' => $cliente['Nombre'],
        'apellido' => $cliente['Apellido'],
        'email' => $cliente['Correo'],
        'telefono' => $cliente['Telefono'],
    ]);
    exit();
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>