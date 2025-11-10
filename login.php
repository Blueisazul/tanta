<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['usuario'])) {
  if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Administrador'){
      // Si ya hay una sesión iniciada, redirige al usuario según su tipo
      header("Location: Admin/admin-page.php");
      exit(); 
  }else if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Cliente'){
      header("Location: index.php");
      exit();

  }
}  
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-white">
  <section class="flex flex-col md:flex-row h-screen p-3">
    
    <!-- Imagen -->
    <div class="hidden md:block md:w-1/2 lg:w-2/3 h-1/3 md:h-full">
      <img src="assets/banner.png" alt="Banner" class="w-full h-full object-cover rounded-xl"/>
    </div>

    <!-- Formulario -->
    <div class="w-full md:w-1/2 lg:w-1/3 h-full flex items-center justify-center px-4 sm:px-6 lg:px-12">
      <div class="w-full max-w-md">
        <h1 class="text-3xl md:text-4xl font-bold text-center text-zinc-800 mb-8">Inicio de Sesión</h1>
        
        <form id="loginForm">
          <div class="mb-6">
            <label for="emailLogin" class="block text-zinc-800 mb-1">Correo</label>
            <input 
              type="email" 
              id="emailLogin" 
              name="emailLogin" 
              placeholder="Ingrese su correo" 
              class="w-full px-4 py-3 rounded-lg bg-gray-200 border border-white focus:border-amber-500 focus:bg-white focus:outline-none"
              required
            />
          </div>

          <div class="mb-6 relative">
            <label for="passwordLogin" class="block text-zinc-800 mb-1">Contraseña</label>
            <input 
              type="password" 
              id="passwordLogin" 
              name="passwordLogin" 
              placeholder="Ingrese su contraseña" 
              minlength="6"
              class="w-full px-4 py-3 pr-10 rounded-lg bg-gray-200 border border-white focus:border-amber-500 focus:bg-white focus:outline-none"
              required
            />
            <button 
              type="button" 
              id="verContraUsuario"
              class="absolute right-3 top-10.5 text-gray-600"
            >
              <i class="fa-solid fa-eye" id="eyeIcon"></i>
            </button>
          </div>

          <button 
            type="submit"
            class=" cursor-pointer w-full bg-gradient-to-bl from-amber-400/90 to-amber-500 hover:from-amber-400/90 hover:to-amber-500/90 focus:bg-amber-400 text-zinc-800 font-semibold rounded-lg px-4 py-3 transition-all duration-300"
          >
            Ingresar
          </button>
        </form>

        <hr class="my-6 border-gray-300"/>

        <p class="text-center text-zinc-800">
          ¿No tiene cuenta?
          <a href="create-acount.php" class="text-amber-500 hover:text-amber-600 font-semibold transition-colors duration-300">
            Crear cuenta
          </a>
        </p>
      </div>
    </div>
  </section>

  <script src="assets/js/validarLogin.js"></script>
  <script>
    (function(d){
      var s = d.createElement("script");
      /* uncomment the following line to override default position*/
      s.setAttribute("data-position", 5);
      /* uncomment the following line to override default size (values: small, large)*/
      /* s.setAttribute("data-size", "small");*/
      /* uncomment the following line to override default language (e.g., fr, de, es, he, nl, etc.)*/
      s.setAttribute("data-language", "es");
      /* uncomment the following line to override color set via widget (e.g., #053f67)*/
      /* s.setAttribute("data-color", "#053e67");*/
      /* uncomment the following line to override type set via widget (1=person, 2=chair, 3=eye, 4=text)*/
      /* s.setAttribute("data-type", "1");*/
      /* s.setAttribute("data-statement_text:", "Our Accessibility Statement");*/
      /* s.setAttribute("data-statement_url", "http://www.example.com/accessibility")";*/
      /* uncomment the following line to override support on mobile devices*/
      /* s.setAttribute("data-mobile", true);*/
      /* uncomment the following line to set custom trigger action for accessibility menu*/
      /* s.setAttribute("data-trigger", "triggerId")*/
      /* uncomment the following line to override widget's z-index property*/
      /* s.setAttribute("data-z-index", 10001);*/
      /* uncomment the following line to enable Live site translations (e.g., fr, de, es, he, nl, etc.)*/
      /* s.setAttribute("data-site-language", "null");*/
      s.setAttribute("data-widget_layout", "full")
      s.setAttribute("data-account", "bLLEVPcx9p");
      s.setAttribute("src", "https://cdn.userway.org/widget.js");
      (d.body || d.head).appendChild(s);
    })(document)
    </script>
    <noscript>Please ensure Javascript is enabled for purposes of <a href="https://userway.org">website accessibility</a></noscript>
</body>
</body>
</html>