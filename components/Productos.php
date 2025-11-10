<div class="flex flex-row max-w-screen-xl mx-auto mt-5 relative">
    <!-- Línea con degradado -->
    <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>

    <div class="w-1/5 mx-auto mt-10 pl-4 pr-2">
        <aside>
            <div>
              <form id="filter-form" class="">
                <fieldset class="">
                  <!-- Categoría -->
                  <legend class="text-md font-medium mb-2">Categoría</legend>
                  <div class="flex flex-col space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 category-filter" value="Bollería">
                      <span>Bollería</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 category-filter" value="Dulces">
                      <span>Dulces</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 category-filter" value="Especiales">
                      <span>Especiales</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 category-filter" value="Pan artesanal">
                      <span>Pan artesanal</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 category-filter" value="Pan saludable">
                      <span>Pan saludable</span>
                    </label>
                  </div>
                </fieldset>

                <!-- Línea con degradado --> 
                <div class="py-10">
                  <div class="top-0 left-0 w-full h-[0.5px] bg-gray-300"></div>
                </div>

                <!-- Precios -->
                <fieldset class="">
                  <legend class="text-md font-medium mb-2">Precios</legend>
                  <div class="flex flex-col space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 price-filter" value="BAJO">
                      <span>Menos de S/ 5</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 price-filter" value="LEVE">
                      <span>S/ 5 - S/ 10</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 price-filter" value="ALTO">
                      <span>S/ 10 - S/ 25</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 price-filter" value="MUCHO">
                      <span>Más de S/ 25</span>
                    </label>
                  </div>
                </fieldset>

                <!-- Línea con degradado --> 
                <div class="py-10">
                  <div class="top-0 left-0 w-full h-[0.5px] bg-gray-300"></div>
                </div>

                <!-- Porciones -->
                <fieldset class="">
                  <legend class="text-md font-medium mb-2">Porciones</legend>
                  <div class="flex flex-col space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 portion-filter" value="INDIVIDUAL">
                      <span>Individual</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 portion-filter" value="MEDIANO">
                      <span>Mediano</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 portion-filter" value="FAMILIAR">
                      <span>Familiar</span>
                    </label>
                  </div>
                </fieldset>

                <!-- Línea con degradado --> 
                <div class="py-10">
                  <div class="top-0 left-0 w-full h-[0.5px] bg-gray-300"></div>
                </div>

                <!-- Entrega -->
                <fieldset class="">
                  <legend class="text-md font-medium mb-2">Tipo de Entrega</legend>
                  <div class="flex flex-col space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 delivery-filter" value="RETIRO">
                      <span>Retiro en tienda</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 delivery-filter" value="DELIVERY">
                      <span>Entrega a domicilio</span>
                    </label>
                  </div>
                </fieldset>   
                
                <!-- Línea con degradado --> 
                <div class="py-10">
                  <div class="top-0 left-0 w-full h-[0.5px] bg-gray-300"></div>
                </div>

                <!-- Preferencias -->
                <fieldset class="">
                  <legend class="text-md font-medium mb-2">Preferencias</legend>
                  <div class="flex flex-col space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 preference-filter" value="VEGAN">
                      <span>Vegano</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 cursor-pointer preference-filter" value="GLUTEN-FREE">
                      <span>Sin gluten</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                      <input type="checkbox" class="w-4 h-4 accent-amber-400 cursor-pointer preference-filter" value="LACTOSE-FREE">
                      <span>Sin lactosa</span>
                    </label>                    
                  </div>
                </fieldset>                
              </form>
            </div>
        </aside>
    </div>

    <!-- Grid Productos-->
    <section class="w-4/5 mx-auto px-14 max-w-screen-xl mt-10">
        <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-10 md:gap-6 lg:gap-10">
            <!-- Productos renderizados dinámicamente -->
        </div>
        <!-- Contenedor de paginación -->
        <div id="pagination" class="flex justify-center mt-10 space-x-2">
            <!-- Botones de paginación generados dinámicamente -->
        </div>
    </section>

    <!-- Importar los scripts -->
     <!--
    <script src="/js/category.js" defer></script>
    <script src="/js/pagination.js" defer></script>
    <script src="/cart.js" defer></script>
-->
</div>