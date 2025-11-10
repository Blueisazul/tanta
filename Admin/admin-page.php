<?php
session_start();

require '../Config/config.php';
require '../database.php';

if (isset($_SESSION['usuario'])) {
    if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Cliente') {
        header("Location: ../index.php");
        exit();
    } else {
        $db = new Database();
        $con = $db->conectar();

        $sql = $con->prepare("SELECT Nombre, Apellido, DNI, Telefono FROM tb_persona WHERE Id_Persona = :id");
        $sql->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        $sql->execute();
        $datos_persona = $sql->fetch(PDO::FETCH_ASSOC);

        $nombre = $datos_persona['Nombre'];
    }
}


// Obtener distribución de categorías
$sql_categorias = $con->prepare("
    SELECT c.Descripcion_Categoria AS categoria, COUNT(p.Id_Producto) AS cantidad
    FROM tb_producto p
    INNER JOIN tb_categoria c ON p.Id_Categoria = c.Id_Categoria
    GROUP BY p.Id_Categoria
");
$sql_categorias->execute();
$distribucion_categorias = $sql_categorias->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos para ChartJS
$labels_categorias = [];
$data_categorias = [];
foreach ($distribucion_categorias as $categoria) {
    $labels_categorias[] = $categoria['categoria'];
    $data_categorias[] = $categoria['cantidad'];
}


// Obtener productos más vendidos
$sql_tendencias = $con->prepare("
    SELECT p.Nombre_Producto AS producto, SUM(Cantidad_Pedido) AS cantidad
    FROM tb_detalle_pedido d
    INNER JOIN tb_producto p ON d.Id_Producto = p.Id_Producto
    GROUP BY d.Id_Producto
    ORDER BY cantidad DESC
    LIMIT 7
");
$sql_tendencias->execute();
$productos_tendencias = $sql_tendencias->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos para ChartJS
$labels_tendencias = [];
$data_tendencias = [];
foreach ($productos_tendencias as $producto) {
    $labels_tendencias[] = $producto['producto'];
    $data_tendencias[] = $producto['cantidad'];
}


// Obtener ventas mensuales
$sql_ventas = $con->prepare("
    SELECT 
        MONTH(Fecha_Pedido) AS mes,
        SUM(Total_Pedido) AS total
    FROM tb_pedido
    WHERE YEAR(Fecha_Pedido) = YEAR(CURRENT_DATE)
    GROUP BY MONTH(Fecha_Pedido)
    ORDER BY mes
");
$sql_ventas->execute();
$ventas_mensuales = $sql_ventas->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos para ChartJS
$labels_meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
$data_ventas = array_fill(0, 12, 0); // Inicializar con 0 para todos los meses
foreach ($ventas_mensuales as $venta) {
    $data_ventas[$venta['mes'] - 1] = $venta['total']; // -1 porque los meses van de 1 a 12
}


// Obtener ventas mensuales del año anterior
$sql_ventas_prev = $con->prepare("
    SELECT 
        MONTH(Fecha_Pedido) AS mes,
        SUM(Total_Pedido) AS total
    FROM tb_pedido
    WHERE YEAR(Fecha_Pedido) = YEAR(CURRENT_DATE) - 1
    GROUP BY MONTH(Fecha_Pedido)
    ORDER BY mes
");
$sql_ventas_prev->execute();
$ventas_mensuales_prev = $sql_ventas_prev->fetchAll(PDO::FETCH_ASSOC);

$data_ventas_prev = array_fill(0, 12, 0);
foreach ($ventas_mensuales_prev as $venta) {
    $data_ventas_prev[$venta['mes'] - 1] = $venta['total'];
}


// Obtener ingresos por distrito
$sql_distritos = $con->prepare("
    SELECT d.Descripcion_Distrito AS distrito, SUM(p.Total_Pedido) AS ingresos
    FROM tb_pedido p
    INNER JOIN tb_distrito d ON p.Id_Distrito = d.Id_Distrito
    GROUP BY p.Id_Distrito
    ORDER BY ingresos DESC
");
$sql_distritos->execute();
$ingresos_distritos = $sql_distritos->fetchAll(PDO::FETCH_ASSOC);

$labels_distritos = [];
$data_ingresos = [];
foreach ($ingresos_distritos as $row) {
    $labels_distritos[] = $row['distrito'];
    $data_ingresos[] = $row['ingresos'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <meta name="author" content="David Grzyb">
    <meta name="description" content="">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            --border-color: #e5e7eb;
            --primary-gradient: linear-gradient(to right, #4278f5, #2655ea);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-main);
        }

        .sidebar {
            background: #ffffff;
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

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .stat-card-primary {
            border-top: 3px solid #4f46e5;
        }

        .stat-card-secondary {
            border-top: 3px solid #06b6d4;
        }

        .stat-card-success {
            border-top: 3px solid #10b981;
        }

        .stat-card-warning {
            border-top: 3px solid #f59e0b;
        }

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            background: #2756EA;
        }

        .btn-outline {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: #f9fafb;
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
            <a href="../Admin/admin-page.php" class="flex items-center py-3 pl-6 nav-item active-nav-link">
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
            <h1 class="text-lg font-semibold text-gray-800">Panel de Administración</h1>
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
            <!-- Header and Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h1 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">Resumen General</h1>
                <div class="flex space-x-2">
                    <button class="px-4 py-2 btn-outline rounded-md text-sm font-medium">
                        <i class="fas fa-download mr-2"></i>Exportar
                    </button>
                    <button class="px-4 py-2 btn-primary rounded-md text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>Generar Reporte
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
                <div class="stat-card stat-card-primary rounded-lg p-5">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Ventas Totales</p>
                            <h3 class="text-2xl font-bold mt-1 text-gray-800">$24,780</h3>
                            <p class="text-xs mt-2 text-gray-500"><span class="text-green-600">+12.5%</span> vs mes
                                anterior</p>
                        </div>
                        <div
                            class="bg-indigo-100 p-2 rounded-full h-10 w-10 flex items-center justify-center text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-shopping-cart-icon lucide-shopping-cart">
                                <circle cx="8" cy="21" r="1" />
                                <circle cx="19" cy="21" r="1" />
                                <path
                                    d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card stat-card-secondary rounded-lg p-5">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Clientes Nuevos</p>
                            <h3 class="text-2xl font-bold mt-1 text-gray-800">1,245</h3>
                            <p class="text-xs mt-2 text-gray-500"><span class="text-green-600">+8.2%</span> vs mes
                                anterior</p>
                        </div>
                        <div
                            class="bg-cyan-100 p-2 rounded-full h-10 w-10 flex items-center justify-center text-cyan-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-user-plus-icon lucide-user-plus">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <line x1="19" x2="19" y1="8" y2="14" />
                                <line x1="22" x2="16" y1="11" y2="11" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card stat-card-success rounded-lg p-5">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Órdenes Completadas</p>
                            <h3 class="text-2xl font-bold mt-1 text-gray-800">856</h3>
                            <p class="text-xs mt-2 text-gray-500"><span class="text-green-600">+5.3%</span> vs mes
                                anterior</p>
                        </div>
                        <div
                            class="bg-green-100 p-2 rounded-full h-10 w-10 flex items-center justify-center text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-circle-check-big-icon lucide-circle-check-big">
                                <path d="M21.801 10A10 10 0 1 1 17 3.335" />
                                <path d="m9 11 3 3L22 4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card stat-card-warning rounded-lg p-5">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Productos Vendidos</p>
                            <h3 class="text-2xl font-bold mt-1 text-gray-800">3,456</h3>
                            <p class="text-xs mt-2 text-gray-500"><span class="text-green-600">+15.7%</span> vs mes
                                anterior</p>
                        </div>
                        <div
                            class="bg-amber-100 p-2 rounded-full h-10 w-10 flex items-center justify-center text-amber-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" 
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                                stroke-linejoin="round" class="lucide lucide-package-check-icon lucide-package-check">
                                <path d="m16 16 2 2 4-4"/>
                                <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                                <path d="m7.5 4.27 9 5.15"/><polyline points="3.29 7 12 12 20.71 7"/>
                                <line x1="12" x2="12" y1="22" y2="12"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                <!-- Monthly Comparison (antes Ventas Mensuales) -->
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Evolución de Ventas por Mes</h3>
                        <!-- Puedes agregar botones de año si lo deseas -->
                    </div>
                    <div class="chart-container">
                        <canvas id="chartOne"></canvas>
                    </div>
                </div>

                <!-- Categorías -->
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Distribución de Categorías</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 text-xs btn-outline rounded-md">Semanal</button>
                            <button class="px-3 py-1 text-xs btn-primary rounded-md">Mensual</button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartThree"></canvas>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                <!-- Delivery Performance -->
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Comparativas de Ventas Anual</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartTwo"></canvas>
                    </div>
                </div>

                <!-- Tendencía -->
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Productos Tendencias</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 text-xs btn-outline rounded-md">Semanal</button>
                            <button class="px-3 py-1 text-xs btn-primary rounded-md">Mensual</button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartFour"></canvas>
                    </div>
                </div>
            </div>

            <!-- Ingresos por Distritos -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Ingresos por Distritos</h3>
                </div>
                <div class="chart-container">
                    <canvas id="chartDistritos"></canvas>
                </div>
            </div>
        </main>
    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"
        integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>

    <script>
        // Chart One - Ventas Mensuales (ahora con apariencia similar a Chart Two)
        var chartOne = document.getElementById('chartOne').getContext('2d');
        new Chart(chartOne, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels_meses); ?>,
                datasets: [{
                    label: 'Ventas Online',
                    data: <?php echo json_encode($data_ventas); ?>,
                    backgroundColor: 'rgba(79, 70, 229, 0.1)', // Cambiado a tono violeta
                    borderColor: '#4f46e5', // Violeta más intenso
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true, // Relleno debajo de la línea
                    lineTension: 0.4 // Suaviza la línea
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function (value) {
                                return '$' + value;
                            }
                        },
                        gridLines: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)',
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false,
                        }
                    }]
                },
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 12,
                        padding: 20
                    }
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].label + ': $' + tooltipItem.yLabel;
                        }
                    }
                }
            }
        });

        // Chart Two - Comparativa de Ventas Año Actual vs Año Anterior
        var chartTwo = document.getElementById('chartTwo').getContext('2d');
        new Chart(chartTwo, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels_meses); ?>,
                datasets: [                       
                    {
                        label: '2024',
                        data: <?php echo json_encode($data_ventas_prev); ?>,
                        backgroundColor: 'rgba(6, 182, 212, 0.1)', // Cyan
                        borderColor: '#06b6d4',
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#06b6d4',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true
                    },
                    {
                        label: '2025',
                        data: <?php echo json_encode($data_ventas); ?>,
                        backgroundColor: 'rgba(16, 185, 129, 0.1)', // Violeta
                        borderColor: '#10b981',
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                         pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function (value) { return '$' + value; }
                        },
                        gridLines: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)',
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false,
                        }
                    }]
                },
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 12,
                        padding: 20
                    }
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].label + ': $' + tooltipItem.yLabel;
                        }
                    }
                }
            }
        });

        // Chart Three - Distribución de Categorías
        new Chart(document.getElementById('chartThree'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels_categorias); ?>,
                datasets: [{
                    data: <?php echo json_encode($data_categorias); ?>,
                    backgroundColor: [
                        '#4f46e5', // Violeta
                        '#06b6d4', // Cyan
                        '#10b981', // Verde esmeralda
                        '#f59e0b', // Ámbar
                        '#f97316', // Naranja
                        '#ef4444', // Rojo
                        '#8b5cf6', // Violeta más claro
                        '#ec4899'  // Rosa
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 70,
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 20
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var total = dataset.data.reduce(function (prev, curr) { return prev + curr; });
                            var currentValue = dataset.data[tooltipItem.index];
                            var percentage = Math.floor((currentValue / total) * 100) + "%";
                            return data.labels[tooltipItem.index] + ': ' + currentValue + ' (' + percentage + ')';
                        }
                    }
                }
            }
        });

        // Chart Four - Productos Tendencias
        new Chart(document.getElementById('chartFour'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($labels_tendencias); ?>,
                datasets: [{
                    data: <?php echo json_encode($data_tendencias); ?>,
                    backgroundColor: [
                        '#4f46e5', // Violeta
                        '#06b6d4', // Cyan
                        '#10b981', // Verde esmeralda
                        '#f59e0b', // Ámbar
                        '#f97316', // Naranja
                        '#ef4444', // Rojo
                        '#8b5cf6'  // Violeta más claro
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 20
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            const dataset = data.datasets[tooltipItem.datasetIndex];
                            const value = dataset.data[tooltipItem.index];
                            return data.labels[tooltipItem.index] + ': ' + value + ' unidades';
                        }
                    }
                }
            }
        });

        // Chart Distritos - Ingresos por Distritos
        new Chart(document.getElementById('chartDistritos'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_distritos); ?>,
                datasets: [{
                    label: 'Ingresos',
                    data: <?php echo json_encode($data_ingresos); ?>,
                    backgroundColor: '#0078D4', // Azul Microsoft
                    borderColor: '#0078D4',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function (value) { return '$' + value; }
                        },
                        gridLines: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)',
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false,
                        },
                        barPercentage: 0.6
                    }]
                },
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 12,
                        padding: 20
                    }
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].label + ': $' + tooltipItem.yLabel;
                        }
                    }
                }
            }
        });
    </script>

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
                    localStorage.removeItem('cart');
                    window.location.href = "../Config/cerrar_cuenta.php";
                }
            });
        }
    </script>
</body>

</html>