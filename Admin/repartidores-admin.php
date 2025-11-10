<?php
session_start();
require '../Config/config.php';
require '../database.php';
$db = new Database();
$con = $db->conectar();
if (isset($_SESSION['usuario'])) {
    if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Cliente') {
        header("Location: ../index.php");
        exit();
    } else {
        $sql = $con->prepare("SELECT Nombre, Apellido, DNI, Telefono FROM tb_persona WHERE Id_Persona = :id");
        $sql->bindParam(':id', $_SESSION['id'] , PDO::PARAM_INT);
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $sql->execute();
        $datos_persona = $sql->fetch(PDO::FETCH_ASSOC);
        $nombre = $datos_persona['Nombre'];
    }
}


//Agregar Repartidor
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
   
    $nombre = trim($_POST["nombreR"]);
    $apellido = trim($_POST["apellidoR"]);
    $dni = trim($_POST["DNIR"]);
    $distrito = trim($_POST["distritoR"]);
    $telefono = trim($_POST["telefonoR"]);
    $genero = trim($_POST["sexoR"]);
    $direccion = trim($_POST["direccionR"]);
    $correo = trim($_POST["correoR"]);
    $contrasena = trim($_POST["contrasenaR"]);
    $placa = trim($_POST["placa"]);
    $vehiculo = trim($_POST["moto"]);


    $contrasena_encriptada = password_hash($contrasena, PASSWORD_DEFAULT);
    $genero_valor = (int) $genero; // Convertir a entero para evitar problemas de tipo

    // header: Indica que la respuesta es de tipo JSON (echo json_encode)
    header('Content-Type: application/json');

    //Se realizan las consultas para insertar los datos en las tablas correspondientes
    //Se utiliza el método prepare para preparar la consulta SQL
    $sql_persona = $con->prepare("INSERT INTO tb_persona (Nombre, Apellido, DNI, Telefono, Sexo) VALUES (?, ?, ?, ?, ?)");
    $resultado_persona = $sql_persona->execute([$nombre, $apellido, $dni, $telefono, $genero_valor]);

    //Se obtiene el ID de la última persona insertada
    //Esto es necesario para poder insertar el ID en la tabla de usuario y cliente
    $id_persona = $con->lastInsertId();

    $sql_usuario = $con->prepare("INSERT INTO tb_usuario (Id_Usuario, Correo, Contrasena) VALUES (?, ?, ?)");
    $resultado_usuario = $sql_usuario->execute([$id_persona , $correo, $contrasena_encriptada]);

    $sql_cliente = $con->prepare("INSERT INTO tb_repartidor (Id_Repartidor, Id_Distrito, Direccion_Repartidor, Placa, Vehiculo) VALUES (?, ?, ?, ?, ?)");
    $resultado_repartidor = $sql_cliente->execute([$id_persona, $distrito, $direccion, $placa, $vehiculo]);

    //Verifica si las consultas se ejecutaron correctamente
    //Si todas las consultas se ejecutaron correctamente, se devuelve un mensaje de éxito
    if ($resultado_persona && $resultado_usuario && $resultado_repartidor) {

        echo json_encode(["status" => "success", "message" => "Usuario agregado correctamente"]);
        exit;
    } else {
        
        echo json_encode(["status" => "error", "message" => "Error al agregar el usuario"]);
        exit;
    }
}

