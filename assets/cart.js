
document.addEventListener("DOMContentLoaded", function () {
    const cartButton = document.getElementById('cart-button');//Botón del carrito que permite ver la ventana de carrito
    const cartCount = document.getElementById('cart-count');//Contador de productos en el carrito
    const checkoutModal = document.getElementById('checkout-modal');//Ventana de carrito
    const continueShopping = document.getElementById('continue-shopping');//Permite cerrar la ventana de carrito
    const checkoutOverlay = document.getElementById("checkout-overlay");// Sombreado de fondo
    const cartContainer = document.getElementById('cart-items');// Contenedor de la ventana de los productos del carrito
    const continuePago = document.getElementById('continue-pago');


   //¿Qué es el localstorage?
    // LocalStorage es una forma de almacenar datos en el navegador del usuario.
    //  Permite guardar información de manera persistente, 
    // incluso después de que el usuario cierre la pestaña o el navegador.

    // Verifica si el carrito ya existe en localStorage, si no, lo inicializa

    //Se guardan todos los datos del carrito
    function getCart() {
        return JSON.parse(localStorage.getItem('cart')) || [];
    }

    // Guarda el carrito en localStorage y actualiza la UI
    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI(); // Actualizar la interfaz del carrito
    }





    // Hacer que updateCartUI esté disponible globalmente
    // Esto permite que otras partes del código puedan llamar a esta función
    // para actualizar la interfaz del carrito cuando sea necesario.
    window.updateCartUI = function updateCartUI() {
        const cart = getCart();// Obtener el carrito del localStorage
        cartCount.textContent = cart.length; // Actualizar el contador del carrito
        cartContainer.innerHTML = ""; // Limpiar el contenedor/lista del carrito antes de actualizar
        const cartTotal = document.getElementById("cart-total"); // Cantidad total de la ventana de carrito

        let total = 0; // Variable para calcular el total

        // Si el carrito está vacío, mostrar un mensaje y establever el total a 0
        // Si el carrito tiene productos, iterar sobre ellos y mostrarlos
        if (cart.length === 0) {
            cartContainer.innerHTML = "<p class='text-gray-500 text-center'>Tu carrito está vacío.</p>";
            cartTotal.textContent = "Total: S/ 0.00"; // Reiniciar total
        } else {
            cart.forEach((product, index) => {
                const price = parseFloat(product.price) || 0; // Asegurarse de que el precio sea un número
                const item = document.createElement('div');
                item.classList.add('flex', 'items-center', 'justify-between', 'border-b', 'border-gray-300', 'py-4', 'pt-2', 'gap-4');

                const isMaxQuantity = product.quantity >= product.stock;

                item.innerHTML = ` 
                    <div>
                        <div class="bg-gradient-to-bl from-blue-50/70 to-slate-50/30 rounded-xl w-16 h-16">
                            <img src="${product.img}" class="checkout-img">
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <div>
                                <p class="font-medium text-sm">${product.title}</p>
                                <p class="text-gray-400 font-medium text-xs">${product.categoria}</p>
                                <p class="text-gray-400 font-medium text-xs">Stock: ${product.stock}</p>
                            </div>
                            <!-- Eliminar producto -->
                            <button class="cursor-pointer remove-item text-rose-600 hover:text-white hover:bg-rose-500 rounded-sm text-xs px-1 py-1" data-index="${index}">                
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2" pointer-events="none"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>                        
                            </button>
                        </div>    

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-black text-base">S/ ${price.toFixed(2)}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button class="minus-quantity decrease-item cursor-pointer bg-gray-200 hover:bg-gray-300 px-1 py-1 rounded text-xl" data-index="${index}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-minus-icon lucide-minus" pointer-events="none"><path d="M5 12h14"/></svg>
                                </button>

                                <span class="quantity text-sm">${product.quantity || 1}</span>

                                <button class="plus-quantity decrease-item cursor-pointer bg-gray-200 hover:bg-gray-300 px-1 py-1 rounded text-xl ${isMaxQuantity ? 'opacity-50 cursor-not-allowed' : ''}" data-index="${index}" ${isMaxQuantity ? 'disabled' : ''}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus" pointer-events="none"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                /*
                item.innerHTML = ` 
                    <div>
                        <div class="bg-gradient-to-bl from-blue-50/70 to-slate-50/30 rounded-xl w-16 h-16">
                            <img src="${product.img}" class="checkout-img">
                        </div>
                    </div>
                
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <div>
                                <p class="font-medium text-sm">${product.title}</p>
                                <p class="text-gray-400 font-medium text-xs">${product.categoria}</p>
                            </div>
                            <!-- Eliminar producto -->
                            <button class="cursor-pointer remove-item text-rose-600 hover:text-white hover:bg-rose-500 rounded-sm text-xs px-1 py-1" data-index="${index}">                
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2" pointer-events="none"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>                        
                            </button>
                        </div>    
                    
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-black text-base">S/ ${price.toFixed(2)}</p>
                            </div>
                        
                            <div class="flex items-center gap-2">
                                <button class="minus-quantity decrease-item cursor-pointer bg-gray-200 hover:bg-gray-300 px-1 py-1 rounded text-xl" data-index="${index}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-minus-icon lucide-minus" pointer-events="none"><path d="M5 12h14"/></svg>
                                </button>
                                
                                <span class="quantity text-sm">${product.quantity || 1}</span>
                                
                                <button class="plus-quantity decrease-item cursor-pointer bg-gray-200 hover:bg-gray-300 px-1 py-1 rounded text-xl" data-index="${index}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus" pointer-events="none"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                */

                // <button class="cursor-pointer remove-item text-red-600 text-xs px-2" data-index="${index}">🗑</button>

                cartContainer.appendChild(item);// Agregar el producto al contenedor del carrito
                total += price * (product.quantity || 1); // Sumar precios según la cantidad
            });
            // Actualizar el total del carrito
            cartTotal.textContent = `Total: S/ ${total.toFixed(2)}`; // Mostrar total con 2 decimales
        }

        const cartTotalButton = document.getElementById("cart-total-button");
        if (cartTotalButton) {
            cartTotalButton.textContent = `S/ ${total.toFixed(2)}`;
        }        
        
    };

    // Agregar evento de clic a los botones de eliminar y aumentar/disminuir cantidad
    // Esto permite que el usuario pueda eliminar productos del carrito o cambiar su cantidad
    cartContainer.addEventListener('click', (event) => {
        if (event.target.classList.contains('remove-item')) {
            const index = parseInt(event.target.dataset.index, 10); // Obtener el índice del producto
            if (!isNaN(index)) {// Verificar que el índice sea un número
                // Eliminar el producto del carrito
                removeItem(index); // Llamar a la función para eliminar el producto
            }
        } else if (event.target.classList.contains('plus-quantity')) {
            const index = parseInt(event.target.dataset.index, 10);// Obtener el índice del producto
            // Aumentar la cantidad del producto
            if (!isNaN(index)) {// Verificar que el índice sea un número
                updateQuantity(index, 1); // Aumentar cantidad
            }
        } else if (event.target.classList.contains('minus-quantity')) {
            const index = parseInt(event.target.dataset.index, 10);// Obtener el índice del producto
            // Disminuir la cantidad del producto
            if (!isNaN(index)) {// Verificar que el índice sea un número
                updateQuantity(index, -1); // Disminuir cantidad
            }
        }
    });

    // Función para eliminar un ítem del carrito
    function removeItem(index) {
        let cart = getCart();
        if (index >= 0 && index < cart.length) {
            cart.splice(index, 1); // Eliminar el producto del carrito
            saveCart(cart); // Guardar el carrito actualizado
        }
    }

    // Función para actualizar la cantidad de un producto en el carrito
    function updateQuantity(index, change) {
        let cart = getCart();
        if (index >= 0 && index < cart.length) {
            const product = cart[index];
            const newQuantity = Math.max(1, (product.quantity || 1) + change); // Asegurar que la cantidad no sea menor que 1
            product.quantity = newQuantity;
            saveCart(cart); // Guardar el carrito actualizado
        }
    }

    /*
    function addToCart(event) {
    const button = event.target;
    const product = {
        slug: button.dataset.slug,
        title: button.dataset.title,
        img: button.dataset.img,
        price: parseFloat(button.dataset.price) || 0, // Convertir el precio a número
        quantity: 1 // Inicializar cantidad
    };

    // 🚨 Validar que los datos no sean {slug}, {title}, etc.
    if (product.slug.includes("{") || product.title.includes("{")) {
        console.error("❌ Error: Los datos del producto no se están interpolando correctamente.");
        return;
    }

    let cart = getCart();
    cart.push(product);
    saveCart(cart);
    }

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', addToCart);
    });

    */
    cartButton.addEventListener('click', () => {
        checkoutModal.classList.remove('hidden');//Muestra la ventana de carrito
        checkoutOverlay.classList.remove('hidden'); // Muestra el overlay (sombreado de fondo)
    });

    continueShopping.addEventListener('click', () => {
        checkoutModal.classList.add('hidden');// Oculta la ventana de carrito
        checkoutOverlay.classList.add('hidden'); // Oculta el overlay (sombreado de fondo)
    });
    /*
    continuePago.addEventListener('click', () => {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];

        if (cart.length === 0) {
            alert("Tu carrito está vacío. Añade productos para comprar.");
            return;
        }

        window.location.href = "/pago";
    });
    */
   continuePago.addEventListener('click', () => {
    const cart = getCart();
        if (cart.length === 0) {
            showToast("Añade productos al carrito para continuar.");
            return;
        }

        sessionStorage.setItem('checkoutCart', JSON.stringify(cart));
        window.location.href = 'check-out.php';
    });

    function showToast(message) {
        const toast = document.createElement('div');
        toast.textContent = message;

        toast.className = `
            fixed bottom-5 left-1/2 transform -translate-x-1/2
            bg-gray-800 text-white text-sm px-5 py-3 rounded-lg shadow-lg
            z-[9999] transition-opacity duration-300 opacity-0
            pointer-events-none
        `;

        document.body.appendChild(toast);

        // Forzar reflow para que la transición funcione
        void toast.offsetWidth;
        toast.classList.add('opacity-100');

        // Ocultar y eliminar
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    // Cerrar el modal al hacer clic en el overlay
    // Esto permite que el usuario cierre la ventana de carrito haciendo clic fuera de ella
    checkoutOverlay.addEventListener("click", () => {
        checkoutModal.classList.add("hidden");
        checkoutOverlay.classList.add("hidden");
    });

    // Inicializar la UI del carrito
    updateCartUI();
});