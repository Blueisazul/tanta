<div class="max-w-screen-xl mx-auto px-4 py-8">
    
    <!-- Pestañas mejoradas -->
    <div class="mx-4 md:mx-16 border-b border-neutral-200 mb-6">
        <div class="flex flex-wrap gap-2 md:gap-8 justify-center md:justify-start">
            <button class="py-2 px-2 md:px-4 border-b-4 border-amber-500 text-neutral-700 font-semibold transition-colors duration-200" data-tab="all">Todos mis pedidos</button>
            <button class="py-2 px-2 md:px-4 text-neutral-700 hover:text-neutral-800 border-b-4 border-transparent hover:border-amber-400 transition-colors duration-200" data-tab="in-progress">Pedidos en Curso</button>
            <button class="py-2 px-2 md:px-4 text-neutral-700 hover:text-neutral-800 border-b-4 border-transparent hover:border-amber-400 transition-colors duration-200" data-tab="delivered">Pedidos Entregados</button>
            <button class="py-2 px-2 md:px-4 text-neutral-700 hover:text-neutral-800 border-b-4 border-transparent hover:border-amber-400 transition-colors duration-200" data-tab="cancelled">Pedidos Cancelados</button>
        </div>
    </div>
    
    <!-- Filtros y buscador mejorados -->
    <div class="max-w-screen-xl mx-auto">
        <div class="mx-4 md:mx-20 flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex items-center">
                <span id="order-count" class="border border-slate-300 bg-white text-neutral-800 px-4 py-2 rounded-lg text-sm font-medium ">
                    Cargando pedidos...
                </span>
            </div>
            
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <input type="text" placeholder="Buscar por N° de pedido..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-shadow duration-200" id="search-orders">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                
                <div class="w-full md:w-auto">
                    <select class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white transition-shadow duration-200" id="date-filter">
                        <option value="30">Últimos 30 días</option>
                        <option value="90">3 meses</option>
                        <option value="180">6 meses</option>
                        <option value="365">1 año</option>
                        <option value="all">Todos los pedidos</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lista de pedidos mejorada -->
    <div class="max-w-screen-xl mx-auto">
        <div id="orders-container" class="space-y-4 mx-4 md:mx-20">
            <!-- Pedido 1 - En curso -->
            <?php foreach ($pedidos_completos as $pedido): ?>
                <?php 
                    // Determinar clase de estado
                    $statusClass = '';
                    $statusDot = '';
                    switch(strtolower($pedido['estado'])) {
                        case 'entregado':
                            $statusClass = 'bg-green-100 text-green-800 rounded-full px-2 py-1';
                            $statusDot = 'bg-green-500';
                            break;
                        case 'cancelado':
                            $statusClass = 'bg-red-100 text-red-800 rounded-full px-2 py-1';
                            $statusDot = 'bg-red-500';
                            break;
                        case 'en proceso':
                        default:
                            $statusClass = 'bg-amber-100 text-amber-800 rounded-full px-2 py-1';
                            $statusDot = 'bg-amber-500';
                            break;
                        case 'pendiente':
                            $statusClass = 'bg-blue-100 text-blue-800 rounded-full px-2 py-1';
                            $statusDot = 'bg-blue-500';
                            break;
                    }
                ?>
                <div class="order-card bg-gradient-to-br from-slate-50 to-blue-50/60 rounded-xl shadow-sm hover:shadow-md overflow-hidden transition-shadow duration-300" 
                    data-status="<?= strtolower(str_replace(' ', '-', $pedido['estado'])) ?>" 
                    data-date="<?= date('Y-m-d', strtotime($pedido['fecha'])) ?>"
                    data-order-id="0000000<?= $pedido['id'] ?>">
                    
                    <div class="p-4 border-b border-gray-200 gap-4 flex flex-col md:flex-row justify-between items-center cursor-pointer">
                        <div class="flex flex-col sm:flex-row justify-between w-full md:w-auto">
                            <div class="mb-2 sm:mb-0 flex flex-col px-4 md:px-10">
                                <span class="font-medium text-neutral-500 text-sm">N° de pedido</span>
                                <span class="text-neutral-800 font-semibold text-lg">0000000<?= $pedido['id'] ?></span>
                            </div>
                            <div class="mb-2 sm:mb-0 flex flex-col px-4 md:px-10 border-x border-neutral-200">
                                <span class="font-medium text-neutral-500 text-sm">Fecha de compra</span>
                                <span class="text-neutral-800 font-semibold text-lg"><?= date('d/m/Y', strtotime($pedido['fecha'])) ?></span>
                            </div>
                            <div class="flex flex-col px-4 md:px-10">
                                <span class="font-medium text-neutral-500 text-sm">Total</span>
                                <span class="text-neutral-800 font-semibold text-lg">S/ <?= number_format($pedido['total'], 2) ?></span>
                            </div>
                        </div>
                        <div class="w-full md:w-auto flex justify-end mt-2 md:mt-0 pointer-events-none">
                            <button class="px-4 md:px-10 py-2 text-neutral-700 flex items-center justify-center toggle-detail transition-colors duration-200">
                                <svg class="pointer-events-none ml-1 h-5 w-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Detalles del pedido mejorados -->
                    <div class="p-6 hidden detail-content bg-white/90 backdrop-blur-sm">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-6">
                            <!-- Productos mejorados -->
                            <div class="lg:col-span-2">
                                <h3 class="font-semibold text-lg text-neutral-900 mb-4 pb-2 border-b border-neutral-200">Productos</h3>
                                <ul class="text-sm text-neutral-700 space-y-4">
                                    <?php foreach ($pedido['productos'] as $producto): ?>
                                        <li class="flex items-start gap-4 p-3 bg-slate-50 rounded-lg">
                                            <img src="Admin/<?= $producto['Imagen'] ?>" alt="<?= $producto['Nombre_Producto'] ?>" class="w-16 h-16 object-cover rounded-md border border-neutral-200">
                                            <div class="flex-1 m-1">
                                                <div class="flex justify-between items-start my-1">
                                                    <p class="font-semibold text-neutral-700 text-base"><?= $producto['Nombre_Producto'] ?></p>
                                                    <p class="font-semibold text-neutral-700 text-base">
                                                        S/ <?= number_format($producto['Cantidad_Pedido'] * $producto['Precio_Venta'], 2) ?>
                                                    </p>
                                                </div>
                                                <div class="flex flex-row text-sm justify-between my-1">
                                                    <p><span class="text-neutral-500">Cantidad:</span> <?= $producto['Cantidad_Pedido'] ?></p>
                                                    <p><span class="text-neutral-500">P. Unitario:</span> S/ <?= number_format($producto['Precio_Venta'], 2) ?></p>
                                                    <p><span class="text-neutral-500">Descuento:</span> -S/ <?= number_format($producto['Descuento'], 2) ?></p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- Información de envío y estado mejorados -->
                            <div class="space-y-6">
                                <div class="bg-slate-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-lg text-neutral-900 mb-3 pb-2 border-b border-neutral-200">Información de Envío</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="font-medium text-neutral-800">Método de entrega</p>
                                            <p class="text-neutral-600 text-sm">DELIVERY</p>
                                        </div>
                                        <div>
                                            <p class="font-medium text-neutral-800">Dirección de envío</p>
                                            <p class="text-neutral-600 text-sm"><?= $pedido['direccion_envio'] ?? 'No especificada' ?></p>
                                        </div>
                                        <div>
                                            <p class="font-medium text-neutral-800">Costo de envío</p>
                                            <p class="text-neutral-600 text-sm">S/ <?= number_format($pedido['costo_envio'], 2) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-lg text-neutral-900 mb-3 pb-2 border-b border-neutral-200">Estado del Pedido</h3>
                                    <div class="flex items-center mb-2">
                                        <span class="inline-block w-3 h-3 rounded-full <?= $statusDot ?> mr-2"></span>
                                        <span class="text-neutral-700 font-medium"><?= $pedido['estado'] ?></span>
                                    </div>
                                    <p class="text-sm text-neutral-500">Fecha estimada de entrega: <?= date('d/m/Y', strtotime('+1 day', strtotime($pedido['fecha']))) ?></p>
                                    
                                    <!-- Barra de progreso -->
                                    <div class="mt-4">
                                        <div class="relative pt-1">
                                            <div class="flex mb-2 items-center justify-between">
                                                <div>
                                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full <?= $statusClass ?>">
                                                        Progreso
                                                    </span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs font-semibold inline-block <?= $statusClass ?>">
                                                        <?php 
                                                            $progress = 0;
                                                            if(strtolower($pedido['estado']) == 'entregado') $progress = 100;
                                                            elseif(strtolower($pedido['estado']) == 'cancelado') $progress = 0;
                                                            elseif(strtolower($pedido['estado']) == 'pendiente') $progress = 25;
                                                            else $progress = 50;
                                                            echo $progress.'%';
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-slate-200">
                                                <div style="width:<?= $progress ?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center <?= str_replace('text-amber-800', 'bg-amber-500', $statusClass) ?>"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones del pedido -->
                        <div class="flex flex-col sm:flex-row justify-between items-center pt-4 mt-6 border-t border-neutral-200 gap-4">
                            <div class="text-sm text-neutral-500">
                                ¿Necesitas ayuda con este pedido?
                            </div>
                            <div class="flex gap-3">
                                <button class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors duration-200 text-sm font-medium">
                                    Contactar soporte
                                </button>
                                <?php if(strtolower($pedido['estado']) != 'entregado' && strtolower($pedido['estado']) != 'cancelado'): ?>
                                    <button class="px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200 text-sm font-medium cancel-order" data-order-id="<?= $pedido['id'] ?>">
                                        Cancelar pedido
                                    </button>
                                <?php endif; ?>
                                <?php if(strtolower($pedido['estado']) == 'entregado'): ?>
                                    <button class="px-4 py-2 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg hover:bg-amber-100 transition-colors duration-200 text-sm font-medium">
                                        Devolver producto
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Mensaje cuando no hay pedidos -->
            <div id="no-orders-message" class="hidden text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-slate-900">No se encontraron pedidos</h3>
                <p class="mt-1 text-slate-500">Intenta cambiar los filtros o realiza una nueva compra.</p>
                <div class="mt-6">
                    <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        Ir a la tienda
                    </a>
                </div>
            </div>
        </div>
    </div>    