if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {
    // Recoge los datos del formulario de edición
    $id_persona = trim($_POST["codigoEditR"]);
    $nombre = trim($_POST["nombreEditR"]);
    $apellido = trim($_POST["apellidoEditR"]);
    //$dni = trim($_POST["DNIEditR"]);
    $distrito = trim($_POST["distritoEditR"]);
    $telefono = trim($_POST["telefonoEditR"]);
    $genero = trim($_POST["sexoEditR"]);
    $direccion = trim($_POST["direccionEditR"]);
    $correo = trim($_POST["correoEditR"]);
    $placa = trim($_POST["placaEdit"]);
    $vehiculo = trim($_POST["motoEdit"]);
    $estado_usuario = trim($_POST["estadoUREdit"]);
    $estado_repartidor = trim($_POST["estadoREdit"]);

    header('Content-Type: application/json');

    try {
        // Actualizar tb_persona
        $sql_persona = $con->prepare("UPDATE tb_persona SET Nombre=?, Apellido=?, Telefono=?, Sexo=? WHERE Id_Persona=?");
        $resultado_persona = $sql_persona->execute([$nombre, $apellido, $telefono, $genero, $id_persona]);

        // Actualizar tb_usuario
        $sql_usuario = $con->prepare("UPDATE tb_usuario SET Correo=?, Estado_Usuario=? WHERE Id_Usuario=?");
        $resultado_usuario = $sql_usuario->execute([$correo, $estado_usuario, $id_persona]);

        // Actualizar tb_repartidor
        $sql_repartidor = $con->prepare("UPDATE tb_repartidor SET Id_Distrito=?, Direccion_Repartidor=?, Placa=?, Vehiculo=?, Estado_Repartidor=? WHERE Id_Repartidor=?");
        $resultado_repartidor = $sql_repartidor->execute([$distrito, $direccion, $placa, $vehiculo, $estado_repartidor, $id_persona]);

        if ($resultado_persona && $resultado_usuario && $resultado_repartidor) {
            echo json_encode(["status" => "success", "message" => "Datos del repartidor actualizados correctamente"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Error al actualizar el repartidor"]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
        exit;
    }
}

// Verificar si se envió la solicitud para eliminar un producto
if (isset($_POST['elimina']) && $_POST['elimina'] === 'eliminar' && isset($_POST['codigo'])) {
    // Preparar la consulta SQL de eliminación
    $sql_delete = $con->prepare("UPDATE tb_usuario SET Estado_Usuario = ? WHERE Id_Usuario= ?;");

    // Vincular el código del producto y ejecutar la consulta de eliminación
    $codigo = $_POST['codigo'];
    $estado = 0;

    $sql_delete->bindParam(1, $estado, PDO::PARAM_INT);
    $sql_delete->bindParam(2, $codigo, PDO::PARAM_INT);

    header('Content-Type: application/json');
    if ($sql_delete->execute()) {
        echo json_encode(["status" => "success", "message" => "Repartidor eliminado correctamente"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Error al eliminar el Repartidor"]);
        exit;
    }
    exit(); // Detener la ejecución del script después de manejar la solicitud de eliminación
}




// Se crea la consulta
$sql = $con->prepare("SELECT
    p.Id_Persona,
    p.Nombre,
    p.Apellido,
    p.DNI,
    p.Telefono,
    p.Sexo,
    u.Correo,
    u.Estado_Usuario,
    u.Fecha_Registro,
    r.Id_Distrito,
    d.Descripcion_Distrito,
    r.Direccion_Repartidor,
    r.Placa,
    r.Vehiculo,
    r.Estado_Repartidor
FROM TB_REPARTIDOR r
INNER JOIN TB_USUARIO u ON r.Id_Repartidor = u.Id_Usuario
INNER JOIN TB_PERSONA p ON u.Id_Usuario = p.Id_Persona
INNER JOIN TB_DISTRITO d ON r.Id_Distrito = d.Id_Distrito");

// Se ejecuta la consulta
$sql->execute();

// Recupera los resultados de la consulta
$repartidor = $sql->fetchAll(PDO::FETCH_ASSOC);


?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Repartidores</title>
    <meta name="author" content="David Grzyb">
    <meta name="description" content="">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary: #2655ea;
            --light-bg: #f9fafb;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-secondary: #6b7280;
            --primary-gradient: linear-gradient(to right, #4278f5, #2655ea);
            --border-color: #e5e7eb;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-main);
        }
        
        .sidebar {
            background: var(--card-bg);
            border-right: 1px solid var(--border-color);
        }
        
        .nav-item {
            transition: all 0.2s ease;
            color: var(--text-secondary);
        }
        
        .nav-item:hover {
            background: #f3f4f6;
            color: var(--text-main);
        }
        
        .active-nav-link {
            background: #f3f4f6;
            color: var(--primary);
            border-left: 3px solid var(--primary);
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }
        
        .btn-primary:hover {
            background: #4338ca;
        }
        
        .btn-outline {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }
        
        .btn-outline:hover {
            background: #f9fafb;
        }
        
        .status-active {
            background: #ecfdf5;
            color: #065f46;
        }
        
        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        .fondo-degrade {
            position: relative;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            background: linear-gradient(to top, white 70%, #e0f7fa 90%);
        }

        .circle {
            position: absolute;
            overflow: hidden;
            top: -160px;
            right: -160px;
            height: 320px;
            width: 320px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00e5ff 20%, #0091ea 100%);
            opacity: 0.3;
            filter: blur(80px); /* Efecto difuminado intenso */
            z-index: 0; /* <-- Añade esta línea */
        }
        .select2-container .select2-selection--single {
            height: 45px !important;
            display: flex;
            align-items: center;
            
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px !important;
        }
        /* Centrar el icono del menú desplegable */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px !important;
            top: 0 !important;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
        
        #addRepartidorModal>div,
        #editRepartidorModal>div {
            max-height: 95vh;
            overflow-y: auto;
        }
        .tabla-scroll {
            max-height: 500px;   /* Ajusta la altura máxima a tu gusto */
            overflow-y: auto;
        }
    </style>
</head>
<body class="flex h-screen fondo-degrade">
    <div class="circle"></div>

    <!-- Sidebar -->
    <aside class="sidebar w-64 hidden sm:block shadow-sm">
        <div class="p-6 flex items-center border-b border-gray-200">
            <a href="../Admin/admin-page.php" class="text-xl font-bold text-gray-800">
                <?php include '../components/logo.php'; ?>
            </a>
        </div>
        <nav class="text-sm font-medium pt-3">
            <a href="../Admin/admin-page.php" class="flex items-center py-3 pl-6 nav-item">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-gauge-icon lucide-gauge">
                        <path d="m12 14 4-4"/>
                        <path d="M3.34 19a10 10 0 1 1 17.32 0"/>
                    </svg>
                </span>
                Dashboard
            </a>
            <a href="../Admin/productos-admin.php" class="flex items-center py-3 pl-6 nav-item">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package-icon lucide-package">
                        <path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/>
                        <path d="M12 22V12"/>
                        <polyline points="3.29 7 12 12 20.71 7"/>
                        <path d="m7.5 4.27 9 5.15"/>
                    </svg>
                </span>
                Productos
            </a>
            <a href="../Admin/reporte-admin.php" class="flex items-center py-3 pl-6 nav-item">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="lucide lucide-layers-icon lucide-layers">
                        <path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"/>
                        <path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"/>
                        <path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"/>
                    </svg>                
                </span>
                Reportes BI
            </a>   
            <a href="../Admin/pedidos-admin.php" class="flex items-center py-3 pl-6 nav-item">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag-icon lucide-shopping-bag">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </span>
                Pedidos
            </a>
            <a href="../Admin/clientes-admin.php" class="flex items-center py-3 pl-6 nav-item">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-user-icon lucide-file-user">
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                        <path d="M15 18a3 3 0 1 0-6 0"/>
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/>
                        <circle cx="12" cy="13" r="2"/>
                    </svg>
                </span>
                Clientes
            </a>
            <a href="../Admin/repartidores-admin.php" class="flex items-center py-3 pl-6 nav-item active-nav-link">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck-icon lucide-truck">
                        <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                        <path d="M15 18H9"/>
                        <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/>
                        <circle cx="17" cy="18" r="2"/>
                        <circle cx="7" cy="18" r="2"/>
                    </svg>
                </span>
                Repartidores
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden relative z-[1]">
        <!-- Header -->
        <header class="border-b border-gray-200 py-4 px-6 flex items-center justify-between">
            <div class="w-1/2"></div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    <span>Bienvenido,</span>
                    <span class="font-medium"><?php echo $nombre; ?></span>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-8 h-8 rounded-full overflow-hidden border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-circle-user-round-icon lucide-circle-user-round">
                            <path d="M18 20a6 6 0 0 0-12 0" />
                            <circle cx="12" cy="10" r="4" />
                            <circle cx="12" cy="12" r="10" />
                        </svg> </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Mi cuenta</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Soporte</a>
                        <a href="#" onclick="confirmarCerrarSesion(event)"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Header and Actions  addRepartidorModal-->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h1 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">Listado de Repartidores</h1>
                <button onclick="document.getElementById('addRepartidorModal').classList.remove('hidden')" class="px-4 py-2 btn-primary rounded-md text-sm font-medium">
                    <i class="fas fa-plus mr-2"></i>Agregar Repartidor
                </button>
            </div>
            
            <!-- Search Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <input type="text"  id="buscarDNI" name="buscarDNI" placeholder="Buscar por DNI" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <input type="text"  id="buscarNombre" name="buscarDNI" placeholder="Buscar por Nombre" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <input type="text"  id="buscarApellido" name="buscarDNI" placeholder="Buscar por Apellido" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <input type="text"  id="buscarTelefono" name="buscarDNI" placeholder="Buscar por Teléfono" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <input type="text"  id="buscarCorreo" name="buscarDNI"  placeholder="Buscar por Correo" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <!-- Filters -->

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="buscarGenero" class="block mb-2 text-sm font-medium text-gray-700">Género:</label>
                    <select id="buscarGenero" name="buscarGenero" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="3">Todos</option>
                        <option value="0">Masculino</option>
                        <option value="1">Femenino</option>
                        <option value="2">Otro</option>
                    </select>
                </div>
                <div>
                    <label for="buscarEstadoU" class="block mb-2 text-sm font-medium text-gray-700">Estado Usuario:</label>
                    <select  id="buscarEstadoU" name="buscarEstadoU" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="2">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div>
                    <label for="buscarEstadoR" class="block mb-2 text-sm font-medium text-gray-700">Estado Repartidor:</label>
                    <select id="buscarEstadoR" name="buscarEstadoR"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="2">Todos</option>
                        <option value="1">Disponible</option>
                        <option value="0">No disponible</option>
                    </select>
                </div>
            </div>
            
            <!-- AGREGAR REPARTIDOR -->
            <div id="addRepartidorModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Agregar Nuevo Repartidor</h2>
                    <form id="formAgregarRepartidor" class="grid grid-cols-2 gap-4">
                        <!-- Código (deshabilitado) -->
                        <div class="col-span-2">
                            <label for="codigoR" class="block mb-1 text-sm font-semibold text-gray-700">Código:</label>
                            <input type="text" id="codigoR" name="codigoR" disabled value=""
                                class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-500">
                        </div>
                        <!-- Nombre -->
                        <div class="col-span-2">
                            <label for="nombreR" class="block mb-1 text-sm font-semibold text-gray-700">Nombre:</label>
                            <input type="text" id="nombreR" name="nombreR" placeholder="Nombre del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaNombreR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Apellido -->
                        <div>
                            <label for="apellidoR" class="block mb-1 text-sm font-semibold text-gray-700">Apellido:</label>
                            <input type="text" id="apellidoR" name="apellidoR" placeholder="Apellido del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaApellidoR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- DNI -->
                        <div>
                            <label for="DNIR" class="block mb-1 text-sm font-semibold text-gray-700">DNI:</label>
                            <input type="text" id="DNIR" name="DNIR" placeholder="DNI del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded" maxlength="8">
                            <span id="validaDNIR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <div>
                            <label for="distritoR" class="block mb-1 text-sm font-semibold text-gray-700">Distrito:</label>
                            <select id="distritoR" name="distritoR" class="w-full bg-gray-200 h-11 rounded-lg px-3" required>
                                <option value="">Selecciona un distrito</option>
                                <option value="1">Ancón</option>
                                <option value="2">Ate</option>
                                <option value="3">Barranco</option>
                                <option value="4">Breña</option>
                                <option value="5">Carabayllo</option>
                                <option value="6">Cercado de Lima</option>
                                <option value="7">Chaclacayo</option>
                                <option value="8">Chorrillos</option>
                                <option value="9">Cieneguilla</option>
                                <option value="10">Comas</option>
                                <option value="11">El Agustino</option>
                                <option value="12">Independencia</option>
                                <option value="13">Jesús María</option>
                                <option value="14">La Molina</option>
                                <option value="15">La Victoria</option>
                                <option value="16">Lince</option>
                                <option value="17">Los Olivos</option>
                                <option value="18">Lurigancho</option>
                                <option value="19">Lurín</option>
                                <option value="20">Magdalena del Mar</option>
                                <option value="21">Miraflores</option>
                                <option value="22">Pachacámac</option>
                                <option value="23">Pucusana</option>
                                <option value="24">Pueblo Libre</option>
                                <option value="25">Puente Piedra</option>
                                <option value="26">Punta Hermosa</option>
                                <option value="27">Punta Negra</option>
                                <option value="28">Rímac</option>
                                <option value="29">San Bartolo</option>
                                <option value="30">San Borja</option>
                                <option value="31">San Isidro</option>
                                <option value="32">San Juan de Lurigancho</option>
                                <option value="33">San Juan de Miraflores</option>
                                <option value="34">San Luis</option>
                                <option value="35">San Martín de Porres</option>
                                <option value="36">San Miguel</option>
                                <option value="37">Santa Anita</option>
                                <option value="38">Santa María del Mar</option>
                                <option value="39">Santa Rosa</option>
                                <option value="40">Santiago de Surco</option>
                                <option value="41">Surquillo</option>
                                <option value="42">Villa El Salvador</option>
                                <option value="43">Villa María del Triunfo</option>
                            </select>
                            <span id="validaDistritoR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Teléfono -->
                        <div>
                            <label for="telefonoR" class="block mb-1 text-sm font-semibold text-gray-700">Teléfono:</label>
                            <input type="text" id="telefonoR" name="telefonoR" placeholder="Telefono del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded" maxlength="9">
                            <span id="validaTelefonoR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Sexo -->
                        <div>
                            <label for="sexoR"
                                class="block mb-1 text-sm font-semibold text-gray-700">Género:</label>
                            <select id="sexoR" name="sexoR"
                                class="w-full bg-gray-200 h-11 rounded-lg px-3" required>
                                <option value="">Selecciona género</option>
                                <option value="0">Masculino</option>
                                <option value="1">Femenino</option>
                                <option value="2">Otro</option>
                            </select>
                            <span id="validaSexoR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Dirección -->
                        <div>
                            <label for="direccionR" class="block mb-1 text-sm font-semibold text-gray-700">Dirección:</label>
                            <input type="text" id="direccionR" name="direccionR" placeholder="Dirección del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaDireccionR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Correo -->
                        <div class="col-span-2">
                            <label for="correoR" class="block mb-1 text-sm font-semibold text-gray-700">Correo:</label>
                            <input type="text" id="correoR" name="correoR" placeholder="Correo del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaCorreoR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Contrasena -->
                        <div class="col-span-2">
                            <label for="contrasenaR" class="block mb-1 text-sm font-semibold text-gray-700">Contraseña:</label>
                            <div class="relative">
                                <input type="password" id="contrasenaR" name="contrasenaR" placeholder="Contraseña del repartidor"
                                    class="w-full px-4 py-2 border border-gray-300 rounded">
                                    <!-- Boton del ojo para visualizar la contraseña -->
                                <button type="button" id="verContraRepartidor" class="absolute inset-y-0 right-3 flex items-center text-gray-600">
                                    <i id="eyeIcon" class="fa-solid fa-eye"></i><!--Icono de ojo que cambiar al darle clic -->
                                </button>
                            </div>
                            <span id="validaContrasenaR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Contrasena confirmar -->
                        <div class="col-span-2">
                            <label for="contrasenaCR" class="block mb-1 text-sm font-semibold text-gray-700">Contraseña:</label>
                            <div class="relative">
                                <input type="password" id="contrasenaCR" name="contrasenaCR" placeholder="Confirmar contraseña"
                                    class="w-full px-4 py-2 border border-gray-300 rounded">
                            </div>
                            <span id="validaContrasenaCR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Placa -->
                        <div>
                            <label for="placa" class="block mb-1 text-sm font-semibold text-gray-700">Placa:</label>
                            <input type="text" id="placa" name="placa" placeholder="Placa de la moto"
                                class="w-full px-4 py-2 border border-gray-300 rounded" maxlength="8">
                            <span id="validaPlaca" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Vehículo -->
                        <div>
                            <label for="moto" class="block mb-1 text-sm font-semibold text-gray-700">Moto:</label>
                            <input type="text" id="moto" name="moto" placeholder="Modelo de la moto"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaMoto" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Estado del Usuario -->
                        <div class="col-span-2">
                            <label for="estadoUR" class="block mb-1 text-sm font-semibold text-gray-700">Estado Usuario:</label>
                            <select id="estadoUR" name="estadoUR" class="w-full px-4 py-2 border border-gray-300 rounded" disabled>
                                <option value="">Activo</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            <span id="validaEstadoUR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Estado del Repartidor-->
                        <div class="col-span-2">
                            <label for="estadoR" class="block mb-1 text-sm font-semibold text-gray-700">Estado Repartidor:</label>
                            <select id="estadoR" name="estadoR" class="w-full px-4 py-2 border border-gray-300 rounded" disabled>
                                <option value="">Disponible</option>
                                <option value="1">Disponible</option>
                                <option value="0">No disponible</option>
                            </select>
                            <span id="validaEstadoR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Botones -->
                        <div class="col-span-2 flex justify-end gap-2">
                            <button type="button"
                                onclick="document.getElementById('addRepartidorModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
                        </div>
                    </form>
                    <button onclick="document.getElementById('addRepartidorModal').classList.add('hidden')"
                        class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
            </div>

            <!-- EDITAR REPARTIDOR-->
            <div id="editRepartidorModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Editar Repartidor</h2>
                    <form id="formEditarRepartidor" class="grid grid-cols-2 gap-4" method="POST" enctype="multipart/form-data">
                         <!-- Código (deshabilitado) -->
                        <div class="col-span-2">
                            <label for="codigoEditR" class="block mb-1 text-sm font-semibold text-gray-700">Código:</label>
                            <input type="text" id="codigoEditR" name="codigoEditR" readonly value=""
                                class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-500">
                        </div>
                        <!-- Nombre -->
                        <div class="col-span-2">
                            <label for="nombreEditR" class="block mb-1 text-sm font-semibold text-gray-700">Nombre:</label>
                            <input type="text" id="nombreEditR" name="nombreEditR" placeholder="Nombre del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaNombreEditR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Apellido -->
                        <div>
                            <label for="apellidoEditR" class="block mb-1 text-sm font-semibold text-gray-700">Apellido:</label>
                            <input type="text" id="apellidoEditR" name="apellidoEditR" placeholder="Apellido del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaApellidoEditR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- DNI -->
                        <div>
                            <label for="DNIEditR" class="block mb-1 text-sm font-semibold text-gray-700">DNI:</label>
                            <input type="text" id="DNIEditR" name="DNIEditR" placeholder="DNI del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded" maxlength="8" readonly>
                            <span id="validaDNIEditR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <div>
                            <label for="distritoEditR" class="block mb-1 text-sm font-semibold text-gray-700">Distrito:</label>
                            <select id="distritoEditR" name="distritoEditR" class="w-full bg-gray-200 h-11 rounded-lg px-3" required>
                                <option value="">Selecciona un distrito</option>
                                <option value="1">Ancón</option>
                                <option value="2">Ate</option>
                                <option value="3">Barranco</option>
                                <option value="4">Breña</option>
                                <option value="5">Carabayllo</option>
                                <option value="6">Cercado de Lima</option>
                                <option value="7">Chaclacayo</option>
                                <option value="8">Chorrillos</option>
                                <option value="9">Cieneguilla</option>
                                <option value="10">Comas</option>
                                <option value="11">El Agustino</option>
                                <option value="12">Independencia</option>
                                <option value="13">Jesús María</option>
                                <option value="14">La Molina</option>
                                <option value="15">La Victoria</option>
                                <option value="16">Lince</option>
                                <option value="17">Los Olivos</option>
                                <option value="18">Lurigancho</option>
                                <option value="19">Lurín</option>
                                <option value="20">Magdalena del Mar</option>
                                <option value="21">Miraflores</option>
                                <option value="22">Pachacámac</option>
                                <option value="23">Pucusana</option>
                                <option value="24">Pueblo Libre</option>
                                <option value="25">Puente Piedra</option>
                                <option value="26">Punta Hermosa</option>
                                <option value="27">Punta Negra</option>
                                <option value="28">Rímac</option>
                                <option value="29">San Bartolo</option>
                                <option value="30">San Borja</option>
                                <option value="31">San Isidro</option>
                                <option value="32">San Juan de Lurigancho</option>
                                <option value="33">San Juan de Miraflores</option>
                                <option value="34">San Luis</option>
                                <option value="35">San Martín de Porres</option>
                                <option value="36">San Miguel</option>
                                <option value="37">Santa Anita</option>
                                <option value="38">Santa María del Mar</option>
                                <option value="39">Santa Rosa</option>
                                <option value="40">Santiago de Surco</option>
                                <option value="41">Surquillo</option>
                                <option value="42">Villa El Salvador</option>
                                <option value="43">Villa María del Triunfo</option>
                            </select>
                            <span id="validaDistritoEditR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Teléfono -->
                        <div>
                            <label for="telefonoEditR" class="block mb-1 text-sm font-semibold text-gray-700">Teléfono:</label>
                            <input type="text" id="telefonoEditR" name="telefonoEditR" placeholder="Telefono del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded" maxlength="9">
                            <span id="validaTelefonoEditR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Sexo -->
                        <div>
                            <label for="sexoEditR"
                                class="block mb-1 text-sm font-semibold text-gray-700">Género:</label>
                            <select id="sexoEditR" name="sexoEditR"
                                class="w-full bg-gray-200 h-11 rounded-lg px-3" required>
                                <option value="">Selecciona género</option>
                                <option value="0">Masculino</option>
                                <option value="1">Femenino</option>
                                <option value="2">Otro</option>
                            </select>
                            <span id="validaSexoEditR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Dirección -->
                        <div>
                            <label for="direccionEditR" class="block mb-1 text-sm font-semibold text-gray-700">Dirección:</label>
                            <input type="text" id="direccionEditR" name="direccionEditR" placeholder="Dirección del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaDireccionEditR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Correo -->
                        <div class="col-span-2">
                            <label for="correoEditR" class="block mb-1 text-sm font-semibold text-gray-700">Correo:</label>
                            <input type="text" id="correoEditR" name="correoEditR" placeholder="Correo del repartidor"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaCorreoEditR" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                       
                        <!-- Placa -->
                        <div>
                            <label for="placaEdit" class="block mb-1 text-sm font-semibold text-gray-700">Placa:</label>
                            <input type="text" id="placaEdit" name="placaEdit" placeholder="Placa de la moto"
                                class="w-full px-4 py-2 border border-gray-300 rounded" maxlength="8">
                            <span id="validaPlacaEdit" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Vehículo -->
                        <div>
                            <label for="motoEdit" class="block mb-1 text-sm font-semibold text-gray-700">Moto:</label>
                            <input type="text" id="motoEdit" name="motoEdit" placeholder="Modelo de la moto"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaMotoEdit" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Estado del Usuario -->
                        <div class="col-span-2">
                            <label for="estadoUREdit" class="block mb-1 text-sm font-semibold text-gray-700">Estado Usuario:</label>
                            <select id="estadoUREdit" name="estadoUREdit" class="w-full px-4 py-2 border border-gray-300 rounded" required>
                                <option disabled selected value="">Selecione Estado</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            <span id="validaEstadoUREdit" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Estado del Repartidor-->
                        <div class="col-span-2">
                            <label for="estadoREdit" class="block mb-1 text-sm font-semibold text-gray-700">Disponibilidad:</label>
                            <select id="estadoREdit" name="estadoREdit" class="w-full px-4 py-2 border border-gray-300 rounded" required>
                                <option disabled selected value="">Seleccione Disponibilidad</option>
                                <option value="1">Disponible</option>
                                <option value="0">No disponible</option>
                            </select>
                            <span id="validaEstadoREdit" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Botones -->
                        <div class="col-span-2 flex justify-end gap-2">
                            <button type="button"
                                onclick="document.getElementById('editRepartidorModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
                        </div>
                    </form>
                    <button onclick="document.getElementById('editRepartidorModal').classList.add('hidden')"
                        class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
            </div>
            
            <!-- Delivery Table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto tabla-scroll" style="max-height: 500px; overflow-y: auto;">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Género</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Repartidor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($repartidor as $row): ?>
                                    <tr class="hover:bg-gray-50"
                                        data-genero="<?= htmlspecialchars($row['Sexo']) ?>"
                                        data-estadou="<?= htmlspecialchars($row['Estado_Usuario']) ?>"
                                        data-estador="<?= htmlspecialchars($row['Estado_Repartidor']) ?>"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dni"><?php echo htmlspecialchars($row['DNI']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 nombre"><?php echo htmlspecialchars($row['Nombre']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 apellido"><?php echo htmlspecialchars($row['Apellido']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 telefono"><?php echo htmlspecialchars($row['Telefono']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php
                                                switch ($row['Sexo']) {
                                                    case 0: echo "Masculino"; break;
                                                    case 1: echo "Femenino"; break;
                                                    case 2: echo "Otro"; break;
                                                    default: echo "No especificado";
                                                }
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 correo"><?php echo htmlspecialchars($row['Correo']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['Direccion_Repartidor']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['Placa']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['Vehiculo']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo $row['Estado_Usuario'] == 1 ? '<span class="status-active px-2 py-1 rounded">Activo</span>' : '<span class="status-inactive px-2 py-1 rounded">Inactivo</span>'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo $row['Estado_Repartidor'] == 1 ? '<span class="status-active px-2 py-1 rounded">Disponible</span>' : '<span class="status-inactive px-2 py-1 rounded">No disponible</span>'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <!-- Botones de acción -->
                                            <button
                                                onclick="document.getElementById('editRepartidorModal').classList.remove('hidden')"
                                                class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs btn-editar-repartidor"
                                                data-codigo="<?php echo htmlspecialchars( $row['Id_Persona']); ?>"
                                                data-nombre="<?php echo htmlspecialchars( $row['Nombre']); ?>"
                                                data-apellido="<?php echo htmlspecialchars($row['Apellido']); ?>"
                                                data-dni="<?php echo htmlspecialchars($row['DNI']); ?>"
                                                data-telefono="<?php echo htmlspecialchars($row['Telefono']); ?>"
                                                data-sexo="<?php echo htmlspecialchars($row['Sexo']); ?>"
                                                data-correo="<?php echo htmlspecialchars($row['Correo']); ?>"
                                                data-distrito="<?php echo htmlspecialchars($row['Id_Distrito']); ?>"
                                                data-direccion="<?php echo htmlspecialchars($row['Direccion_Repartidor']); ?>"
                                                data-placa="<?php echo htmlspecialchars($row['Placa']); ?>"
                                                data-moto="<?php echo htmlspecialchars($row['Vehiculo']); ?>"
                                                data-estadou="<?php echo htmlspecialchars($row['Estado_Usuario']); ?>"
                                                data-estador="<?php echo htmlspecialchars($row['Estado_Repartidor']); ?>"

                                            >
                                                Editar</button>
                                            <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs btn-eliminar-repartidor"
                                                data-codigo="<?php echo htmlspecialchars($row['Id_Persona']); ?>"
                                            >
                                             Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    
    <script>
        function confirmarCerrarSesion(event) {
            event.preventDefault();
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Tu sesión se cerrará y volverás al inicio.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, cerrar sesión",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "../Config/cerrar_cuenta.php";
                }
            });
        }
    </script>
    <script>
      $(document).ready(function () {
        $('#distritoR').select2({
          placeholder: "Selecciona un distrito",
          width: '100%'
        });
        $('#distritoEditR').select2({
          placeholder: "Selecciona un distrito",
          width: '100%'
        });
      });
    </script>
    <script src="../assets/js/validarRepartidor.js"></script>
    <script src="../assets/js/filtroRepartidor.js"></script>
</body>
</html>