<?php
require '../Config/config.php';
require '../database.php';

$db = new Database();
$con = $db->conectar();

// Verificar si el dato ingresado existe para editar un repartidor
if (
    isset($_POST['tipo']) &&
    isset($_POST['valor']) &&
    isset($_POST['dni']) &&
    isset($_POST['action']) &&
    $_POST['action'] === 'edit'
) {
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];
    $dni = $_POST['dni']; // DNI del repartidor que se está editando

    $campo = "";
    $tabla = "";
    $campoClave = "";
    $query = "";
    $params = [];

    switch ($tipo) {
        case "dni":
            $campo = "DNI";
            $tabla = "tb_persona";
            $campoClave = "DNI";
            $query = "SELECT COUNT(*) as count FROM $tabla WHERE $campo = ? AND $campoClave != ?";
            $params = [$valor, $dni];
            break;
        case "telefono":
            $campo = "Telefono";
            $tabla = "tb_persona";
            $campoClave = "DNI";
            $query = "SELECT COUNT(*) as count FROM $tabla WHERE $campo = ? AND $campoClave != ?";
            $params = [$valor, $dni];
            break;
        case "correo":
            $campo = "Correo";
            $tabla = "tb_usuario";
            // Buscar el Id_Usuario a partir del DNI
            $stmt = $con->prepare("SELECT Id_Persona FROM tb_persona WHERE DNI = ?");
            $stmt->execute([$dni]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_usuario = $row ? $row['Id_Persona'] : 0;
            $query = "SELECT COUNT(*) as count FROM $tabla WHERE $campo = ? AND Id_Usuario != ?";
            $params = [$valor, $id_usuario];
            break;
        case "placa":
            $campo = "Placa";
            $tabla = "tb_repartidor";
            // Buscar el Id_Repartidor a partir del DNI
            $stmt = $con->prepare("SELECT Id_Persona FROM tb_persona WHERE DNI = ?");
            $stmt->execute([$dni]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_repartidor = $row ? $row['Id_Persona'] : 0;
            $query = "SELECT COUNT(*) as count FROM $tabla WHERE $campo = ? AND Id_Repartidor != ?";
            $params = [$valor, $id_repartidor];
            break;
        default:
            echo "error";
            exit;
    }

    $stmt = $con->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo ($result && $result['count'] > 0) ? "existe" : "disponible";
    exit;
}

echo "error";
exit;

?>