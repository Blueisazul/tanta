<?php

require '../Config/config.php';
require '../database.php';

$db = new Database();
$con = $db->conectar();


//Verificar si el dato ingresado existe para agregar un repartidor
if (isset($_POST['tipo']) && isset($_POST['valor']) && isset($_POST['dni']) && isset($_POST['action']) && $_POST['action'] === 'agre_U') {
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];
    $dni = $_POST['dni']; // Recibimos el DNI del repartidor en edición

    $campo = "";
    switch ($tipo) {

        case "dni":
            $campo = "DNI";
            break;
        case "telefono":
            $campo = "Telefono";
            break;
        case "correo":
            $campo = "Correo";
            break;
        default:
            echo "error";
            exit;
    }

    if($tipo == "dni" || $tipo == "telefono"){
        // Verificar si el DNI o teléfono ya existe en la tabla repartidor
        $stmt = $con->prepare("SELECT COUNT(*) as count FROM tb_persona WHERE $campo = ?");
        $stmt->execute([$valor]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo ($result && $result['count'] > 0) ? "existe" : "disponible";
        exit;
    }else if($tipo == "correo"){
        // Verificar si el correo ya existe en la tabla repartidor
        $stmt = $con->prepare("SELECT COUNT(*) as count FROM tb_usuario WHERE $campo = ?");
        $stmt->execute([$valor]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo ($result && $result['count'] > 0) ? "existe" : "disponible";
        exit;
    }else{
        // Verificar si la placa ya existe en la tabla repartidor
        $stmt = $con->prepare("SELECT COUNT(*) as count FROM tb_cliente WHERE $campo = ?");
        $stmt->execute([$valor]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo ($result && $result['count'] > 0) ? "existe" : "disponible";
        exit;
    }
}

?>