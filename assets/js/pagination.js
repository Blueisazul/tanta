//Cuerpo del contenedor productos y paginación
document.addEventListener('DOMContentLoaded', () => {
    const productGrid = document.getElementById('product-grid');//Contenedor de productos
    const paginationContainer = document.getElementById('pagination');//paginación
    let allProducts = [];//Todos los productos
    let filteredProducts = [];//Productos filtrados
    const itemsPerPage = 9; // Cambia este valor para ajustar la cantidad de productos por p谩gina

    // 馃煛 Modal Simplificado y Mejorado
    const modalHTML = `
    <div id="productModal" class="fixed inset-0 z-50 hidden bg-black/70 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden relative flex flex-col">
            <!-- Botón de cerrar -->
            <button id="closeModal" class="absolute cursor-pointer top-4 right-4 z-10 p-2 text-neutral-500 hover:text-neutral-700 transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" 
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 lucide lucide-x-icon lucide-x">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
            
            <div class="flex flex-col lg:flex-row h-full overflow-y-auto">
                <!-- Sección de imagen con efecto zoom -->
                <div class="lg:w-1/2 bg-gray-50 p-8 flex flex-col">
                    <div class="flex-grow flex items-center justify-center relative group overflow-hidden">
                        <img id="modalImg" 
                             class="max-h-[400px] w-auto object-contain rounded-lg transition-all duration-300  cursor-crosshair" 
                             src="" 
                             alt=""
                             loading="lazy">
                    </div>
                </div>
                
                <!-- Sección de detalles -->
                <div class="lg:w-1/2 p-8 flex flex-col h-full">
                    <div class="flex-grow">
                        <!-- Encabezado -->
                        <span id="modalCategoria" class="text-xs font-semibold tracking-wider text-yellow-600 uppercase"></span>
                        <h2 id="modalTitle" class="text-3xl font-bold mt-2 text-gray-900"></h2>
                        
                        <!-- Precio -->
                        <div class="mt-8 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Precio actual:</h3>
                            <span class="text-lg font-bold text-gray-500">S/ <span id="modalPrice"></span></span>
                        </div>
                        
                        <!-- Descripción -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-800">Descripción</h3>
                            <p id="modalDescripcion" class="text-gray-700 mt-2 text-base leading-relaxed"></p>
                        </div>

                        <!-- Especificaciones -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-800 ">Especificaciones</h3>
                            <div class="grid grid-cols-2 gap-4 mt-3">
                                <div>
                                    <p class="text-sm text-gray-500">Código</p>
                                    <p id="modalSpecCode" class="text-teal-700 font-medium mt-1 text-xs flex items-center">
                                        <span class="bg-teal-100 rounded-full px-2 text-sm">
                                            <span class="mr-1">sku:</span>
                                            <span class="text-xs font-medium">#<span id="modalSKU"></span>
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Stock</p>
                                    <p id="modalSpecStock" class="text-gray-700 font-medium mt-1 flex items-center">
                                        <span id="stockBadge" class="px-2 py-1 rounded-full text-xs flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span id="stockText"></span>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="mt-8 pt-4 border-t border-gray-300">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-lg font-semibold text-gray-800">Total:</span>
                            <span class="text-lg font-bold text-gray-600"> 
                                S/ <span id="modalTotalPrice"></span>
                            </span>
                        </div>
                        <div class="flex flex-row gap-4 items-stretch">
                            <div class="flex items-center justify-center gap-2">
                                <button class="px-3 py-3 cursor-pointer bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg rounded-lg" id="decreaseQty">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class=" w-3 h-3 lucide lucide-minus-icon lucide-minus pointer-events-none"><path d="M5 12h14"/></svg>
                                </button>
                                <span class="px-3 py-2 bg-white text-gray-800 font-medium"
                                      id="productQuantity">1</span>
                                <button class="px-3 py-3 cursor-pointer bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg rounded-lg" id="increaseQty">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 lucide lucide-plus-icon lucide-plus pointer-events-none"><path d="M5 12h14"/><path d="M12 5v14"/></svg>                                        
                                </button>
                            </div>
                            <button id="modalAddToCart" 
                                    class="w-full flex cursor-pointer items-center justify-center gap-3 px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" 
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                        class="h-6 w-6 lucide lucide-shopping-cart-icon lucide-shopping-cart">
                                        <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                                    </svg>
                                Agregar al carrito 
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    let modalProduct = null; // Para guardar el producto actual en el modal

    // Función para abrir el modal actualizada
    function openProductModal(product) {
        // Configurar elementos b谩sicos
        document.getElementById('modalImg').src = product.img;
        document.getElementById('modalImg').alt = product.title;
        document.getElementById('modalTitle').textContent = product.title;
        document.getElementById('modalCategoria').textContent = product.categoria;
        document.getElementById('modalPrice').textContent = product.price.toFixed(2);
        document.getElementById('modalDescripcion').textContent = product.descripcion;

        // Configurar SKU y Stock
        document.getElementById('modalSKU').textContent = product.id;
        const stockText = product.stock > 0 ? `${product.stock} disponibles` : 'Agotado';
        const stockBadge = document.getElementById('stockBadge');
        document.getElementById('stockText').textContent = stockText;

        if (parseInt(product.stock) <= 0) {
            stockBadge.classList.remove('bg-green-100', 'text-green-800');
            stockBadge.classList.add('bg-red-100', 'text-red-800');
        } else {
            stockBadge.classList.remove('bg-red-100', 'text-red-800');
            stockBadge.classList.add('bg-green-100', 'text-green-800');
        }

        // Configurar cantidad y precio total
        document.getElementById('productQuantity').textContent = '1';
        const totalPrice = product.price * 1;
        document.getElementById('modalTotalPrice').textContent = totalPrice.toFixed(2);

        // Mostrar modal
        document.getElementById('productModal').classList.remove('hidden');
        modalProduct = product;

        // Configurar eventos para el zoom de imagen
        const imgElement = document.getElementById('modalImg');
        const zoomIcon = document.createElement('div');
        zoomIcon.id = 'zoomIcon';
        zoomIcon.style.position = 'absolute';
        zoomIcon.style.top = '16px';
        zoomIcon.style.right = '16px';
        zoomIcon.style.background = 'rgba(255,255,255,0.8)';
        zoomIcon.style.borderRadius = '50%';
        zoomIcon.style.padding = '6px';
        zoomIcon.style.cursor = 'pointer';
        zoomIcon.innerHTML = `
            <svg id="zoomInIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <circle cx="11" cy="11" r="8" stroke-width="2"/>
              <path d="M21 21l-4.35-4.35" stroke-width="2"/>
              <path d="M11 8v6M8 11h6" stroke-width="2"/>
            </svg>
            <svg id="zoomOutIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <circle cx="11" cy="11" r="8" stroke-width="2"/>
              <path d="M21 21l-4.35-4.35" stroke-width="2"/>
              <path d="M8 11h6" stroke-width="2"/>
            </svg>
        `;
        imgElement.parentElement.appendChild(zoomIcon);

        let isZoomed = false;
        let origin = { x: 50, y: 50 };

        function setZoom(state, x = 50, y = 50) {
            isZoomed = state;
            origin = { x, y };
            imgElement.style.transition = "transform 0.4s, transform-origin 0.1s";
            if (isZoomed) {
                imgElement.style.transform = "scale(2)";
                imgElement.style.transformOrigin = `${x}% ${y}%`;
                document.getElementById('zoomInIcon').classList.add('hidden');
                document.getElementById('zoomOutIcon').classList.remove('hidden');
                imgElement.classList.add('cursor-zoom-out');
                imgElement.classList.remove('cursor-zoom-in');
            } else {
                imgElement.style.transform = "scale(1)";
                imgElement.style.transformOrigin = "50% 50%";
                document.getElementById('zoomInIcon').classList.remove('hidden');
                document.getElementById('zoomOutIcon').classList.add('hidden');
                imgElement.classList.remove('cursor-zoom-out');
                imgElement.classList.add('cursor-zoom-in');
            }
        }

        // Inicializa el cursor
        imgElement.classList.add('cursor-zoom-in');

        // Click en la imagen o en el icono para alternar zoom
        imgElement.onclick = (e) => {
            if (!isZoomed) {
                // Calcula el punto de origen del zoom según el click
                const rect = imgElement.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                setZoom(true, x, y);
            } else {
                setZoom(false);
            }
        };
        zoomIcon.onclick = () => setZoom(!isZoomed);

        // Permite mover el zoom focalizado mientras está activado
        imgElement.onmousemove = (e) => {
            if (isZoomed) {
                const rect = imgElement.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                imgElement.style.transformOrigin = `${x}% ${y}%`;
            }
        };

        // Al salir de la imagen, no cambia el zoom (solo si quieres puedes desactivar aquí)
        // imgElement.onmouseleave = () => { if (isZoomed) setZoom(false); };

        // Configurar eventos para cantidad

        document.getElementById('increaseQty').onclick = () => {
           const quantityElement = document.getElementById('productQuantity');
           let quantity = parseInt(quantityElement.textContent);
            if (quantity < (product.stock || 10)) {
                quantity++;
               quantityElement.textContent = quantity;
               document.getElementById('modalTotalPrice').textContent = (product.price * quantity).toFixed(2);
            }
        };

        document.getElementById('decreaseQty').onclick = () => {
            const quantityElement = document.getElementById('productQuantity');
            let quantity = parseInt(quantityElement.textContent);

            if (quantity > 1) {
                quantity--;
                quantityElement.textContent = quantity;
                document.getElementById('modalTotalPrice').textContent = (product.price * quantity).toFixed(2);
            }
        };

        const addToCartBtn = document.getElementById('modalAddToCart');
        if (parseInt(product.stock) <= 0) {
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('bg-gray-300', 'cursor-not-allowed', 'opacity-60');
            addToCartBtn.classList.remove('bg-gradient-to-r', 'from-yellow-500', 'to-yellow-600', 'hover:from-yellow-600', 'hover:to-yellow-700');
            addToCartBtn.textContent = 'No disponible';
        } else {
            addToCartBtn.disabled = false;
            addToCartBtn.classList.remove('bg-gray-300', 'cursor-not-allowed', 'opacity-60');
            addToCartBtn.classList.add('bg-gradient-to-r', 'from-yellow-500', 'to-yellow-600', 'hover:from-yellow-600', 'hover:to-yellow-700');
            addToCartBtn.textContent = 'Agregar al carrito';
        }
    } 


    // 馃煛 Función para renderizar los productos en el grid
    const renderProducts = (products, page = 1) => {
        productGrid.innerHTML = '';
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedProducts = products.slice(start, end);

        //Cuerpo del contenedor productos
        paginatedProducts.forEach(product => {
            const productCard = `
                <article class="group bg-gradient-to-bl from-blue-50/70 to-white border border-stone-200 p-8 rounded-xl shadow-sm text-center hover:shadow-lg">
                    <p class="text-lg tracking-wide text-gray-700 font-semibold">${product.Nombre_Producto}</p>
                    <p class="mt-1 text-xs tracking-wider text-gray-400 font-semibold">${product.Descripcion_Categoria.toUpperCase()}</p>
                    <div 
                        class="py-6 flex items-center justify-center transition-transform duration-300 ease-in-out group-hover:scale-105 cursor-pointer open-modal"
                        data-id="${product.Id_Producto}"
                        data-stock="${product.Stock}"
                        data-slug="${product.Nombre_Producto}"
                        data-title="${product.Nombre_Producto}"
                        data-categoria="${product.Descripcion_Categoria}"
                        data-img="Admin/${product.Imagen}"
                        data-price="${product.Precio_Actual}"
                        data-descripcion="${product.Descripcion_Producto}"
                    >
                        <img class="h-32 w-96 object-cover" src="Admin/${product.Imagen}" alt="${product.Nombre_Producto}" />
                    </div>
                    <p class="text-gray-700 text-xl tracking-wide">S/ ${product.Precio_Actual}</p>
                    <div class="py-8">
                        <span class="text-sm text-neutral-500 font-medium"> Cantidad: ${product.Stock > 0 ? product.Stock : '<span class="text-red-600 font-bold">Agotado</span>'}</span>
                        <div class="top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent from-10% via-gray-300 to-transparent to-90%"></div>
                    </div>
                    <button 
                        class="px-7 py-2 border rounded-full transition-all duration-300 ease-in-out ${
                            product.Stock > 0 
                            ? 'border-yellow-600 text-yellow-600 hover:bg-transparent hover:border-yellow-700/80 hover:bg-gradient-to-r hover:from-yellow-600 hover:to-yellow-700/80 hover:bg-clip-text hover:text-transparent hover:scale-103 cursor-pointer add-to-cart'
                            : 'border-gray-300 text-gray-400 cursor-not-allowed bg-gray-100'
                        }"
                        ${product.Stock <= 0 ? 'disabled' : ''}
                        data-id="${product.Id_Producto}"
                        data-stock="${product.Stock}"
                        data-slug="${product.Nombre_Producto}"
                        data-title="${product.Nombre_Producto}"
                        data-categoria="${product.Descripcion_Categoria}"
                        data-img="Admin/${product.Imagen}"
                        data-price="${product.Precio_Actual}"
                        data-descripcion="${product.Descripcion_Producto}"
                    >
                        ${product.Stock > 0 ? 'AGREGAR' : 'NO DISPONIBLE'}
                    </button>
                </article>
            `;
            productGrid.innerHTML += productCard;
        });

        renderPagination(products.length, page);
    };

    // 馃煛 Función para renderizar la paginación
    const renderPagination = (totalItems, currentPage) => {
        paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.className = `px-4 py-2 border cursor-pointer ${i === currentPage ?  'bg-linear-to-bl from-yellow-600/70 to-yellow-500 text-white' : 'bg-white text-yellow-500'} rounded `;
            button.addEventListener('click', () => renderProducts(filteredProducts, i));
            paginationContainer.appendChild(button);
        }
    };
    
    // 馃煛 Función para obtener el rango de precios seg煤n el filtro
    function getPriceRange(filterValue) {
        switch (filterValue) {
            case 'BAJO':
                return { min: 0, max: 5 };
            case 'LEVE':
                return { min: 5, max: 10 };
            case 'ALTO':
                return { min: 10, max: 25 };
            case 'MUCHO':
                return { min: 25, max: Infinity };
            default:
                return null;
        }
    }
    

    // Función para accionar el filtrado de productos
    productGrid.addEventListener('filterProducts', (event) => {
        const { categories, prices } = event.detail;// , prices

        filteredProducts = allProducts.filter(product => {
            const matchesCategory = !categories.length || categories.includes(product.Descripcion_Categoria);
            //const matchesPrice = !prices.length || prices.includes(product.Precio_Actual);
             /*
            const matchesPortion = !portions.length || portions.includes(product.porcion);
            const matchesDelivery = !deliveries.length || deliveries.includes(product.entrega);
            const matchesPreference = !preferences.length || preferences.includes(product.preferencia);
            */

             // Filtrado por rango de precios
            let matchPrice = true;
            if (prices.length) {
                matchPrice = prices.some(priceFilter => {
                    const range = getPriceRange(priceFilter);
                    return product.Precio_Actual >= range.min && product.Precio_Actual < range.max;
                });
            }

            return matchesCategory && matchPrice ; // && matchesPrice && matchesPortion && matchesDelivery && matchesPreference;
        });

        renderProducts(filteredProducts, 1);
    });

    // Abrir modal de producto
    // Agregar producto al carrito
    // Se da click en el botón "Agregar al carrito" => Desde las tarjetas de productos
    productGrid.addEventListener('click', (event) => {
        const addButton = event.target.closest('.add-to-cart');

        if (addButton) {
            const producto = {
                id: addButton.dataset.id,
                stock: addButton.dataset.stock,
                slug: addButton.dataset.slug,
                title: addButton.dataset.title,
                categoria: addButton.dataset.categoria,
                img: addButton.dataset.img,
                price: parseFloat(addButton.dataset.price),
                descripcion: addButton.dataset.descripcion || 'Sin descripción.'
            };

            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const existingProduct = cart.find(item => item.slug === producto.slug);

            if (existingProduct) {
                // Incrementar la cantidad si el producto ya existe
                existingProduct.quantity = (existingProduct.quantity || 1) + 1;
            } else {
                // Agregar el producto con cantidad inicial 1
                producto.quantity = 1;
                cart.push(producto);
            }

            // Guardar el carrito actualizado en localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            if (typeof updateCartUI === 'function') updateCartUI();
        }

        // Se dio click en el contenedor del producto (imagen) se abre el modal
        const modalTarget = event.target.closest('.open-modal');

        if (modalTarget) {
            const producto = {
                id: modalTarget.dataset.id,
                stock: modalTarget.dataset.stock,
                slug: modalTarget.dataset.slug,
                title: modalTarget.dataset.title,
                categoria: modalTarget.dataset.categoria,
                img: modalTarget.dataset.img,
                price: parseFloat(modalTarget.dataset.price),
                descripcion: modalTarget.dataset.descripcion || 'Sin descripción.'
            };
            openProductModal(producto);

            document.getElementById('modalImg').src = producto.img;
            document.getElementById('modalTitle').textContent = producto.title;
            document.getElementById('modalDescripcion').innerHTML = "Descripción:<br>" + producto.descripcion;

            const stockElemento = document.getElementById('stockProducto');
            const botonAgregar = document.getElementById('modalAddToCart');

            if (parseInt(producto.stock) <= 0 ) {
                stockElemento.innerHTML = `Cantidad: <span class="text-red-600 font-bold">Agotado</span>`;
                botonAgregar.disabled = true;
                botonAgregar.classList.add('bg-gray-300', 'cursor-not-allowed');
                botonAgregar.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                botonAgregar.textContent = 'No disponible';
            } else {
                stockElemento.textContent = "Cantidad: " + producto.stock;
                botonAgregar.disabled = false;
                botonAgregar.classList.remove('bg-gray-300', 'cursor-not-allowed');
                botonAgregar.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                botonAgregar.textContent = 'Agregar al Carrito';
            }

            document.getElementById('productModal').classList.remove('hidden');

            /*
            document.getElementById('modalImg').src = producto.img;
            document.getElementById('modalTitle').textContent = producto.title;
            document.getElementById('modalDescripcion').innerHTML ="Descripción:<br>"+ producto.descripcion;
            document.getElementById('stockProducto').textContent = "Cantidad: " + producto.stock;
            document.getElementById('productModal').classList.remove('hidden');// Mostrar el modal
            */
        }
    });


    // Cerrar modal, detalle del producto
    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('productModal').classList.add('hidden');
        modalProduct = null;// Limpiar el producto modal
    });

    // Agregar producto al carrito desde el modal/ ventana detalles producto
    document.getElementById('modalAddToCart').addEventListener('click', () => {
        if (!modalProduct) return;

        //const quantity = parseInt(document.getElementById('productQuantity').textContent) || 1; 
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const existingProduct = cart.find(item => item.slug === modalProduct.slug);

        if (existingProduct) {
            // Incrementar la cantidad si el producto ya existe
            existingProduct.quantity = (existingProduct.quantity || 1) + 1;
        } else {
            // Agregar el producto con cantidad inicial 1
            modalProduct.quantity = 1;
            cart.push(modalProduct);
        }
        // Guardar el carrito actualizado en localStorage
        localStorage.setItem('cart', JSON.stringify(cart));

        // Actualizar la UI del carrito si es necesario
        if (typeof updateCartUI === 'function') {
            updateCartUI();
        }

        // Cerrar modal tras agregar
        document.getElementById('productModal').classList.add('hidden');
        modalProduct = null;// Limpiar el producto modal
    });

    // Inicializar productos

        fetch('productos_catalogo.php')
        .then(response => response.json())
        .then(data => {
          allProducts = data;
          filteredProducts = data;
          renderProducts(data, 1);
        })
        .catch(error => {
          console.error('Error al cargar productos:', error);
        });
      


    // Filtrar productos al cambiar el formulario de filtros
    //Se recolectan los datos de los checkboxes (filtros)
    //Escuchar el evento de cambio en el formulario de filtros
    document.getElementById('filter-form').addEventListener('change', () => {
        //Si se hace seleccionado un checkbox de categor铆a
        const categories = Array.from(document.querySelectorAll('.category-filter:checked')).map(input => input.value);
        const prices = Array.from(document.querySelectorAll('.price-filter:checked')).map(input => input.value);

        console.log(categories);
        console.log(prices);
        const filterEvent = new CustomEvent('filterProducts', {
            detail: { categories, prices } //, portions, deliveries, preferences }
        });

        productGrid.dispatchEvent(filterEvent);
    });
});