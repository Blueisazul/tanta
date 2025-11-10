<!-- Sombreado de fondo -->
<div id="checkout-overlay" class="fixed z-60 inset-0 bg-black/50 hidden"></div>

<div>
    <!-- Carrito flotante -->
    <button id="cart-button" class="cursor-pointer fixed top-1/2 right-2 transform -translate-y-1/2 bg-gradient-to-bl from-white via-slate-50 to-white border backdrop-blur-md border-stone-200 text-gray-700 rounded-2xl shadow-md py-3 px-3 z-150 flex flex-col items-center hover:shadow-lg transition-all">
  
      <!-- Icono y contador -->
      <div class="flex items-center gap-2 pb-2">
        <!-- Icono del carrito -->
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag-icon lucide-shopping-bag">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
          <path d="M3 6h18"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <!-- Contador de items -->
        <span id="cart-count" class="text-lg font-semibold">0</span> 
        <span class="text-sm tracking-wider text-gray-400 font-medium">Items</span>
      </div>
    
      <!-- Precio -->
      <div class="px-4 py-1">
        <!-- Precio total carrito flotante -->
        <p id="cart-total-button" class="text-lg font-extrabold tracking-wide bg-gradient-to-r from-amber-600/70 to-yellow-600 bg-clip-text text-transparent">
          S/ 0.00
        </p>
      </div>
    </button>
  
    <!-- Ventana modal de checkout -->
    <div id="checkout-modal" class="fixed z-150 inset-y-0 right-0 h-full bg-white p-6 shadow-lg w-96 text-center flex flex-col justify-between hidden">
      <div class="overflow-auto max-h-[80vh]">
        <h2 class="text-xl font-semibold">CARRITO</h2>
        <!-- Lista/contenedor de productos en el carrito -->
        <div id="cart-items" class="mt-4 text-left space-y-2"></div>
      </div>
      <div class="mt-4">
        <!-- Total del carrito/ ventana carrito -->
        <p id="cart-total" class="text-lg font-semibold">Total: S/0.00</p>
        <div class="flex flex-col justify-between mt-4">
          <button id="continue-shopping" class="cursor-pointer bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-sm">Seguir comprando</button>
          <button id="continue-pago" class="cursor-pointer mt-2 bg-black hover:bg-gray-800 text-white px-4 py-2 rounded-sm">
            Pagar
          </button>

        </div>
      </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Verifica si el carrito fue pagado
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('carrito') === 'limpio') {
        sessionStorage.removeItem("checkoutCart"); // Limpia el carrito real
        limpiarCarritoUI(); // Limpia visualmente el carrito flotante
    }

    // Función para limpiar interfaz del carrito
    function limpiarCarritoUI() {
        const cartItems = document.getElementById("cart-items");
        const cartCount = document.getElementById("cart-count");
        const cartTotal = document.getElementById("cart-total");
        const cartTotalButton = document.getElementById("cart-total-button");
        const urlParams = new URLSearchParams(window.location.search);

        if (cartItems) cartItems.innerHTML = "<p class='text-gray-500'>Tu carrito está vacío.</p>";
        if (cartCount) cartCount.textContent = "0";
        if (cartTotal) cartTotal.textContent = "Total: S/0.00";
        if (cartTotalButton) cartTotalButton.textContent = "S/ 0.00";
        /*
        if (urlParams.get('carrito') === 'limpio') {
            sessionStorage.removeItem("checkoutCart"); // Limpia el carrito real
            limpiarCarritoUI(); // Limpia visualmente el carrito flotante
        }
        */
    }
});
</script>