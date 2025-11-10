<!-- ---
import About1 from '/public/about1.png';
import Paper from "/public/paper_texture.png";
import Cookie from '/public/cookie_about.png';
import Piopio from "/public/piopio_about.png";
import Suerte from '/public/suerte.png';
--- -->

<div class="max-w-screen-xl mx-auto mt-5 ">

  <!-- Billboard -->
  <div class="flex  pt-4">
    <!-- Taza -->
    <div class="w-2/5 flex px-6 pt-20 justify-end">
      <div class="w-[28vw] h-[28vw]">
        <img class="max-w-full object-cover object-center transition-transform duration-300 transform group-hover:scale-110" src="assets/about1.png" alt="Cookie">
      </div>
    </div>
    <!-- Panel -->
    <div class="w-3/5">
      <section class="w-full flex justify-center py-6 pr-14 bg-white">
        <div class="relative w-[98%] max-w-5xl h-80 rounded-lg shadow-lg p-3 bg-gradient-to-br from-gray-800/90 via-gray-800 to-black">
            <div class="w-full h-full bg-gradient-to-r from-yellow-400 from-41% to-stone-100 to-41% flex relative">
              <div style="background-image: url('assets/paper_texture.png');" class="w-full h-full bg-center bg-cover">
                <!-- texto abajo izquierda -->
                <div class="absolute p-4 bottom-4 left-4 w-2/5">
                  <p class="text-3xl font-bold text-gray-800 uppercase tracking-wide">
                    Horneamos con 
                    <span class="relative inline-block before:content-[''] before:absolute before:bottom-0 before:left-0 before:w-full before:h-1/3 before:bg-gradient-to-r before:from-pink-500 before:to-pink-500">
                      <span class="relative text-gray-800 dark:text-white">
                        alma</span>
                    </span>
                    , servimos con                     <span class="relative inline-block before:content-[''] before:absolute before:bottom-0 before:left-0 before:w-full before:h-1/3 before:bg-gradient-to-r before:from-pink-500 from-10% before:to-pink-500">
                      <span class="relative text-gray-800 dark:text-white">
                        pasión</span>
                    </span>.
                  </p>
                </div> 
                <!-- texto arriba derecha -->
                <div class="absolute p-6 right-4 w-3/5 text-right">
                  <p class="text-lg leading-8 font-medium text-gray-800 uppercase tracking-wider">
                    El sabor de siempre, con el encanto de lo eterno. 
                  </p>
                  <div class="flex justify-end space-x-4 mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tractor-icon lucide-tractor"><path d="m10 11 11 .9a1 1 0 0 1 .8 1.1l-.665 4.158a1 1 0 0 1-.988.842H20"/><path d="M16 18h-5"/><path d="M18 5a1 1 0 0 0-1 1v5.573"/><path d="M3 4h8.129a1 1 0 0 1 .99.863L13 11.246"/><path d="M4 11V4"/><path d="M7 15h.01"/><path d="M8 10.1V4"/><circle cx="18" cy="18" r="2"/><circle cx="7" cy="15" r="5"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart-handshake-icon lucide-heart-handshake"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M12 5 9.04 7.96a2.17 2.17 0 0 0 0 3.08c.82.82 2.13.85 3 .07l2.07-1.9a2.82 2.82 0 0 1 3.79 0l2.96 2.66"/><path d="m18 15-2-2"/><path d="m15 18-2-2"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-croissant-icon lucide-croissant"><path d="m4.6 13.11 5.79-3.21c1.89-1.05 4.79 1.78 3.71 3.71l-3.22 5.81C8.8 23.16.79 15.23 4.6 13.11Z"/><path d="m10.5 9.5-1-2.29C9.2 6.48 8.8 6 8 6H4.5C2.79 6 2 6.5 2 8.5a7.71 7.71 0 0 0 2 4.83"/><path d="M8 6c0-1.55.24-4-2-4-2 0-2.5 2.17-2.5 4"/><path d="m14.5 13.5 2.29 1c.73.3 1.21.7 1.21 1.5v3.5c0 1.71-.5 2.5-2.5 2.5a7.71 7.71 0 0 1-4.83-2"/><path d="M18 16c1.55 0 4-.24 4 2 0 2-2.17 2.5-4 2.5"/></svg>
                  </div>
                </div>  
              </div>  
            </div>
        </div>
      </section>
    </div>
  </div>

  <!-- Línea falso piso -->
  <div class="h-[2px] -mt-0.5  bg-gradient-to-r from-transparent via-teal-200 to-transparent w-full opacity-70"></div>

  <!-- Timeline -->
  <div class="flex flex-col items-center gap-10 pt-14 pb-6">
    <div class="flex flex-col gap-8 w-full max-w-5xl">
      <div class="flex flex-col items-center py-10 w-full max-w-5xl">
        <div class="w-full space-y-10">
          
          <!-- 2016 -->
          <div class="flex flex-col items-center">
            <p class="text-lg font-bold">2016</p>
            <p class="text-sm text-gray-500">Inicio del Sueño</p>
          </div>
          <div class="flex w-full justify-between items-center px-10 relative">
            <div class="w-1/3 text-right pr-4 border-t border-b border-stone-300 py-4">
              <p class="font-bold">Inauguración de la panadería</p>
              <p class="font-light">Se abre la primera tienda con recetas tradicionales.</p>
            </div>
            <div class="w-1/3 flex justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun-icon lucide-sun"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
            </div>
            <div class="w-1/3"></div>
          </div>

          <!-- 2018 -->
          <div class="flex flex-col items-center">
            <p class="text-lg font-bold">2018</p>
            <p class="text-sm text-gray-500">Primer gran cambio</p>
          </div>
          <div class="flex w-full justify-between items-center px-10 relative">
            <div class="w-1/3 relative">
              <img src=assets/cookie_about.png class="absolute inset-0 w-auto h-90 left-10 -top-40 object-cover"  alt="Cookie"/>
            </div>
            <div class="w-1/3 flex justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-biceps-flexed-icon lucide-biceps-flexed"><path d="M12.409 13.017A5 5 0 0 1 22 15c0 3.866-4 7-9 7-4.077 0-8.153-.82-10.371-2.462-.426-.316-.631-.832-.62-1.362C2.118 12.723 2.627 2 10 2a3 3 0 0 1 3 3 2 2 0 0 1-2 2c-1.105 0-1.64-.444-2-1"/><path d="M15 14a5 5 0 0 0-7.584 2"/><path d="M9.964 6.825C8.019 7.977 9.5 13 8 15"/></svg>
            </div>
            <div class="w-1/3 pl-4 border-t border-b border-stone-300 py-4">
              <p class="font-bold">Menú renovado</p>
              <p class="font-light">Se incorporan nuevas recetas y se amplía la oferta de productos.</p>
            </div>
          </div>
          
          <!-- 2020 -->
          <div class="flex flex-col items-center">
            <p class="text-lg font-bold">2020</p>
            <p class="text-sm text-gray-500">Adaptación</p>
          </div>
          <div class="flex w-full justify-between items-center px-10 relative">
            <div class="w-1/3 text-right pr-4 border-t border-b border-stone-300 py-4">
              <p class="font-bold">Pedidos a distancia</p>
              <p class="font-light">Se implementa una linea para entregas a domicilio.

              </p>
            </div>
            <div class="w-1/3 flex justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chef-hat-icon lucide-chef-hat"><path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/></svg>
            </div>
            <div class="w-1/3"></div>
          </div>

          <!-- 2022 -->
          <div class="flex flex-col items-center">
            <p class="text-lg font-bold">2022</p>
            <p class="text-sm text-gray-500">Un compromiso con la calidad</p>
          </div>
          <div class="flex w-full justify-between items-center px-10 relative">
            <div class="w-1/3"></div>
            <div class="w-1/3 flex justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-carrot-icon lucide-carrot"><path d="M2.27 21.7s9.87-3.5 12.73-6.36a4.5 4.5 0 0 0-6.36-6.37C5.77 11.84 2.27 21.7 2.27 21.7zM8.64 14l-2.05-2.04M15.34 15l-2.46-2.46"/><path d="M22 9s-1.33-2-3.5-2C16.86 7 15 9 15 9s1.33 2 3.5 2S22 9 22 9z"/><path d="M15 2s-2 1.33-2 3.5S15 9 15 9s2-1.84 2-3.5C17 3.33 15 2 15 2z"/></svg>
            </div>
            <div class="w-1/3 pl-4 border-t border-b border-stone-300 py-4">
              <p class="font-bold">Ingredientes naturales</p>
              <p class="font-light">Compromiso con productos frescos y de origen responsable.</p>
            </div>
          </div>

          <!-- 2024 -->
          <div class="flex flex-col items-center">
            <p class="text-lg font-bold">2024</p>
            <p class="text-sm text-gray-500">Crecemos juntos</p>
          </div>
          <div class="flex w-full justify-between items-center px-10 relative">
            <div class="w-1/3 text-right pr-4 border-t border-b border-stone-300 py-4">
              <p class="font-bold">Nueva imagen y mejoras</p>
              <p class="font-light">Renovamos nuestro espacio para una mejor experiencia.</p>
            </div>
            <div class="w-1/3 flex justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-feather-icon lucide-feather"><path d="M12.67 19a2 2 0 0 0 1.416-.588l6.154-6.172a6 6 0 0 0-8.49-8.49L5.586 9.914A2 2 0 0 0 5 11.328V18a1 1 0 0 0 1 1z"/><path d="M16 8 2 22"/><path d="M17.5 15H9"/></svg>
            </div>
            <div class="w-1/3 relative">
              <img class="absolute inset-0 w-auto h-90 left-10 -top-60 object-cover" src=assets/piopio_about.png alt="Crumb"/>
            </div>
          </div>

          <div class="flex flex-col items-center">
            <p class="text-lg font-bold">2025</p>
            <!-- <p class="text-sm text-gray-500">No olvides sonreir</p> -->
          </div>

        </div>
          <!-- Algo esta por venir -->
          <section
          class="py-8 relative bg-cover bg-center text-left px-10 md:px-20 flex-1"
        >
          <div
            class="cursor-pointer absolute z-50 inset-0 flex items-center transition justify-center duration-300 hover:scale-105"
          >
            <img
              src=assets/Suerte.png
              alt="Suerte"
              class="max-w-full max-h-full object-contain"
            />
          </div>
          <div
            class=" relative z-10 bg-gradient-to-bl from-amber-300 from-10% via-orange-400 via-70% to-amber-600 bg-clip-text text-transparent text-center flex flex-col items-center content-between"
          >
            <div class="py-8 text-2xl text-center text-gray-800">No olvides sonreir</div>
            <div class="text-9xl font-bold px-20">Lo mejor está por venir...</div>           
          </div>
        </section>

      </div>
    </div>
  </div>

</div>
