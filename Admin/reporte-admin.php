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




$sql = $con->prepare("
    SELECT 
        -- p.Id_Pedido, // Si no lo usas, también puedes quitarlo
        -- p.Fecha_Pedido,
        -- p.Fecha_Entrega,
        -- p.Estado_Pedido,
        -- p.Direccion_Entrega,
        -- d.Descripcion_Distrito,
        pers.Nombre AS Nombre_Cliente,
        pers.Apellido AS Apellido_Cliente,
        prod.Nombre_Producto,
        dp.Cantidad_Pedido, 
        cat.Id_Categoria,
        cat.Descripcion_Categoria
    FROM tb_pedido p
    INNER JOIN tb_cliente c ON p.Id_Cliente = c.Id_Cliente
    INNER JOIN tb_persona pers ON c.Id_Cliente = pers.Id_Persona
    INNER JOIN tb_detalle_pedido dp ON p.Id_Pedido = dp.Id_Pedido
    INNER JOIN tb_producto prod ON dp.Id_Producto = prod.Id_Producto
    INNER JOIN tb_categoria cat ON prod.Id_Categoria = cat.Id_Categoria
    -- INNER JOIN tb_distrito d ON p.Id_Distrito = d.Id_Distrito
    ORDER BY p.Fecha_Pedido DESC
");

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

    header('Content-Type: application/json');
    if ($actualizado == 1 ) {
        echo json_encode(["status" => "success", "message" => "Pedido actualizado correctamente"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Error al actualizar el pedido"]);
        exit;
    }
}

// Consulta para BI -Bandera
$sql_bi = $con->prepare("
    SELECT 
        prod.Nombre_Producto,
        prod.Id_Categoria,
        cat.Descripcion_Categoria,
        COUNT(ped.Id_Pedido) AS Total_Pedidos
    FROM tb_pedido ped
    INNER JOIN tb_cliente cli ON ped.Id_Cliente = cli.Id_Cliente
    INNER JOIN tb_persona pers ON cli.Id_Cliente = pers.Id_Persona
    INNER JOIN tb_detalle_pedido dp ON ped.Id_Pedido = dp.Id_Pedido
    INNER JOIN tb_producto prod ON dp.Id_Producto = prod.Id_Producto
    INNER JOIN tb_categoria cat ON prod.Id_Categoria = cat.Id_Categoria
    GROUP BY prod.Nombre_Producto, prod.Id_Categoria, cat.Descripcion_Categoria
    ORDER BY Total_Pedidos DESC
");
$sql_bi->execute();
$bi_data = $sql_bi->fetchAll(PDO::FETCH_ASSOC);

// Unificar datos de pedidos y BI
$pedidos_bi = [];
foreach ($pedidos as $pedido) {
    $bi_row = null;
    foreach ($bi_data as $bi) {
        // Unir por Nombre_Producto e Id_Categoria
        if (
            strtolower($pedido['Nombre_Producto']) == strtolower($bi['Nombre_Producto']) &&
            $pedido['Id_Categoria'] == $bi['Id_Categoria']
        ) {
            $bi_row = $bi;
            break;
        }
    }
    $pedidos_bi[] = array_merge($pedido, $bi_row ?? [
        'Nombre_Producto' => '',
        'Id_Categoria' => '',
        'Descripcion_Categoria' => '',
        'Total_Pedidos' => ''
    ]);
}

// Obtener totales de productos
$sql_totales = $con->prepare("
    SELECT 
        prod.Nombre_Producto,
        SUM(dp.Cantidad_Pedido) AS Total_Vendido
    FROM tb_detalle_pedido dp
    INNER JOIN tb_producto prod ON dp.Id_Producto = prod.Id_Producto
    GROUP BY prod.Nombre_Producto
    ORDER BY Total_Vendido DESC
");
$sql_totales->execute();
$totales_productos = $sql_totales->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    <!-- SheetJS (xlsx) -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

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
        }
        .tabla-scroll {
            max-height: 500px;   /* Ajusta la altura máxima a tu gusto */
            overflow-y: auto;
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
        .choices__list--dropdown {
            margin-top: -1px !important; /* para eliminar separación entre select y dropdown */
        }
        .choices {
            margin-bottom: 0 !important; /* elimina el margen por defecto de Choices */
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
            <a href="../Admin/reporte-admin.php" class="flex items-center py-3 pl-6 nav-item active-nav-link">
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
                <h1 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">Reportes</h1>
                <div class="flex space-x-2">
                    <button
                        id="btnExportarExcel"
                        class="px-4 py-2 bg-green-400 border border-gray-300 rounded-md text-sm font-medium text-gray-600 hover:bg-green-300">
                        <i class="fas fa-download mr-2"></i>Exportar
                    </button>

                </div>
            </div>


            <!-- FILTRO DE FECHA Y ESTADO -->
            <!--
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-700">Fecha desde:</label>
        <input type="datetime-local" id="fechaDesde" name="fechaDesde" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
    </div>
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-700">Fecha hasta:</label>
        <input type="datetime-local" id="fechaHasta" name="fechaHasta" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
    </div>
</div>
-->

            <!-- FILTROS UNIFICADOS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <select id="filtroCategoria" class="px-4 py-2 border border-gray-300 rounded-md filtro-choice" multiple>
                    <option value="">Todas las categorías</option>
                    <?php
                    $categorias = [];
                    foreach ($pedidos as $row) {
                        $categorias[$row['Id_Categoria']] = $row['Descripcion_Categoria'];
                    }
                    foreach ($categorias as $id => $desc) {
                        echo '<option value="' . htmlspecialchars($desc) . '">' . htmlspecialchars($desc) . '</option>';
                    }
                    ?>
                </select>
                <select id="filtroProducto" class="px-4 py-2 border border-gray-300 rounded-md filtro-choice" multiple>
                    <option value="">Todos los productos</option>
                    <?php
                    $productos = array_unique(array_column($pedidos, 'Nombre_Producto'));
                    foreach ($productos as $p) {
                        echo '<option value="' . htmlspecialchars($p) . '">' . htmlspecialchars($p) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- TABLA UNIFICADA CON SCROLL -->
            <div class="overflow-x-auto tabla-scroll" style="max-height: 500px; overflow-y: auto;">
                <table class="min-w-full divide-y divide-gray-200 table" id="tablaUnificada">
                    <thead class="bg-gray-50 ">
                        <tr class="text-left">
                            <!-- <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Fecha del pedido</th> -->
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase cursor-pointer" id="ordenarCantidad">
                            Cantidad
                            <span id="iconoOrdenCantidad" class="ml-1">&#8597;</span>
                        </th>
                            <!-- <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Distrito (Usuario)</th> -->
                            <!-- <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Estado</th> -->
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        // Agrupar y sumar cantidades por producto y categoría
                        $resumen_productos = [];
                        foreach ($pedidos as $row) {
                            $producto = $row['Nombre_Producto'];
                            $categoria = $row['Descripcion_Categoria'];
                            $key = $categoria . '||' . $producto; // clave única por categoría y producto
                            if (!isset($resumen_productos[$key])) {
                                $resumen_productos[$key] = [
                                    'Descripcion_Categoria' => $categoria,
                                    'Nombre_Producto' => $producto,
                                    'Cantidad_Pedido' => 0
                                ];
                            }
                            $resumen_productos[$key]['Cantidad_Pedido'] += (int)($row['Cantidad_Pedido'] ?? 0);
                        }
                        foreach ($resumen_productos as $row): ?>
                            <tr>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['Descripcion_Categoria'] ?? '') ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['Nombre_Producto'] ?? '') ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($row['Cantidad_Pedido'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const filtroGenero = document.getElementById('filtroGenero');
    const filtroProducto = document.getElementById('filtroProducto');
    const filtroEstado = document.getElementById('filtroEstadoProducto');
    const filtroCategoria = document.getElementById('filtroCategoria');
    const tabla = document.getElementById('tablaBI').getElementsByTagName('tbody')[0];

    function filtrarTabla() {
        const genero = filtroGenero.value.toLowerCase();
        const producto = filtroProducto.value.toLowerCase();
        const estado = filtroEstado.value;
        const categoria = filtroCategoria.value.toLowerCase();

        Array.from(tabla.rows).forEach(row => {
            const celdaGenero = row.cells[0].textContent.toLowerCase();
            const celdaProducto = row.cells[1].textContent.toLowerCase();
            const celdaEstado = row.cells[2].getAttribute('data-estado');
            const celdaCategoria = row.cells[3].textContent.toLowerCase();

            let mostrar = true;
            if (genero && celdaGenero !== genero) mostrar = false;
            if (producto && celdaProducto !== producto) mostrar = false;
            if (estado && celdaEstado !== estado) mostrar = false;
            if (categoria && celdaCategoria !== categoria) mostrar = false;

            row.style.display = mostrar ? '' : 'none';
        });
    }

    filtroGenero.addEventListener('change', filtrarTabla);
    filtroProducto.addEventListener('change', filtrarTabla);
    filtroEstado.addEventListener('change', filtrarTabla);
    filtroCategoria.addEventListener('change', filtrarTabla);
});
</script>
<!-- SCRIPT DE FILTROS UNIFICADOS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabla = document.getElementById('tablaUnificada').getElementsByTagName('tbody')[0];

    function getSelectValues(select) {
        return Array.from(select.selectedOptions)
            .map(opt => opt.value)
            .filter(val => val !== "");
    }

    function filtrarTabla() {
        const productos = getSelectValues(document.getElementById('filtroProducto')).map(v => v.toLowerCase());
        const categorias = getSelectValues(document.getElementById('filtroCategoria')).map(v => v.toLowerCase());

        Array.from(tabla.rows).forEach(row => {
            const celdaCategoria = row.cells[0].textContent.toLowerCase();
            const celdaProducto = row.cells[1].textContent.toLowerCase();

            let mostrar = true;

            if (productos.length && !productos.includes(celdaProducto)) mostrar = false;
            if (categorias.length && !categorias.includes(celdaCategoria)) mostrar = false;

            row.style.display = mostrar ? '' : 'none';
        });
    }

    document.getElementById('filtroProducto').addEventListener('change', filtrarTabla);
    document.getElementById('filtroCategoria').addEventListener('change', filtrarTabla);
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.filtro-choice').forEach(function(select) {
        new Choices(select, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            removeItemButton: true, // Esto agrega la "x" para quitar seleccionados
            placeholder: true
        });
    });
});
</script>
<script>
document.getElementById('btnExportarExcel').addEventListener('click', function () {
    // Selecciona la tabla - bandera 
    const tabla = document.getElementById('tablaUnificada');
    // Clona la tabla para no exportar filas ocultas
    const tablaClon = tabla.cloneNode(true);
    // Elimina filas ocultas (por filtros)
    Array.from(tablaClon.tBodies[0].rows).forEach(row => {
        if (row.style.display === 'none') row.remove();
    });
    // Convierte la tabla a hoja de cálculo
    const wb = XLSX.utils.table_to_book(tablaClon, {sheet: "Reporte"});
    // Descarga el archivo Excel sheetjs
    XLSX.writeFile(wb, 'reporte_pedidos.xlsx');
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let asc = true;
    document.getElementById('ordenarCantidad').addEventListener('click', function () {
        const tabla = document.getElementById('tablaUnificada').getElementsByTagName('tbody')[0];
        const filas = Array.from(tabla.rows).filter(row => row.style.display !== 'none');
        filas.sort(function(a, b) {
            const valA = parseInt(a.cells[2].textContent) || 0;
            const valB = parseInt(b.cells[2].textContent) || 0;
            return asc ? valA - valB : valB - valA;
        });
        filas.forEach(row => tabla.appendChild(row));
        asc = !asc;
        document.getElementById('iconoOrdenCantidad').textContent = asc ? '↑' : '↓';
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Selecciona el enlace de Reportes
    const reportesLink = document.querySelector('.active-nav-link');
    reportesLink.addEventListener('click', function (e) {
        e.preventDefault();

        // Sumar cantidades por producto
        const tabla = document.getElementById('tablaUnificada').getElementsByTagName('tbody')[0];
        const resumen = {};

        Array.from(tabla.rows).forEach(row => {
            const producto = row.cells[1].textContent.trim();
            const cantidad = parseInt(row.cells[2].textContent) || 0;
            if (resumen[producto]) {
                resumen[producto] += cantidad;
            } else {
                resumen[producto] = cantidad;
            }
        });

        // Mostrar el resultado en un alert o en consola
        let mensaje = 'Total vendido por producto:\n\n';
        for (const [producto, total] of Object.entries(resumen)) {
            mensaje += `${producto}: ${total}\n`;
        }
        alert(mensaje);
        // Si quieres mostrarlo en consola, usa: console.log(mensaje);
    });
});
</script>
</body>

</html>