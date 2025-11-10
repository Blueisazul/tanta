<!-- ---
import Croissant1 from "/public/croissant1.png";
import Categoria1 from "/public/categoria1.png";
import Categoria2 from "/public/categoria_2.png";
import Categoria3 from "/public/categoria_3.png";
import Categoria4 from "/public/categoria_4.png";
import Categoria5 from "/public/categoria_5.png";
import Milhojas from "/public/milhojas.png";
import Tarta_Manzana from "/public/tarta_manzana.png";
import Pan_Integral from "/public/pan_integral.png";
import Torta_Arandano from "/public/torta_arandano.png";
import Red_Velvet from "/public/red_velvet.png";
import Empanada from "/public/empanada.png";
import Banner1 from "/public/banner1.png";
import Banner2 from "/public/banner2.png";
import Banner3 from "/public/banner3.png";
import Anuncio1 from "/public/anuncio1.png";
import Anuncio2 from "/public/anuncio2.png";
import Logo1 from "/public/logo1.png";
import Logo3 from "/public/logo3.png";
import Logo5 from "/public/logo5.png";
import Logo7 from "/public/logo7.png";
import Logo9 from "/public/logo9.png";
import Color1 from "/public/color1.png";
import Color2 from "/public/color2.png";
import Color3 from "/public/color3.png";
import Color4 from "/public/color4.png";
import Color5 from "/public/color5.png";


import Anuncio3 from "/public/anuncio3.png";
--- -->