</div>

<script>
    // Mostrar/ocultar detalles al hacer clic en toda la tarjeta
    document.querySelectorAll('.order-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Evitar que se active al hacer clic en botones de acción
            if (
                e.target.closest('.cancel-order') ||
                e.target.closest('.toggle-detail') ||
                e.target.closest('button') ||
                e.target.tagName === 'A'
            ) return;

            const content = card.querySelector('.detail-content');
            const icon = card.querySelector('.toggle-detail svg');

            // Cerrar otros detalles abiertos
            document.querySelectorAll('.detail-content').forEach(el => {
                if (el !== content && !el.classList.contains('hidden')) {
                    el.classList.add('hidden');
                    const otherIcon = el.closest('.order-card').querySelector('svg');
                    otherIcon.classList.remove('rotate-180');
                }
            });

            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');

            // Scroll suave para mostrar el detalle completo
            if (!content.classList.contains('hidden')) {
                setTimeout(() => {
                    content.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            }
        });
    });

    // Eliminar el evento anterior del botón de detalle
    document.querySelectorAll('.toggle-detail').forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation(); // Solo evita que el evento burbujee, pero no hace nada más
        });
    });

    // Funcionalidad para cambiar pestañas mejorada
    const tabs = document.querySelectorAll('[data-tab]');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Actualizar pestaña activa
            tabs.forEach(t => {
                t.classList.remove('border-amber-500', 'text-neutral-700');
                t.classList.add('border-transparent', 'text-neutral-700');
            });
            tab.classList.add('border-amber-500');
            tab.classList.remove('border-transparent');
            
            // Filtrar pedidos
            const tabName = tab.getAttribute('data-tab');
            filterOrders(tabName);
        });
    });
    
    // Filtrar pedidos por pestaña con búsqueda y fecha
    function filterOrders(tab, searchTerm = '', daysFilter = null) {
        const orderCards = document.querySelectorAll('.order-card');
        let visibleCount = 0;
        const noOrdersMessage = document.getElementById('no-orders-message');
        
        orderCards.forEach(card => {
            const status = card.getAttribute('data-status');
            const orderId = card.getAttribute('data-order-id');
            const orderDate = new Date(card.getAttribute('data-date'));
            const currentDate = new Date();
            const diffTime = Math.abs(currentDate - orderDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            let shouldShow = false;
            
            // Filtro por pestaña
            switch(tab) {
                case 'all':
                    shouldShow = true;
                    break;
                case 'in-progress':
                    shouldShow = status === 'en-proceso' || status === 'en-camino';
                    break;
                case 'delivered':
                    shouldShow = status === 'entregado';
                    break;
                case 'cancelled':
                    shouldShow = status === 'cancelado';
                    break;
            }
            
            // Filtro por búsqueda
            if (searchTerm && !orderId.toLowerCase().includes(searchTerm.toLowerCase())) {
                shouldShow = false;
            }
            
            // Filtro por fecha
            if (daysFilter && daysFilter !== 'all' && diffDays > parseInt(daysFilter)) {
                shouldShow = false;
            }
            
            if (shouldShow) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Actualizar contador y mostrar mensaje si no hay pedidos
        document.getElementById('order-count').textContent = `${visibleCount} pedido${visibleCount !== 1 ? 's' : ''}`;
        
        if (visibleCount === 0) {
            noOrdersMessage.classList.remove('hidden');
        } else {
            noOrdersMessage.classList.add('hidden');
        }
    }
    
    // Filtrar por fecha
    document.getElementById('date-filter').addEventListener('change', (e) => {
        const days = e.target.value;
        const activeTab = document.querySelector('[data-tab].border-amber-600').getAttribute('data-tab');
        const searchTerm = document.getElementById('search-orders').value;
        filterOrders(activeTab, searchTerm, days);
    });
    
    // Buscador en tiempo real con debounce
    let searchTimeout;
    document.getElementById('search-orders').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = e.target.value;
            const activeTab = document.querySelector('[data-tab].border-amber-600').getAttribute('data-tab');
            const daysFilter = document.getElementById('date-filter').value;
            filterOrders(activeTab, searchTerm, daysFilter);
        }, 300);
    });
    
    // Cancelar pedido
    document.querySelectorAll('.cancel-order').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const orderId = button.getAttribute('data-order-id');
            if (confirm(`¿Estás seguro que deseas cancelar el pedido #${orderId}?`)) {
                // Aquí iría la llamada AJAX para cancelar el pedido
                alert(`Pedido #${orderId} cancelado (simulado)`);
                // Recargar o actualizar la interfaz
                location.reload();
            }
        });
    });
    
    // Inicializar mostrando todos los pedidos
    filterOrders('all');
    
    // Scroll a la sección de pedidos si hay hash
    document.addEventListener("DOMContentLoaded", function() {
        if (window.location.hash === "#orders-container") {
            const el = document.getElementById("orders-container");
            if (el) {
                setTimeout(() => {
                    el.scrollIntoView({ behavior: "smooth" });
                }, 300);
            }
        }
    });
</script>