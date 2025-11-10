<?php
session_start();
require '../Config/config.php';
require '../database.php';

$db = new Database();
$con = $db->conectar();

// Redirección según tipo de usuario
// Si ya hay una sesión iniciada, redirige al usuario según su tipo
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

        if (isset($con)) {
            $stmt = $con->query("SELECT * FROM tb_producto");
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            echo "Error: La conexión no se ha establecido.";
        }

        //Lógica de la alerta de stock bajo
        $alerta_stock_bajo = false;
        foreach ($productos as $prod) {
            if ($prod['Disponibilidad'] == 0 || $prod['Disponibilidad'] == 2) {
                $alerta_stock_bajo = true;
                break;
            }
        }
    }
}

// Verificamos si se envió el formulario de agregar producto
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    // Ruta donde se guardarán las imágenes
    $directorioDestino = "../Admin/images/";
    $foto = '';//inicializamos la variable foto como vacía

    // Verificar si hay una imagen subida
    // y si no hay errores en la carga
    if (isset($_FILES['imagenP']) && $_FILES['imagenP']['error'] === UPLOAD_ERR_OK) {
        $fotoNombre = basename($_FILES['imagenP']['name']);//nombre de la imagen de acuerdo al nombre del archivo
        $rutaDestino = $directorioDestino . $fotoNombre;//ruta de la imagen
        $rutaBD = "images/" . $fotoNombre;//ruta dentro del archivo Admin
        // Asegurar que la carpeta exista
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }
        // Mover el archivo a la carpeta destino
        if (move_uploaded_file($_FILES['imagenP']['tmp_name'], $rutaDestino)) {
            $foto = $rutaBD; // Guardamos la ruta relativa en la BD
        } else {
            $foto = ''; // En caso de error, dejamos vacío
        }
    } else {
        $foto = ''; // Si no hay imagen, se deja vacío
    }

    // Preparar la consulta SQL de inserción
    $sql_insert = $con->prepare("INSERT INTO tb_producto (Id_Categoria, Nombre_Producto, Descripcion_Producto, Stock, Stock_Min, Precio_Actual, Imagen)
    VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Vincular parámetros y ejecutar la consultaS
    $categoria = $_POST['categoriaP'];
    $nombre_producto = $_POST['nombreP'];
    $descripcion = $_POST['descripcionP'];
    $stock = $_POST['stockP'];
    $stockMin = $_POST['stockMinP'];
    $precio = $_POST['precioP'];


    $sql_insert->bindParam(1, $categoria, PDO::PARAM_INT);
    $sql_insert->bindParam(2, $nombre_producto, PDO::PARAM_STR);
    $sql_insert->bindParam(3, $descripcion, PDO::PARAM_STR);
    $sql_insert->bindParam(4, $stock, PDO::PARAM_INT);
    $sql_insert->bindParam(5, $stockMin, PDO::PARAM_INT);
    $sql_insert->bindParam(6, $precio, PDO::PARAM_STR);
    $sql_insert->bindParam(7, $foto, PDO::PARAM_STR);

    header('Content-Type: application/json');
    if ($sql_insert->execute()) {
        echo json_encode(["status" => "success", "message" => "Producto agregado correctamente"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Error al agregar el producto"]);
        exit;
    }
}

// Verificar si se envió el formulario de actualización de producto
if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
    // Ruta donde se guardarán las imágenes
    $directorioDestino = "../Admin/images/";
    $foto = '';//inicializamos la variable foto como vacía

    // Obtener el código del producto
    $codigo = $_POST['codigoEditarP'];
    $nombre = $_POST['nombreEditarP'];
    $stock = $_POST['stockEditarP'];
    $stockMin = $_POST['stockMinEditarP'];
    $precio = $_POST['precioEditarP'];
    $categoria = $_POST['categoriaEditarP'];
    $descripcion = $_POST['descripcionEditarP'];
    $disponibilidad = $_POST['disponibilidadP'];
    $estado = $_POST['estadoEditarP'];

    $stock_valor = (float) $stock;
    $stockMin_valor = (float) $stockMin;

    if ($stock_valor == 0) {
        $disponibilidad = 0;//Agotado
    } else if ($stock_valor <= $stockMin_valor && $stockMin_valor != 0) {
        $disponibilidad = 2;//Escaso
    } else {
        $disponibilidad = 1;//Disponible
    }

    if ($estado == '1') {
        $estado = 1; // Convertir a 1 si está activo
    } else {
        $estado = 0; // Convertir a 0 si no está activo
    }

    // Consulta para obtener la imagen actual del producto
    $sql_get_image = $con->prepare("SELECT Imagen FROM tb_producto WHERE Id_Producto = ?");
    $sql_get_image->execute([$codigo]);
    $producto = $sql_get_image->fetch(PDO::FETCH_ASSOC);
    $imagenActual = $producto['Imagen']; // Imagen actual en la BD

    // Manejo de la nueva imagen
    if (!empty($_FILES['imagenEditarP']['name']) && $_FILES['imagenEditarP']['error'] === UPLOAD_ERR_OK) {
        $fotoNombre = basename($_FILES['imagenEditarP']['name']);
        $rutaDestino = $directorioDestino . $fotoNombre;
        $rutaBD = "images/" . $fotoNombre;

        // Asegurar que la carpeta de destino existe
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        if (move_uploaded_file($_FILES['imagenEditarP']['tmp_name'], $rutaDestino)) {
            // Verificar si se debe eliminar la imagen anterior
            if (!empty($imagenActual) && file_exists("../Admin/" . $imagenActual) && $imagenActual !== $rutaBD) {
                unlink("../Admin/" . $imagenActual);
            }
            $foto = $rutaBD; // Guardamos la nueva ruta de imagen en la BD
        } else {
            //echo json_encode(["status" => "error", "message" => "Error al mover la imagen"]);
        }
    } else {
        $foto = $imagenActual; // No se subió nueva imagen, se mantiene la anterior
    }

    // Preparar la consulta SQL de actualización
    $sql_update = $con->prepare("UPDATE tb_producto SET Id_Categoria=?, Nombre_Producto=?, Descripcion_Producto=?, Stock=?, Stock_Min =?, Precio_Actual=?, Imagen=?, Disponibilidad =?, Estado_Producto =? WHERE Id_Producto=?");

    // Vincular parámetros y ejecutar la consulta
    $sql_update->bindParam(1, $categoria, PDO::PARAM_INT);
    $sql_update->bindParam(2, $nombre, PDO::PARAM_STR);
    $sql_update->bindParam(3, $descripcion, PDO::PARAM_STR);
    $sql_update->bindParam(4, $stock, PDO::PARAM_INT);
    $sql_update->bindParam(5, $stockMin, PDO::PARAM_INT);
    $sql_update->bindParam(6, $precio, PDO::PARAM_STR);
    $sql_update->bindParam(7, $foto, PDO::PARAM_STR);
    $sql_update->bindParam(8, $disponibilidad, PDO::PARAM_INT);
    $sql_update->bindParam(9, $estado, PDO::PARAM_INT);
    $sql_update->bindParam(10, $codigo, PDO::PARAM_INT);

    header('Content-Type: application/json');
    if ($sql_update->execute()) {
        echo json_encode(["status" => "success", "message" => "Producto actualizado correctamente"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Error al actualizar el producto"]);
        exit;
    }
}

// Verificar si se envió la solicitud para eliminar un producto
if (isset($_POST['elimina']) && $_POST['elimina'] === 'eliminar' && isset($_POST['codigo'])) {
    // Preparar la consulta SQL de eliminación
    $sql_delete = $con->prepare("UPDATE tb_producto SET Estado_Producto = ? WHERE Id_Producto = ?;");

    // Vincular el código del producto y ejecutar la consulta de eliminación
    $codigo = $_POST['codigo'];
    $estado = 0;

    $sql_delete->bindParam(1, $estado, PDO::PARAM_INT);
    $sql_delete->bindParam(2, $codigo, PDO::PARAM_INT);

    header('Content-Type: application/json');
    if ($sql_delete->execute()) {
        echo json_encode(["status" => "success", "message" => "Producto eliminado correctamente"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Error al eliminar el producto"]);
        exit;
    }
    exit(); // Detener la ejecución del script después de manejar la solicitud de eliminación
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Productos</title>
    <meta name="author" content="David Grzyb">
    <meta name="description" content="">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #2655ea;
            --light-bg: #F1F5FF;
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

        .btn-outline {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: #f9fafb;
        }

        .status-available {
            background: #bbf7d1;
            color: #16a34a;
        }

        .status-scarce {
            background: #fef3c7;
            color: #92400e;
        }

        .status-out {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-active {
            background: #bbf7d1;
            color: #16a34a;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .img-thumbnail {
            padding: 0.25rem;
            background-color: #fff;
            border: 1px solid #dddfeb;
            border-radius: 0.35rem;
            max-width: 100%;
            height: auto;
        }

        #addProductModal>div,
        #editProductModal>div {
            max-height: 80vh;
            overflow-y: auto;
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

        .choices__inner .choices__list--multiple .choices__item,
        .choices__list--multiple .choices__item {
            background-color: #00635D !important;
            color: #fff !important;
            border: none !important;
        }
        .tabla-scroll {
            max-height: 400px;   /* Ajusta la altura máxima a tu gusto */
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
                        <path d="m12 14 4-4" />
                        <path d="M3.34 19a10 10 0 1 1 17.32 0" />
                    </svg>
                </span>
                Dashboard
            </a>
            <a href="../Admin/productos-admin.php" class="flex items-center py-3 pl-6 nav-item active-nav-link">
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

        <!-- AlpineJS -->
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
        <!-- Font Awesome -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"
            integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Header and Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h1 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">Listado de Productos</h1>
                <button onclick="document.getElementById('addProductModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-md text-sm font-medium">
                    <i class="fas fa-plus mr-2"></i>Agregar Producto
                </button>
            </div>

            <!-- Search and Filters -->
            <div class="mb-4">
                <div class="flex flex-col md:flex-row gap-6">
                    <input type="text" id="buscarCodigo" placeholder="Buscar por Código"
                        class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <input type="text" id="buscarNombre" placeholder="Buscar por Nombre"
                        class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
            <div class="mb-6">
                <div class="flex flex-col md:flex-row gap-6 pt-2">
                    <div class="flex-1">
                        <label for="filtroCategoria" class="block mb-2 text-gray-700 font-semibold text-sm">Filtrar por
                            Categoría:</label>
                        <select id="filtroCategoria" multiple
                            class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="0">Todas</option>
                            <option value="1">Bollería</option>
                            <option value="2">Dulces</option>
                            <option value="3">Especiales</option>
                            <option value="4">Pan artesanal</option>
                            <option value="5">Pan saludable</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="filtroDisponibilidad" class="block mb-2 text-gray-700 font-semibold text-sm">Filtrar
                            por Disponibilidad:</label>
                        <select id="filtroDisponibilidad" multiple
                            class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="3">Todas</option>
                            <option value="1">Disponible</option>
                            <option value="2">Escaso</option>
                            <option value="0">Agotado</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="filtroEstado" class="block mb-2 text-gray-700 font-semibold text-sm">Filtrar
                            por Estado:</label>
                        <select id="filtroEstado" multiple
                            class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="2">Todas</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>                            
                        </select>
                    </div>                    
                </div>
            </div>

            <!-- Add Product Modal -->
            <div id="addProductModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Agregar Nuevo Producto</h2>
                    <form id="formAgregarProducto" class="grid grid-cols-2 gap-4">
                        <!-- Código (deshabilitado) -->
                        <div class="col-span-2">
                            <label for="codigoP" class="block mb-1 text-sm font-semibold text-gray-700">Código:</label>
                            <input type="text" id="codigoP" name="codigoP" disabled value=""
                                class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-500">
                        </div>
                        <!-- Nombre -->
                        <div class="col-span-2">
                            <label for="nombreP" class="block mb-1 text-sm font-semibold text-gray-700">Nombre:</label>
                            <input type="text" id="nombreP" name="nombreP" placeholder="Nombre del producto"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaNombreP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Stock - Actual -->
                        <div>
                            <label for="stockP" class="block mb-1 text-sm font-semibold text-gray-700">Stock
                                Actual:</label>
                            <input type="text" id="stockP" name="stockP" placeholder="Cantidad en stock"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaStockP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Stock - Mínimo -->
                        <div>
                            <label for="stockMinP" class="block mb-1 text-sm font-semibold text-gray-700">Stock
                                Minimo:</label>
                            <input type="text" id="stockMinP" name="stockMinP" placeholder="Cantidad en stock minimo"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaStockMinP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Precio -->
                        <div>
                            <label for="precioP" class="block mb-1 text-sm font-semibold text-gray-700">Precio:</label>
                            <input type="text" id="precioP" name="precioP" placeholder="Precio del producto"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaPrecioP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Categoria -->
                        <div>
                            <label for="categoriaP"
                                class="block mb-1 text-sm font-semibold text-gray-700">Categoría:</label>
                            <select id="categoriaP" name="categoriaP"
                                class="w-full px-4 py-2 border border-gray-300 rounded" required>
                                <option value="">Seleccione categoría</option>
                                <option value="1">Bollería</option>
                                <option value="2">Dulces</option>
                                <option value="3">Especiales</option>
                                <option value="4">Pan artesanal</option>
                                <option value="5">Pan saludable</option>
                            </select>
                            <span id="validaCategoriaP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Descripción -->
                        <div class="col-span-2">
                            <label for="descripcionP"
                                class="block mb-1 text-sm font-semibold text-gray-700">Descripción:</label>
                            <textarea id="descripcionP" name="descripcionP" placeholder="Descripción del producto"
                                class="w-full px-4 py-2 border border-gray-300 rounded  resize-none"></textarea>
                            <span id="validaDescripcionP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Imagen -->
                        <div>
                            <label for="imagenP" class="block mb-1 text-sm font-semibold text-gray-700">Imagen
                                📂:</label>
                            <input type="file" id="imagenP" name="imagenP"
                                class="w-full px-4 py-2 border border-gray-300 rounded" onchange="preview(event)">
                            <img class="img-thumbnail" id="img-preview">
                        </div>
                        <!-- Botones -->
                        <div class="col-span-2 flex justify-end gap-2">
                            <button type="button"
                                onclick="document.getElementById('addProductModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
                        </div>
                    </form>
                    <button onclick="document.getElementById('addProductModal').classList.add('hidden')"
                        class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div id="editProductModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Editar Producto</h2>
                    <form id="formEditarProducto" class="grid grid-cols-2 gap-4" method="POST" enctype="multipart/form-data">
                        <!-- Código (deshabilitado) -->
                        <div class="col-span-2">
                            <label for="codigoEditarP" class="block mb-1 text-sm font-semibold text-gray-700">Código:</label>
                            <input type="text" id="codigoEditarP" name="codigoEditarP" readonly value=""
                                class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100 text-gray-500">
                        </div>
                        <!-- Nombre -->
                        <div class="col-span-2">
                            <label for="nombreEditarP" class="block mb-1 text-sm font-semibold text-gray-700">Nombre:</label>
                            <input type="text" id="nombreEditarP" name="nombreEditarP" placeholder="Nombre del producto"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaNombreEditarP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Stock - Actual -->
                        <div>
                            <label for="stockEditarP" class="block mb-1 text-sm font-semibold text-gray-700">Stock Actual:</label>
                            <input type="text" id="stockEditarP" name="stockEditarP" placeholder="Cantidad en stock"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaStockEditarP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Stock - Mínimo -->
                        <div>
                            <label for="stockMinEditarP" class="block mb-1 text-sm font-semibold text-gray-700">Stock Minimo:</label>
                            <input type="text" id="stockMinEditarP" name="stockMinEditarP" placeholder="Cantidad en stock minimo"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaStockMinEditarP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Precio -->
                        <div>
                            <label for="precioEditarP" class="block mb-1 text-sm font-semibold text-gray-700">Precio:</label>
                            <input type="text" id="precioEditarP" name="precioEditarP" placeholder="Precio del producto"
                                class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaPrecioEditarP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Categoria -->
                        <div>
                            <label for="categoriaEditarP" class="block mb-1 text-sm font-semibold text-gray-700">Categoría:</label>
                            <select id="categoriaEditarP" name="categoriaEditarP" class="w-full px-4 py-2 border border-gray-300 rounded" required>
                                <option value="">Seleccione categoría</option>
                                <option value="1">Bollería</option>
                                <option value="2">Dulces</option>
                                <option value="3">Especiales</option>
                                <option value="4">Pan artesanal</option>
                                <option value="5">Pan saludable</option>
                            </select>
                            <span id="validaCategoriaEditarP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Descripción -->
                        <div class="col-span-2">
                            <label for="descripcionEditarP" class="block mb-1 text-sm font-semibold text-gray-700">Descripción:</label>
                            <textarea id="descripcionEditarP" name="descripcionEditarP" placeholder="Descripción del producto" class="w-full px-4 py-2 border border-gray-300 rounded  resize-none"></textarea>
                            <span id="validaDescripcionEditarP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Disponibilidad -->
                        <div>
                            <label for="disponibilidadP" class="block mb-1 text-sm font-semibold text-gray-700">Disponibilidad:</label>
                            <input type="text" id="disponibilidadP" name="disponibilidadP" readonly class="w-full px-4 py-2 border border-gray-300 rounded">
                            <span id="validaDisponibilidadP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Estado -->
                        <div>
                            <label for="estadoEditarP" class="block mb-1 text-sm font-semibold text-gray-700">Estado:</label>
                            <select id="estadoEditarP" name="estadoEditarP" class="w-full px-4 py-2 border border-gray-300 rounded" required>
                                <option value="">Seleccione un estado</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            <span id="validaEstadoEditarP" class="text-red-500 text-sm mt-1 block"></span>
                        </div>
                        <!-- Imagen -->
                        <div>
                            <label for="imagenEditarP" class="block mb-1 text-sm font-semibold text-gray-700">Imagen 📂:</label>
                            <input type="file" id="imagenEditarP" name="imagenEditarP" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded">
                            <img id="previewImagenEditar" src="" width="350">
                        </div>
                        <!-- Botones -->
                        <div class="col-span-2 flex justify-end gap-2">
                            <button type="button"
                                onclick="document.getElementById('editProductModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
                        </div>
                    </form>
                    <button onclick="document.getElementById('editProductModal').classList.add('hidden')"
                        class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto tabla-scroll">
                    <table class="min-w-full divide-y divide-gray-200 table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Código</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Imagen</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Categoría</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descripción</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stock</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Disponibilidad</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>




                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($productos as $producto): ?>
                                <tr class="hover:bg-gray-50"
                                    data-categoria="<?= htmlspecialchars($producto['Id_Categoria']) ?>"
                                    data-disponibilidad="<?= htmlspecialchars($producto['Disponibilidad']) ?>"
                                    data-estado="<?= htmlspecialchars($producto['Estado_Producto']) ?>"    
                                    >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 codigo">
                                        <?= htmlspecialchars($producto['Id_Producto']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if (!empty($producto['Imagen'])): ?>
                                            <img src="<?= htmlspecialchars($producto['Imagen']) ?>"
                                                class="w-16 h-16 object-cover rounded">
                                        <?php else: ?>
                                            <span class="text-gray-400 text-sm">Sin imagen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php
                                        $categoria = $con->prepare("SELECT * FROM tb_categoria WHERE Id_Categoria = :id");
                                        $categoria->bindParam(':id', $producto['Id_Categoria']);
                                        $categoria->execute();
                                        $categoria_valor = $categoria->fetch(PDO::FETCH_ASSOC);
                                        echo htmlspecialchars($categoria_valor['Descripcion_Categoria'] ?? 'Sin categoría');
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 nombre">
                                        <?= htmlspecialchars($producto['Nombre_Producto']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?= htmlspecialchars($producto['Descripcion_Producto']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($producto['Stock'] ?? 0) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/
                                        <?= number_format($producto['Precio_Actual'], 2) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= ($producto['Disponibilidad'] == 0) ? 'status-out' :
                                            (($producto['Disponibilidad'] == 1) ? 'status-available' : 'status-scarce') ?>">
                                            <?= ($producto['Disponibilidad'] == 0) ? 'Agotado' :
                                                (($producto['Disponibilidad'] == 1) ? 'Disponible' : 'Escaso') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $producto['Estado_Producto'] === 1 ? 'status-active' : 'status-inactive' ?>">
                                            <?= ($producto['Estado_Producto'] > 0) ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button
                                                onclick="document.getElementById('editProductModal').classList.remove('hidden')"
                                                class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs btn-editar-producto"
                                                data-codigo="<?= $producto['Id_Producto'] ?>"
                                                data-categoria="<?= $producto['Id_Categoria'] ?>"
                                                data-nombre="<?= $producto['Nombre_Producto'] ?>"
                                                data-descripcion="<?= $producto['Descripcion_Producto'] ?>"
                                                data-stock="<?= $producto['Stock'] ?>"
                                                data-stock-min="<?= $producto['Stock_Min'] ?>"
                                                data-precio="<?= $producto['Precio_Actual'] ?>"
                                                data-disponibilidad="<?= $producto['Disponibilidad'] ?>"
                                                data-estado="<?= $producto['Estado_Producto'] ?>">
                                                Editar
                                            </button>
                                            <button
                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs btn-eliminar-producto"
                                                data-codigo="<?= $producto['Id_Producto'] ?>">
                                                Eliminar
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
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

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

        document.addEventListener('DOMContentLoaded', function () {
            new Choices('#filtroCategoria', {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Selecciona categorías'
            });
            new Choices('#filtroDisponibilidad', {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Selecciona disponibilidad'
            });
            new Choices('#filtroEstado', {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Selecciona estado'
            });
        });
    </script>

    <!-- Otros scripts JS -->

    <?php if ($alerta_stock_bajo): ?>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',

                    text: 'Algunos productos están escasos o agotados. Verifica el inventario.',

                    confirmButtonText: 'Entendido'
                });
            });

        </script>
    <?php endif; ?>

    <script src="../assets/js/validarProducto.js"></script>
    <script src="../assets/js/funciones_productos.js"></script>
    <script src="../assets/js/filtroproductos.js"></script>
</body>

</html>