<div class="max-w-screen-xl mx-auto max-w-screen-xl flex flex-col gap-6 -z-10">
  <!-- Seccion Portada -->
  <div class="flex flex-col lg:flex-row min-h-[80vh]">
    <!-- Texto 1 -->
    <section
      class="group pt-4 bg-cover bg-center content-center text-left md:p-20 text-gray-600 rounded-lg flex-1">
      <h1 class="cursor-pointer leading-tight tracking-wide text-5xl text-zinc-800 font-extrabold">
        <span class="relative inline-block before:content-[''] before:absolute before:bottom-0 before:left-0 before:w-full before:h-1/3 before:bg-gradient-to-r before:from-yellow-500 before:to-amber-500">
          <span class="relative text-zinc-800 ">
            No es solo pan</span>
        </span>, es una experiencia para los sentidos
      </h1>

      <h2 class="cursor-pointer py-6 text-zinc-700 leading-normal tracking-wide text-2xl font-semibold mt-2">
        Cada capa es un susurro de sabor y cada mordida, un romance inolvidable
      </h2>

      <div class="flex text-xl font-medium items-center mt-4">
        <button
          class="cursor-pointer text-amber-500 group-hover:text-orange-500 transition flex items-center">
            Pruébalo y siente la diferencia
          <div class="transition-transform transform group-hover:translate-x-2">
            <svg
              xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right"
            >
              <path d="m9 18 6-6-6-6"></path>
            </svg>
          </div>
        </button>
      </div>
    </section>

    <!-- Imagen Pan -->
    <section
      class="py-0 relative bg-cover bg-center text-left p-10 md:p-20 flex-1"
    >
      <div
        class="cursor-pointer absolute z-20 inset-0 flex items-center transition justify-center duration-300 hover:scale-105"
      >
        <img
          src="assets/croissant1.png"
          alt="Croissant"
          class="max-w-full max-h-full object-contain"
        />
      </div>
      <div
        class="relative z-10 bg-gradient-to-bl from-yellow-400 from-10% via-orange-500 via-70% to-red-600 to-100% bg-clip-text text-transparent text-center flex flex-col items-center content-between"
      >
        <div class="text-9xl font-bold">NUEVO</div>
        <div class="text-9xl font-bold">NUEVO</div>
        <div class="text-9xl font-bold">NUEVO</div>
        <div class="pt-6 text-2xl text-center text-gray-800">Ya Disponible</div>
      </div>
    </section>
  </div>

    <!-- Seccion 3 columnas -->
  <div class="py-10 px-4 max-w-screen-xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    
    <!-- Sección Productos -->
    <div class="bg-white px-6 py-8 rounded-lg text-center shadow-md hover:shadow-lg transition">
      <div class="flex justify-center mb-4 text-amber-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-croissant">
          <path d="m4.6 13.11 5.79-3.21c1.89-1.05 4.79 1.78 3.71 3.71l-3.22 5.81C8.8 23.16.79 15.23 4.6 13.11Z"></path>
          <path d="m10.5 9.5-1-2.29C9.2 6.48 8.8 6 8 6H4.5C2.79 6 2 6.5 2 8.5a7.71 7.71 0 0 0 2 4.83"></path>
          <path d="M8 6c0-1.55.24-4-2-4-2 0-2.5 2.17-2.5 4"></path>
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-800 mb-2">Nuestros Productos</h2>
      <p class="text-gray-500">Descubre nuestra variedad de panes, postres y pasteles artesanales.</p>
    </div>

    <!-- Sección Delivery -->
    <div class="bg-white px-6 py-8 rounded-lg text-center shadow-md hover:shadow-lg transition">
      <div class="flex justify-center mb-4 text-amber-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck">
          <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
          <path d="M15 18H9"></path>
          <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path>
          <circle cx="17" cy="18" r="2"></circle>
          <circle cx="7" cy="18" r="2"></circle>
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-800 mb-2">Delivery</h2>
      <p class="text-gray-500">Recibe tu pan fresco en casa con nuestro servicio de reparto.</p>
    </div>

    <!-- Sección Ingredientes y Calidad -->
    <div class="bg-white px-6 py-8 rounded-lg text-center shadow-md hover:shadow-lg transition">
      <div class="flex justify-center mb-4 text-amber-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wheat">
          <path d="M2 22 16 8"></path>
          <path d="M3.47 12.53 5 11l1.53 1.53a3.5 3.5 0 0 1 0 4.94L5 19l-1.53-1.53a3.5 3.5 0 0 1 0-4.94Z"></path>
          <path d="M7.47 8.53 9 7l1.53 1.53a3.5 3.5 0 0 1 0 4.94L9 15l-1.53-1.53a3.5 3.5 0 0 1 0-4.94Z"></path>
          <path d="M11.47 4.53 13 3l1.53 1.53a3.5 3.5 0 0 1 0 4.94L13 11l-1.53-1.53a3.5 3.5 0 0 1 0-4.94Z"></path>
          <path d="M20 2h2v2a4 4 0 0 1-4 4h-2V6a4 4 0 0 1 4-4Z"></path>
          <path d="M11.47 17.47 13 19l-1.53 1.53a3.5 3.5 0 0 1-4.94 0L5 19l1.53-1.53a3.5 3.5 0 0 1 4.94 0Z"></path>
          <path d="M15.47 13.47 17 15l-1.53 1.53a3.5 3.5 0 0 1-4.94 0L9 15l1.53-1.53a3.5 3.5 0 0 1 4.94 0Z"></path>
          <path d="M19.47 9.47 21 11l-1.53 1.53a3.5 3.5 0 0 1-4.94 0L13 11l1.53-1.53a3.5 3.5 0 0 1 4.94 0Z"></path>
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-800 mb-2">Ingredientes y Calidad</h2>
      <p class="text-gray-500">Utilizamos ingredientes naturales para brindarte el mejor sabor y frescura.</p>
    </div>

  </div>

  <!-- Sección Categorías Responsive -->
  <div class="pt-4 grid gap-4 h-auto lg:h-[500px] 
              grid-cols-1 
              sm:grid-cols-2 
              lg:grid-cols-7 
              lg:grid-rows-6">

    <!-- Categoría 1 -->
    <div class="shadow-sm cursor-pointer col-span-1 sm:col-span-2 lg:col-span-2 lg:row-span-6 bg-gradient-to-bl from-sky-200 to-sky-300/80 hover:bg-sky-400 rounded-lg flex group relative overflow-hidden">
      <img src="assets/categoria1.png" alt="Baguette" class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" />
      <div class="py-14 px-6 sm:px-10 relative z-10 flex flex-col place-items-start">
        <div class="text-2xl font-semibold text-black">Bollería y viennoiserie</div>
        <button class="group-hover:bg-white group-hover:text-black bg-emerald-400 text-white rounded-lg px-4 py-2 mt-4 transition duration-300">
          Descubre más
        </button>
      </div>
    </div>

    <!-- Categoría 2 -->
    <div class="shadow-sm cursor-pointer col-span-1 sm:col-span-2 lg:col-span-3 lg:row-span-3 bg-gradient-to-bl from-amber-200 to-amber-300/80 hover:bg-amber-400 rounded-lg flex group relative overflow-hidden">
      <img src="assets/categoria_2.png" alt="Panes" class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" />
      <div class="py-10 px-6 sm:px-10 relative z-10 flex flex-col place-items-start">
        <div class="text-2xl font-semibold text-black">Panes artesanales</div>
        <button class="group-hover:bg-white group-hover:text-black bg-emerald-400 text-white rounded-lg px-4 py-2 mt-4 transition duration-300">
          Descubre más
        </button>
      </div>
    </div>

    <!-- Categoría 3 -->
    <div class="shadow-sm cursor-pointer col-span-1 sm:col-span-2 lg:col-span-2 lg:row-span-3 bg-gradient-to-bl from-fuchsia-200 to-fuchsia-300/80 hover:bg-fuchsia-400 rounded-lg flex group relative overflow-hidden">
      <img src="assets/categoria_4.png" alt="Especiales" class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" />
      <div class="py-10 px-6 sm:px-10 relative z-10 flex flex-col place-items-start">
        <div class="text-2xl font-semibold text-black">Ediciones especiales</div>
        <button class="group-hover:bg-white group-hover:text-black bg-emerald-400 text-white rounded-lg px-4 py-2 mt-4 transition duration-300">
          Descubre más
        </button>
      </div>
    </div>

    <!-- Categoría 4 -->
    <div class="shadow-sm cursor-pointer col-span-1 sm:col-span-2 lg:col-span-2 lg:row-span-4 bg-gradient-to-bl from-rose-200 to-rose-300/80 hover:bg-rose-400 rounded-lg flex group relative overflow-hidden">
      <img src="assets/categoria_5.png" alt="Dulces" class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" />
      <div class="py-10 px-6 sm:px-10 relative z-10 flex flex-col place-items-start">
        <div class="text-2xl font-semibold text-black">Dulces y pasteles</div>
        <button class="group-hover:bg-white group-hover:text-black bg-emerald-400 text-white rounded-lg px-4 py-2 mt-4 transition duration-300">
          Descubre más
        </button>
      </div>
    </div>

    <!-- Categoría 5 -->
    <div class="shadow-sm cursor-pointer col-span-1 sm:col-span-2 lg:col-span-3 lg:row-span-4 bg-gradient-to-bl from-green-200 to-green-300/80 hover:bg-green-400 rounded-lg flex group relative overflow-hidden">
      <img src="assets/categoria_3.png" alt="Saludable" class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" />
      <div class="py-10 px-6 sm:px-10 relative z-10 flex flex-col place-items-start">
        <div class="text-2xl font-semibold text-black">Panadería saludable</div>
        <button class="group-hover:bg-white group-hover:text-black bg-emerald-400 text-white rounded-lg px-4 py-2 mt-4 transition duration-300">
          Descubre más
        </button>
      </div>
    </div>

  </div>


  <!-- Sección Productos Destacados -->
  <div class="flex items-center justify-center my-12 px-4 sm:px-6 lg:px-0">
    <div class="w-full max-w-3xl text-center">
      <span class="relative inline-block before:content-[''] before:absolute before:bottom-1 before:left-0 before:w-full before:h-1/5 before:bg-gradient-to-r before:from-transparent before:via-pink-400/70 before:to-violet-400/70">
        <span class="relative text-xl font-medium text-gray-600 whitespace-nowrap">
          Productos 
          <span class="font-bold text-gray-700">Destacados</span>
        </span>
      </span>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 sm:px-6 lg:px-0 pb-12">
    <!-- Producto 1 -->
    <div class="group relative overflow-hidden bg-gradient-to-r from-orange-400 to-orange-500 rounded-lg shadow-lg cursor-pointer transition duration-300 hover:from-orange-400/90 hover:to-orange-500/90">
      <svg class="absolute bottom-0 left-0 mb-8" viewBox="0 0 375 283" fill="none" style="transform: scale(1.5); opacity: 0.1;">
        <rect x="159.52" y="175" width="152" height="152" rx="8" transform="rotate(-45 159.52 175)" fill="white"/>
        <rect y="107.48" width="152" height="152" rx="8" transform="rotate(-45 0 107.48)" fill="white"/>
      </svg>
      <div class="relative pt-6 pb-2 px-5 flex items-center justify-center">
        <img class="relative w-full max-w-[240px] h-auto object-cover transition-transform duration-300 transform group-hover:scale-110" src="assets/milhojas.png" alt="Milhojas">
      </div>
      <div class="relative text-white px-6 pb-6">
        <span class="block opacity-75 -mb-1">Bollería • 1 un</span>
        <div class="flex justify-between items-center">
          <span class="block font-semibold text-xl">Milhojas</span>
          <span class="block bg-white rounded-full text-orange-500 text-xs font-bold px-3 py-2">S/ 6.00</span>
        </div>
      </div>
    </div>

    <!-- Producto 2 -->
    <div class="group relative overflow-hidden bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-lg shadow-lg cursor-pointer transition duration-300 hover:from-emerald-400/90 hover:to-emerald-500/90">
      <svg class="absolute bottom-0 left-0 mb-8" viewBox="0 0 375 283" fill="none" style="transform: scale(1.5); opacity: 0.1;">
        <rect x="159.52" y="175" width="152" height="152" rx="8" transform="rotate(-45 159.52 175)" fill="white"/>
        <rect y="107.48" width="152" height="152" rx="8" transform="rotate(-45 0 107.48)" fill="white"/>
      </svg>
      <div class="relative pt-6 pb-2 px-5 flex items-center justify-center">
        <img class="relative w-full max-w-[240px] h-auto object-cover transition-transform duration-300 transform group-hover:scale-110" src="assets/tarta_manzana.png" alt="Tarta de Manzana">
      </div>
      <div class="relative text-white px-6 pb-6">
        <span class="block opacity-75 -mb-1">Repostería • 1 un</span>
        <div class="flex justify-between items-center">
          <span class="block font-semibold text-xl">Tarta de Manzana</span>
          <span class="block bg-white rounded-full text-emerald-500 text-xs font-bold px-3 py-2">S/ 8.00</span>
        </div>
      </div>
    </div>

    <!-- Producto 3 -->
    <div class="group relative overflow-hidden bg-gradient-to-r from-stone-400 to-stone-500 rounded-lg shadow-lg cursor-pointer transition duration-300 hover:from-stone-400/90 hover:to-stone-500/90">
      <svg class="absolute bottom-0 left-0 mb-8" viewBox="0 0 375 283" fill="none" style="transform: scale(1.5); opacity: 0.1;">
        <rect x="159.52" y="175" width="152" height="152" rx="8" transform="rotate(-45 159.52 175)" fill="white"/>
        <rect y="107.48" width="152" height="152" rx="8" transform="rotate(-45 0 107.48)" fill="white"/>
      </svg>
      <div class="relative pt-6 pb-2 px-5 flex items-center justify-center">
        <img class="relative w-full max-w-[240px] h-auto object-cover transition-transform duration-300 transform group-hover:scale-110" src="assets/pan_integralok.png" alt="Pan Integral">
      </div>
      <div class="relative text-white px-6 pb-6">
        <span class="block opacity-75 -mb-1">Saludable • 5 un</span>
        <div class="flex justify-between items-center">
          <span class="block font-semibold text-xl">Pan Integral</span>
          <span class="block bg-white rounded-full text-stone-500 text-xs font-bold px-3 py-2">S/ 6.00</span>
        </div>
      </div>
    </div>

    <!-- Producto 4 -->
    <div class="group relative overflow-hidden bg-gradient-to-r from-indigo-400 to-indigo-500 rounded-lg shadow-lg cursor-pointer transition duration-300 hover:from-indigo-400/90 hover:to-indigo-500/90">
      <svg class="absolute bottom-0 left-0 mb-8" viewBox="0 0 375 283" fill="none" style="transform: scale(1.5); opacity: 0.1;">
        <rect x="159.52" y="175" width="152" height="152" rx="8" transform="rotate(-45 159.52 175)" fill="white"/>
        <rect y="107.48" width="152" height="152" rx="8" transform="rotate(-45 0 107.48)" fill="white"/>
      </svg>
      <div class="relative pt-6 pb-2 px-5 flex items-center justify-center">
        <img class="relative w-full max-w-[240px] h-auto object-cover transition-transform duration-300 transform group-hover:scale-110" src="assets/torta_arandano.png" alt="Pastel de Arándanos">
      </div>
      <div class="relative text-white px-6 pb-6">
        <span class="block opacity-75 -mb-1">Repostería • 1 un</span>
        <div class="flex justify-between items-center">
          <span class="block font-semibold text-xl">Pastel de Arándanos</span>
          <span class="block bg-white rounded-full text-indigo-500 text-xs font-bold px-3 py-2">S/ 8.00</span>
        </div>
      </div>
    </div>

    <!-- Producto 5 -->
    <div class="group relative overflow-hidden bg-gradient-to-r from-rose-400 to-rose-500 rounded-lg shadow-lg cursor-pointer transition duration-300 hover:from-rose-400/90 hover:to-rose-500/90">
      <svg class="absolute bottom-0 left-0 mb-8" viewBox="0 0 375 283" fill="none" style="transform: scale(1.5); opacity: 0.1;">
        <rect x="159.52" y="175" width="152" height="152" rx="8" transform="rotate(-45 159.52 175)" fill="white"/>
        <rect y="107.48" width="152" height="152" rx="8" transform="rotate(-45 0 107.48)" fill="white"/>
      </svg>
      <div class="relative pt-6 pb-2 px-5 flex items-center justify-center">
        <img class="relative w-full max-w-[240px] h-auto object-cover transition-transform duration-300 transform group-hover:scale-110" src="assets/red_velvet.png" alt="Red Velvet">
      </div>
      <div class="relative text-white px-6 pb-6">
        <span class="block opacity-75 -mb-1">Repostería • 1 un</span>
        <div class="flex justify-between items-center">
          <span class="block font-semibold text-xl">Pastel Red Velvet</span>
          <span class="block bg-white rounded-full text-rose-500 text-xs font-bold px-3 py-2">S/ 8.00</span>
        </div>
      </div>
    </div>

    <!-- Producto 6 -->
    <div class="group relative overflow-hidden bg-gradient-to-r from-amber-400 to-amber-500 rounded-lg shadow-lg cursor-pointer transition duration-300 hover:from-amber-400/90 hover:to-amber-500/90">
      <svg class="absolute bottom-0 left-0 mb-8" viewBox="0 0 375 283" fill="none" style="transform: scale(1.5); opacity: 0.1;">
        <rect x="159.52" y="175" width="152" height="152" rx="8" transform="rotate(-45 159.52 175)" fill="white"/>
        <rect y="107.48" width="152" height="152" rx="8" transform="rotate(-45 0 107.48)" fill="white"/>
      </svg>
      <div class="relative pt-6 pb-2 px-5 flex items-center justify-center">
        <img class="relative w-full max-w-[240px] h-auto object-cover transition-transform duration-300 transform group-hover:scale-110" src="assets/empanada.png" alt="Empanada">
      </div>
      <div class="relative text-white px-6 pb-6">
        <span class="block opacity-75 -mb-1">Especiales • 5 un</span>
        <div class="flex justify-between items-center">
          <span class="block font-semibold text-xl">Empanada de Carne</span>
          <span class="block bg-white rounded-full text-amber-500 text-xs font-bold px-3 py-2">S/ 7.00</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Sección Oferta -->
  <div class="pt-20 flex flex-col lg:flex-row gap-4 px-4 sm:px-6 lg:px-0">
    <!-- Oferta 1 -->
    <div class="group cursor-pointer inset-0 bg-gradient-to-tr from-rose-200 to-rose-50 text-left rounded-sm shadow-lg duration-300 hover:scale-103 w-full lg:w-3/5"> 
      <div style="background-image: url('assets/banner1.png');" class="relative flex flex-col lg:flex-row bg-center">   
        <!-- Descuento -->
        <div class="absolute top-4 right-4 bg-amber-400 text-white font-bold px-4 py-1 rounded-full text-sm">
          -30% OFF
        </div>

        <!-- Contenedor de texto -->
        <div class="lg:w-[60%] p-5 md:px-10 flex flex-col justify-center">
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 tracking-wider">Doble placer, <span class="text-purple-800">doble tentación</span></h1>
          <h2 class="text-sm sm:text-base mt-2 font-medium text-gray-700">Cuando una no es suficiente, el antojo se duplica.</h2>
          <button
            class="cursor-pointer mt-2 py-2 pr-2 font-medium text-rose-600 group-hover:text-purple-800 transition flex items-center">
              Dejate envolver por el sabor
            <div class="transition-transform transform group-hover:translate-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right">
                <path d="m9 18 6-6-6-6"></path>
              </svg>
            </div>
          </button>
        </div>  

        <!-- Contenedor de imagen -->
        <div class="lg:w-[40%] flex items-center justify-center">
          <img class="w-full object-cover object-center rounded-sm transition-transform duration-300 transform group-hover:scale-110" src=assets/anuncio1.png alt="Anuncio">
        </div>
      </div> 
    </div>

    <!-- Oferta 2 -->
    <div class="group cursor-pointer inset-0 bg-gradient-to-tr from-zinc-200 to-zinc-50 text-left rounded-sm shadow-lg duration-300 hover:scale-103 w-full lg:w-2/5"> 
      <div style="background-image: url('assets/banner2.png');" class="relative flex flex-col lg:flex-row bg-center">   
        <!-- Descuento -->
        <div class="absolute top-4 right-4 bg-amber-400 text-white font-bold px-4 py-1 rounded-full text-sm">
          -20% OFF
        </div>

        <!-- Contenedor de texto -->
        <div class="lg:w-[60%] p-5 md:px-10 flex flex-col justify-center">
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 tracking-wider">Un bocado, <span class="text-orange-400">un susurro</span></h1>
          <h2 class="text-sm sm:text-base mt-2 font-medium text-gray-700">Mañanas que provocan.</h2>
          <button class="cursor-pointer font-medium mt-2 py-2 pr-2 text-indigo-600 group-hover:text-orange-400 transition flex items-center">
            Dejate tentar
            <div class="transition-transform transform group-hover:translate-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right">
                <path d="m9 18 6-6-6-6"></path>
              </svg>
            </div>
          </button>
        </div>  

        <!-- Contenedor de imagen -->
        <div class="lg:w-[40%] flex items-center justify-center">
          <img class="w-full object-cover object-center rounded-sm transition-transform duration-300 transform group-hover:scale-110" src=assets/anuncio2.png alt="Anuncio">
        </div>
      </div> 
    </div>
  </div>

  <!-- Secccion Catering -->
  <div class="mt-10 px-4 sm:px-6 lg:px-0">
    <div class="group cursor-pointer inset-0 bg-gradient-to-tr from-zinc-900 to-zinc-800 text-left rounded-sm shadow-lg duration-300 hover:scale-103 w-full"> 
      <div style="background-image: url('assets/banner3.png');" class="relative flex flex-col lg:flex-row bg-center">   

        <!-- Contenedor de texto -->
        <div class="lg:w-[60%] p-5 md:px-10 flex flex-col justify-center">
          <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-stone-200 tracking-wider">Un <span class="text-yellow-300">Catering Service</span> que conquista desde el primer bocado</h1>
          <h2 class="text-sm sm:text-base mt-2 font-medium tracking-wide text-gray-50">Un evento especial merece un banquete a la altura. El aroma del pan recién horneado, la suavidad de cada bocado y el dulzor que enamora en cada detalle.</h2>
          <button class="cursor-pointer mt-2 py-2 pr-2 font-medium text-pink-500 group-hover:text-fuchsia-600 transition flex items-center">
            Nosotros ponemos el sabor, tú disfrutas el momento
            <div class="transition-transform transform group-hover:translate-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right">
                <path d="m9 18 6-6-6-6"></path>
              </svg>
            </div>
          </button>
        </div>  

        <!-- Contenedor de imagen -->
        <div class="lg:w-[40%] flex items-center justify-center">
          <img class="w-full object-cover object-center rounded-sm transition-transform duration-300 transform group-hover:scale-110" src=assets/anuncio3.png alt="Anuncio">
        </div>
      </div> 
    </div>
  </div>

  <!-- Sección Somos Proveedores -->
  <div class="pt-6 flex items-center justify-center">
    <div class="w-full max-w-3xl text-center">
      <span class="my-2 text-sm font-medium text-gray-500 whitespace-nowrap">Somos proveedores principales</span>
    </div>
  </div>

  <!-- Sección Logos -->
  <div class="z-10 px-4 sm:px-6 lg:px-0 mt-4">
    <div class="relative grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
      <!-- Mounster Inc -->
      <div class="relative group">    
        <img class="w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-100 group-hover:opacity-0" src=assets/logo5.png alt="Mounster Inc">
        <img class="absolute top-0 left-0 w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-0 group-hover:opacity-100" src=assets/Color1.png alt="Monster Inc Hover">
      </div>  
      <!-- La Ratatouille -->
      <div class="relative group">    
        <img class="w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-100 group-hover:opacity-0" src=assets/logo3.png alt="La Ratatouille">
        <img class="absolute top-0 left-0 w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-0 group-hover:opacity-100" src=assets/Color2.png alt="La Ratatouille Hover">
      </div>
      <!-- Pizza Planet -->
      <div class="relative group">    
        <img class="w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-100 group-hover:opacity-0" src=assets/logo1.png alt="Pizza Planet">
        <img class="absolute top-0 left-0 w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-0 group-hover:opacity-100" src=assets/Color3.png alt="Pizza Planet Hover">
      </div>  
      <!-- The Krusty Krab -->
      <div class="relative group">    
        <img class="w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-100 group-hover:opacity-0" src=assets/logo7.png alt="The Krusty Krab">
        <img class="absolute top-0 left-0 w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-0 group-hover:opacity-100" src=assets/Color4.png alt="The Krusty Krab Hover">
      </div>
      <!-- Los Pollos Hermanos -->      
      <div class="relative group">    
        <img class="w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-100 group-hover:opacity-0" src=assets/logo9.png alt="Los Pollos Hermanos">
        <img class="absolute top-0 left-0 w-full h-full object-cover rounded-sm transition-opacity duration-300 opacity-0 group-hover:opacity-100" src=assets/Color5.png alt="Los Pollos Hermanos Hover">
      </div>
    </div>
  </div>
</div>