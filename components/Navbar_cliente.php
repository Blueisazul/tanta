<style>
    .enlace_datos:hover { background: #3d68ff; }
</style>

<nav class="">
	<div class="max-w-screen-xl mx-auto flex flex-wrap items-center justify-between py-4  ">
        <a href="index.php" class="flex items-center space-x-3 rtl:space-x-reverse">
            <div class="text-zinc-800 dark:text-white">
                <?php include __DIR__ . '/logo.php'; ?>
            </div>
        </a>

        <!-- Menú Hamburguesa para pantallas pequeñas -->
        <button
            id="menu-toggle"
            type="button"
            class="inline-flex items-center p-2 text-sm text-black rounded-lg md:hidden hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400"
            aria-controls="navbar-sticky"
            aria-expanded="false"
        >
            <span class="sr-only">Abrir menú</span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>




        
        <!-- Navegación principal -->
        <div
        id="navbar-sticky"
        class="hidden w-full md:flex md:w-auto md:items-center md:space-x-5 mt-1 md:mt-0"
        >
            <ul class="flex flex-col items-center justify-center text-center md:flex-row gap-5 bg-white md:bg-transparent p-4 md:p-0 md:space-x-5 w-full">
            <li>
                <a href="index.php" class="text-black hover:text-amber-500">Inicio</a>
            </li>
            <li>
                <a href="catalogo.php" class="text-black hover:text-amber-500">Productos</a>
            </li>
            <li>
                <a href="sobre-nosotros.php"  class="text-black hover:text-amber-500">Sobre Nosotros</a>
            </li>
            <li>
                <a href="contacto.php" class="text-black hover:text-amber-500">Contacto</a>
            </li>
            </ul>
        </div>

        <!-- Botón de llamada a la acción -->

        <!-- Contenedor del la lupa y icono de inicio -->
        <div class="flex gap-3 items-center absolute left-1/2 -translate-x-1/2 top-4 md:static md:mt-0 md:order-2 md:right-4 md:translate-x-0 z-30">
            <!-- Lupa -->
            <div class="cursor-pointer hover:text-amber-500">
                <svg width="25" height="25" fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <!-- Usuario -->
            <div class="relative">
                <button id="usuarioDropdown" class="cursor-pointer hover:text-amber-500 flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round">
                        <circle cx="12" cy="8" r="5"/>
                        <path d="M20 21a8 8 0 0 0-16 0"/>
                    </svg>
                    <span>Bienvenido, <?php echo $nombre; ?></span>
                </button>
                <!-- Menú desplegable (oculto por defecto): hidden -->
                <ul id="dropdownMenu" class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-md hidden z-50">
                        <a class="block px-4 py-2 text-gray-700 hover:bg-gray-200 transition-all duration-200 ease-in-out" href="#">📝 Datos Personales</a>
                        <a class="block px-4 py-2 text-gray-700 hover:bg-gray-200 transition-all duration-200 ease-in-out" id="misComprasBtn" href="listaPedido.php">🛍️ Mis Compras</a>
                        <a class="block px-4 py-2 text-gray-700 hover:bg-gray-200 transition-all duration-200 ease-in-out" id="cerrarSesionBtn" href="#">🚪 Cerrar Sesión</a>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
  // Hamburguesa: mostrar/ocultar menú en móvil
  const toggleBtn = document.getElementById('menu-toggle');
  const menu = document.getElementById('navbar-sticky');
  toggleBtn.addEventListener('click', () => {
    menu.classList.toggle('hidden');
    menu.classList.toggle('block');
  });

  // Menú usuario: mostrar/ocultar dropdown
  document.getElementById("usuarioDropdown").addEventListener("click", function (event) {
      event.stopPropagation();
      let menu = document.getElementById("dropdownMenu");
      menu.classList.toggle("hidden");
  });
  // Cerrar el menú si se hace clic fuera del boton Bienvenido
  document.addEventListener("click", function (event) {
      let menu = document.getElementById("dropdownMenu");
      let button = document.getElementById("usuarioDropdown");
      if (!button.contains(event.target) && !menu.contains(event.target)) {
          menu.classList.add("hidden");
      }
  });
</script>
<script src="assets/js/cerrar_sesion.js"></script>