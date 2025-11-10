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
        $sql->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $sql->execute();
        $datos_persona = $sql->fetch(PDO::FETCH_ASSOC);
        $nombre = $datos_persona['Nombre'];
    }
}

// Obtener repartidores disponibles
$sql_rep = $con->prepare("SELECT p.Id_Persona, p.Nombre, p.Apellido
    FROM tb_repartidor r
    INNER JOIN tb_usuario u ON r.Id_Repartidor = u.Id_Usuario
    INNER JOIN tb_persona p ON u.Id_Usuario = p.Id_Persona
    WHERE r.Estado_Repartidor = 1 AND u.Estado_Usuario = 1");
$sql_rep->execute();
$repartidores_disponibles = $sql_rep->fetchAll(PDO::FETCH_ASSOC);



// Llamamos todos los datos de la tabla tb_pedido




$sql = $con->prepare("SELECT 
    p.Id_Pedido,
    p.Id_Distrito,
    r.Id_Repartidor,
    p.Fecha_Pedido,
    CONCAT(cpers.Nombre, ' ', cpers.Apellido) AS Nombre_Cliente,
    CONCAT(rpers.Nombre, ' ', rpers.Apellido) AS Nombre_Repartidor,
    p.Direccion_Entrega,
    d.Descripcion_Distrito,
    p.Total_Pedido,
    p.Estado_Pedido,
    p.Fecha_Entrega
FROM tb_pedido p
INNER JOIN tb_cliente c ON p.Id_Cliente = c.Id_Cliente
INNER JOIN tb_persona cpers ON c.Id_Cliente = cpers.Id_Persona
LEFT JOIN tb_repartidor r ON p.Id_Repartidor = r.Id_Repartidor
LEFT JOIN tb_persona rpers ON r.Id_Repartidor = rpers.Id_Persona
INNER JOIN tb_distrito d ON p.Id_Distrito = d.Id_Distrito
ORDER BY p.Fecha_Pedido DESC");

$sql->execute();
$pedidos = $sql->fetchAll(PDO::FETCH_ASSOC);


// Verificar si se envió el formulario de actualización de producto
if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
   
    // Obtener el código del producto
    $codigo = $_POST['codigoEditarPe'];


    $id_repartidor = isset($_POST['repartidorEdit']) ? $_POST['repartidorEdit'] : null;

    $estado = $_POST['estadoEditarPe'];
    $fecha_entrega = $_POST['fechaEntregaEditarPe'];

    $estado_valor = (int) $estado;

    $actualizado = 0;


    if($id_repartidor == null){

        // Preparar la consulta SQL de actualización
        $sql_update = $con->prepare("UPDATE tb_pedido SET Estado_Pedido=?, Fecha_Entrega=? WHERE Id_Pedido=?");

        // Vincular parámetros y ejecutar la consulta
        $sql_update->bindParam(1, $estado, PDO::PARAM_INT);
        $sql_update->bindParam(2, $fecha_entrega, PDO::PARAM_STR);
        $sql_update->bindParam(3, $codigo, PDO::PARAM_INT);
        $sql_update->execute();
        $actualizado = 1;
    }else if($id_repartidor != null){

        // Preparar la consulta SQL de actualización
        $sql_update = $con->prepare("UPDATE tb_pedido SET Estado_Pedido=?, Fecha_Entrega=?, Id_Repartidor=? WHERE Id_Pedido=?");

        // Vincular parámetros y ejecutar la consulta
        $sql_update->bindParam(1, $estado, PDO::PARAM_INT);
        $sql_update->bindParam(2, $fecha_entrega, PDO::PARAM_STR);
        $sql_update->bindParam(3, $id_repartidor, PDO::PARAM_INT);
        $sql_update->bindParam(4, $codigo, PDO::PARAM_INT);
        $sql_update->execute();

            //Actualizar la disponibilidad del repartidor
        $sql_update_repartidor = $con->prepare("UPDATE tb_repartidor SET Estado_Repartidor = 0 WHERE Id_Repartidor = ?");
        $sql_update_repartidor->bindParam(1, $id_repartidor, PDO::PARAM_INT);
        $sql_update_repartidor->execute();
        $actualizado = 1;
    }


    if($estado_valor == 2){
        //Recuperar el id del repartidor de la tabla tb_pedido
        $sql_repartidor = $con->prepare("SELECT Id_Repartidor FROM tb_pedido WHERE Id_Pedido = ?");
        $sql_repartidor->bindParam(1, $codigo, PDO::PARAM_INT);
        $sql_repartidor->execute();
        $id_repartidor = $sql_repartidor->fetchColumn();

         // Preparar la consulta SQL de actualización
         $sql_update_repartidor = $con->prepare("UPDATE tb_repartidor SET Estado_Repartidor = 1 WHERE Id_Repartidor = ?");
         $sql_update_repartidor->bindParam(1, $id_repartidor, PDO::PARAM_INT);
         $sql_update_repartidor->execute();
    }

    // Verificar si la actualización fue exitosa
    if ($actualizado == 1 ) {
         error_log("Entró al bloque de actualización de pedido. Código: $codigo");
        // Obtener datos del cliente del pedido actualizado
        $sql_cliente = $con->prepare("SELECT u.Correo, p.Nombre, p.Apellido 
            FROM tb_pedido pe
            INNER JOIN tb_cliente c ON pe.Id_Cliente = c.Id_Cliente
            INNER JOIN tb_persona p ON c.Id_Cliente = p.Id_Persona
            INNER JOIN tb_usuario u ON u.Id_Usuario = p.Id_Persona
            WHERE pe.Id_Pedido = ?");
        $sql_cliente->execute([$codigo]);
        $cliente = $sql_cliente->fetch(PDO::FETCH_ASSOC);
        error_log("Datos del cliente: " . print_r($cliente, true));

        // Solo si hay correo
        if ($cliente && !empty($cliente['Correo'])) {
            // Llama a correo.php usando file_get_contents o cURL
            $estado_texto = '';
            switch ($estado) {
                case 0: $estado_texto = 'Pendiente'; break;
                case 1: $estado_texto = 'En proceso'; break;
                case 2: $estado_texto = 'Entregado'; break;
                case 3: $estado_texto = 'Cancelado'; break;
                default: $estado_texto = 'Actualizado'; break;
            }

            $mensaje = "El estado de su pedido ha sido actualizado a: $estado_texto.";

            // Usando file_get_contents (requiere allow_url_fopen = On)
            $postdata = http_build_query([
                'id_pedido' => $codigo, // o el nombre de tu variable de ID de pedido
                'nombre' => $cliente['Nombre'],
                'apellido' => $cliente['Apellido'],
                'email' => $cliente['Correo'],
                //'telefono' => '', // Si tienes el teléfono, agrégalo aquí
                'tipo' => 'estado',
                'estado' => $estado_texto // Por ejemplo: "En proceso"
            ]);
            $opts = [
                'http' => [
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                ]
            ];
            // Construye la URL completa a correo.php
            $url = 'http://localhost/Tanta/correo.php';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if ($response === false) {
                error_log("Error al llamar a correo.php con cURL: " . curl_error($ch));
            } else {
                error_log("Respuesta de correo.php (cURL): " . $response);
            }
            curl_close($ch);
        }
    }

    header('Content-Type: application/json');
    if ($actualizado == 1 ) {
        echo json_encode(["status" => "success", "message" => "Pedido actualizado correctamente"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Error al actualizar el pedido"]);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Pedidos</title>
    <meta name="author" content="David Grzyb">
    <meta name="description" content="">
   

    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
            background: #e11d48;
        }

        /* Estados de pedidos */
        .status-pending {
            background: #fee28a;
            color: #ca9a04;
        }

        .status-processing {
            background: #bfd7fe;
            color: #2570eb;
        }

        .status-shipped {
            background: #ebd5ff;
            color: #9133ea;
        }

        .status-delivered {
            background: #bbf7d1;
            color: #16a34a;
        }

        .status-cancelled {
            background: #fecaca;
            color: #dc2626;
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
            filter: blur(80px);
            /* Efecto difuminado intenso */
            z-index: 0;
            /* <-- Añade esta línea */
        }
    </style>
    <style>
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
        #editPedidoModal>div {
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

    <!-- Sidebar - BARRA LATERAL -->
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
                        <path d="m12 14 4-4" />
                        <path d="M3.34 19a10 10 0 1 1 17.32 0" />
                    </svg>
                </span>
                Dashboard
            </a>
            <a href="../Admin/productos-admin.php" class="flex items-center py-3 pl-6 nav-item">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-package-icon lucide-package">
                        <path
                            d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z" />
                        <path d="M12 22V12" />
                        <polyline points="3.29 7 12 12 20.71 7" />
                        <path d="m7.5 4.27 9 5.15" />
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
            <a href="../Admin/pedidos-admin.php" class="flex items-center py-3 pl-6 nav-item active-nav-link">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-shopping-bag-icon lucide-shopping-bag">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                        <path d="M3 6h18" />
                        <path d="M16 10a4 4 0 0 1-8 0" />
                    </svg>
                </span>
                Pedidos
            </a>
            <a href="../Admin/clientes-admin.php" class="flex items-center py-3 pl-6 nav-item">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-file-user-icon lucide-file-user">
                        <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                        <path d="M15 18a3 3 0 1 0-6 0" />
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z" />
                        <circle cx="12" cy="13" r="2" />
                    </svg>
                </span>
                Clientes
            </a>
            <a href="../Admin/repartidores-admin.php" class="flex items-center py-3 pl-6 nav-item">
                <span class="mr-3" style="display:inline-flex;align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-truck-icon lucide-truck">
                        <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2" />
                        <path d="M15 18H9" />
                        <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14" />
                        <circle cx="17" cy="18" r="2" />
                        <circle cx="7" cy="18" r="2" />
                    </svg>
                </span>
                Repartidores
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden relative z-[1]">
        <!-- Header R - ENCABEZADO -->
        <header class="border-b border-gray-200 py-3 px-6 flex items-center justify-between">
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
            <!-- ENCABEZADO 2 Y ACCIONES -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h1 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">Gestión de Pedidos</h1>
                <div class="flex space-x-2">
                    <button
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-download mr-2"></i>Exportar
                    </button>
                    <button class="px-4 py-2 btn-primary rounded-md text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>Nuevo Pedido
                    </button>
                </div>
            </div>

            <!-- FILTROS -->
             <!--   FILTROS DE BÚSQUEDA -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <input type="text" id="buscarCodigo" placeholder="Buscar por N° Pedido"
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <input type="text" id="buscarCliente" placeholder="Buscar por Cliente"
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <input type="text" id="buscarRepartidor" placeholder="Buscar por Repartidor"
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <input type="text" id="buscarDireccion" placeholder="Buscar por Dirección"
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>

            <!-- FILTRO DE FECHA Y ESTADO -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Fecha desde:</label>
                    <input type="datetime-local" id="fechaDesde" name="fechaDesde" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Fecha hasta:</label>
                    <input type="datetime-local" id="fechaHasta" name="fechaHasta" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>
                <div>
                    <label for="filtroEstado" class="block mb-2 text-sm font-medium text-gray-700">Estado:</label>
                    <select id="filtroEstado" name="filtroEstado"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <option value="4">Todos los estados</option>
                        <option value="0">Pendiente</option>
                        <option value="1">En proceso</option>
                        <option value="2">Entregado</option>
                        <option value="3">Cancelado</option>
                    </select>
                </div>
            </div>

            <!-- Edit Pedido -->
            <div id="editPedidoModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Editar Pedido</h2>
                    <form id="formEditarPedido" class="grid grid-cols-2 gap-4" method="POST" enctype="multipart/form-data">
                        <!-- Código (deshabilitado) -->
                        <div class="col-span-2">
                            <label for="codigoEditarPe" class="block mb-1 text-sm font-semibold text-gray-700">Código:</label>
                            <input type="text" id="codigoEditarPe" name="codigoEditarPe"
                                class="w-full px-4 py-2 border border-gray-300 rounded text-gray-500" readonly>
                        </div>
                        <!-- Fecha del pedido-->
                        <div class="col-span-2">
                            <label for="fechaEditPe" class="block mb-1 text-sm font-semibold text-gray-700">Fecha Realizada:</label>
                            <input type="datetime-local" id="fechaEditPe" name="fechaEditPe" placeholder="Fecha realizada del pedido"
                                class="w-full px-4 py-2 border border-gray-300 rounded text-gray-500" readonly>
                            <span id="validaFechaEditPe" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Nombre del cliente -->
                        <div>
                            <label for="nombreClienteEditPe" class="block mb-1 text-sm font-semibold text-gray-700">Nombre Cliente:</label>
                            <input type="text" id="nombreClienteEditPe" name="nombreClienteEditPe" placeholder="Nombre del cliente"
                                class="w-full px-4 py-2 border border-gray-300 rounded text-gray-500" readonly>
                            <span id="validaNombreClienteEditPe" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Nombre del repartidor -->
                        <div>
                            <label for="repartidorEdit" class="block mb-1 text-sm font-semibold text-gray-700">Repartidor: </label>
                            <select id="repartidorEdit" name="repartidorEdit" class="w-full bg-gray-200 h-11 rounded-lg px-3">
                                <option value="">Selecciona un repartidor</option>
                                <?php foreach ($repartidores_disponibles as $rep): ?>
                                    <option value="<?= htmlspecialchars($rep['Id_Persona']) ?>">
                                        <?= htmlspecialchars($rep['Nombre'] . ' ' . $rep['Apellido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span id="validaNombreRepartidorEditPe" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Dirección -->
                        <div>
                            <label for="direccionEditarPe" class="block mb-1 text-sm font-semibold text-gray-700">Dirección:</label>
                            <input type="text" id="direccionEditarPe" name="direccionEditarPe" placeholder="Dirección de la entrega"
                                class="w-full px-4 py-2 border border-gray-300 rounded text-gray-500" readonly>
                            <span id="validaDireccionEditarPe" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Distrito -->
                        <div>
                            <label for="distritoEditarPe" class="block mb-1 text-sm font-semibold text-gray-700">Distrito:</label>
                            <select id="distritoEditarPe" name="distritoEditarPe" class="w-full px-4 py-2 border border-gray-300 rounded" disabled required>
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
                            <span id="validaDistritoEditarPe" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Total Pedido -->
                        <div class="col-span-2">
                            <label for="totalEditarPe" class="block mb-1 text-sm font-semibold text-gray-700">Total:</label>
                            <input type="text" id="totalEditarPe" name="totalEditarPe" placeholder="Total del pedido"
                                class="w-full px-4 py-2 border border-gray-300 rounded text-gray-500" readonly>
                            <span id="validaTotalEditarPe" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Estado Pedido -->
                        <div>
                            <label for="estadoEditarPe" class="block mb-1 text-sm font-semibold text-gray-700">Estado:</label>
                            <select id="estadoEditarPe" name="estadoEditarPe" class="w-full px-4 py-2 border border-gray-300 rounded" required>
                                <option value="">Selecciona un Estado</option>
                                <option value="0">Pendiente</option>
                                <option value="1">En proceso</option>
                                <option value="2">Entregado</option>
                                <option value="3">Cancelado</option>
                            </select>
                            <span id="validaEstadoEditarPe" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Fecha de entrega del pedido -->
                        <div>
                            <label for="fechaEntregaEditarPe" class="block mb-1 text-sm font-semibold text-gray-700">Fecha de Entrega:</label>
                            <input type="datetime-local" id="fechaEntregaEditarPe" name="fechaEntregaEditarPe" class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaFechaEntregaEditarPe" class="text-red-500 text-sm mt-1 block"></span>
                        </div>

                        <!-- Botones -->
                        <div class="col-span-2 flex justify-end gap-2">
                            <button type="button"
                                onclick="document.getElementById('editPedidoModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
                        </div>
                    </form>
                    <button onclick="document.getElementById('editPedidoModal').classList.add('hidden')"
                        class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto tabla-scroll" style="max-height: 500px; overflow-y: auto;">
                    <table class="min-w-full divide-y divide-gray-200 table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Pedido</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Repartidor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distrito</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            
                            foreach ($pedidos as $pedido): ?>
                                <tr class="hover:bg-gray-50" data-estadoo="<?= htmlspecialchars($pedido['Estado_Pedido']) ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #<?= str_pad($pedido['Id_Pedido'], 6, '0', STR_PAD_LEFT) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('d/m/Y H:i', strtotime($pedido['Fecha_Pedido'])) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($pedido['Nombre_Cliente']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($pedido['Nombre_Repartidor'] ?? 'Sin asignar') ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?= htmlspecialchars($pedido['Direccion_Entrega']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($pedido['Descripcion_Distrito']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/
                                        <?= number_format($pedido['Total_Pedido'], 2) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $estado = '';
                                        $clase = '';
                                        switch ($pedido['Estado_Pedido']) {
                                            case 0:
                                                $estado = 'Pendiente';
                                                $clase = 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 1:
                                                $estado = 'En proceso';
                                                $clase = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 2:
                                                $estado = 'Entregado';
                                                $clase = 'bg-green-100 text-green-800';
                                                break;
                                            case 3:
                                                $estado = 'Cancelado';
                                                $clase = 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                $estado = 'Desconocido';
                                                $clase = 'bg-gray-100 text-gray-800';
                                        }
                                        ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $clase ?>">
                                            <?= $estado ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button
                                                class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs">
                                                <i class="fas fa-eye mr-1"></i>Ver
                                            </button>
                                            <button
                                                onclick="document.getElementById('editPedidoModal').classList.remove('hidden')"
                                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs btn-editar-pedido"
                                                data-codigo="<?= $pedido['Id_Pedido'] ?>"
                                                data-fecha="<?= $pedido['Fecha_Pedido'] ?>"
                                                data-cliente="<?= $pedido['Nombre_Cliente'] ?>"
                                                data-repartidor="<?= htmlspecialchars($pedido['Nombre_Repartidor'] ?? 'Sin asignar') ?>"
                                                data-direccion="<?= $pedido['Direccion_Entrega'] ?>"
                                                data-idrepartidor="<?= $pedido['Id_Repartidor'] ?>"
                                                data-distrito="<?= $pedido['Id_Distrito'] ?>"
                                                data-total="<?= $pedido['Total_Pedido'] ?>"
                                                data-estado="<?= $pedido['Estado_Pedido'] ?>"
                                                data-fechaentrega="<?= $pedido['Fecha_Entrega'] ?>"
                                                >
                                                <!-- Icono de lápiz para editar -->
                                                <i class="fas fa-pencil-alt mr-1"></i>
                                                Editar
                                            </button>
                                        </div>
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
        $(document).ready(function() {
            $('#repartidorEdit').select2({
                placeholder: "Selecciona un repartidor",
                width: '100%'
            });
        });
</script>
    <script src="../assets/js/validarPedidos.js"></script>
    <script src="../assets/js/filtroPedidos.js"></script>
</body>

</html>