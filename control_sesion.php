<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'database.php';


//Se da a entender que la respuesta se dará en JSON 
//Para el frontend
header('Content-Type: application/json');

try {
    //El database hace la conexión con la base de datos
    $conexion = Database::conectar();
    //Recepciona y limpia los espacios en blanco.
    $correo = trim($_POST['emailLogin']);
    $contrasena = trim($_POST['passwordLogin']);


    // Buscar usuario en la tabla de usuarios
    $stmt_usuario = $conexion->prepare("SELECT Id_Usuario, Correo, Contrasena, Estado_Usuario, Fecha_Registro FROM tb_usuario WHERE LOWER(Correo) = LOWER(:correo);");
    $stmt_usuario->bindParam(':correo', $correo);
    $stmt_usuario->execute();
    $usuario = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
    $correo_buscado = $usuario['Correo'];
    $usuario_id = $usuario['Id_Usuario'];

    if($correo_buscado == $correo){
        // Si el correo existe, se busca el usuario en la tabla de clientes
        // Id del usuario
       
        // Si el correo existe, se busca el tipo de usuario
        $tipo_usuario_admin = $conexion->prepare("
            SELECT U.Id_Usuario, U.Correo, U.Contrasena, U.Estado_Usuario, U.Fecha_Registro,
                CASE 
                    WHEN C.Id_Cliente IS NOT NULL THEN 'Cliente'
                    WHEN A.Id_Administrador IS NOT NULL THEN 'Administrador'
                    WHEN R.Id_Repartidor IS NOT NULL THEN 'Repartidor'
                    ELSE 'No especificado'
                END AS Tipo_Usuario
            FROM tb_usuario U
            LEFT JOIN tb_cliente C ON U.Id_Usuario = C.Id_Cliente
            LEFT JOIN tb_administrador A ON U.Id_Usuario = A.Id_Administrador
            LEFT JOIN tb_repartidor R ON U.Id_Usuario = R.Id_Repartidor
            WHERE U.Id_Usuario = :idUsuario;
        ");
        $tipo_usuario_admin->bindParam(':idUsuario', $usuario_id, PDO::PARAM_INT);
        $tipo_usuario_admin->execute();
        $usuario = $tipo_usuario_admin->fetch(PDO::FETCH_ASSOC);

        //Campos consultados
        $tipo = $usuario['Tipo_Usuario']; // Cliente, Administrador, Repartidor
        $contrasena_BD = $usuario['Contrasena'];
        $estado_usuario = $usuario['Estado_Usuario'];
        $correo_usuario = $usuario['Correo'];
        $usuario_encontrado_id = $usuario['Id_Usuario'];


  
        if ($tipo == 'Administrador' && password_verify($contrasena, $contrasena_BD ) && $estado_usuario == 1) {
            $_SESSION['id'] = $usuario_encontrado_id;
            $_SESSION['usuario'] = $correo_usuario;
            $_SESSION['tipo'] = $tipo;
            ob_clean(); // Limpia cualquier salida previa
            echo json_encode(["success" => true, "message" => "Inicio de sesión como Administrador.", "redirect" => "Admin/admin-page.php"]);
            exit;

        } elseif ($tipo == 'Cliente' && password_verify($contrasena, $contrasena_BD ) && $estado_usuario == 1) {
            $_SESSION['id'] = $usuario_encontrado_id;
            $_SESSION['usuario'] = $correo_usuario;
            $_SESSION['tipo'] = $tipo;
            ob_clean(); // Limpia cualquier salida previa
            echo json_encode(["success" => true, "message" => "Inicio de sesión exitoso.", "redirect" => "index.php"]);
            exit;

        } elseif ($tipo == 'Repartidor' && password_verify($contrasena, $contrasena_BD ) && $estado_usuario == 1){
            $_SESSION['id'] = $usuario_encontrado_id;
            $_SESSION['usuario'] = $correo_usuario;
            $_SESSION['tipo'] = $tipo;
            ob_clean(); // Limpia cualquier salida previa
            echo json_encode(["success" => true, "message" => "Inicio de sesión como empleado.", "redirect" => "index.php"]);
            exit;
        }  elseif ( $estado_usuario == 0){
            ob_clean(); // Limpia cualquier salida previa
            echo json_encode(["success" => false, "message" => "Cuenta inhabilitada", "redirect" => "login.php"]);
            exit;
        }
        else {
            ob_clean(); // Limpia cualquier salida previa
            echo json_encode(["success" => false, "message" => "Contraseña Incorrecta", "redirect" => "login.php"]);
            exit;
        }

    }else{
        ob_clean(); // Limpia cualquier salida previa
        echo json_encode(["success" => false, "message" => "Correo inválido."]);
        exit;
    }
    //Si el error ocurre en laa base de datos se mandará un mensaje 
    //de error de conexión
} catch (PDOException $e) {
    ob_clean(); // Limpia cualquier salida previa
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $e->getMessage()]);
    exit;
}

?>