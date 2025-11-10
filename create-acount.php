<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario'])) {
    // Si ya hay una sesión iniciada, redirige al usuario según su tipo
    if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Administrador') {
        header("Location: Admin/admin-page.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

//
require 'Config/config.php';
require 'database.php';
//Se hace la conexion a la base de datos
//Se crea una instancia de la clase Database y se llama al metodo conectar
$db = new Database();
$con = $db->conectar();

//Condicional para que se agregar un usuario cuando se envia los datos del formulario
if (isset($_POST['accion']) && $_POST['accion'] === 'agregarU') {
    //Recuperar los datos del formulario
    //Trim para eliminar espacios en blanco al inicio y al final
    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $telefono = trim($_POST["telefono"]);
    $correo = trim($_POST["email"]);
    $contrasena = trim($_POST["password"]);
    $dni = trim($_POST["dni"]);
    $direccion = trim($_POST["direccion"]);
    $distrito = trim($_POST["distrito"]);
    $genero = trim($_POST["genero"]);

   

    $genero_valor = (int) $genero; // Convertir a entero para evitar problemas de tipo
    //Validaciones de los campos del formulario
    //Encriptar la contraseña
    $contrasena_encriptada = password_hash($contrasena, PASSWORD_DEFAULT);

    // header: Indica que la respuesta es de tipo JSON (echo json_encode)
    header('Content-Type: application/json');

    //Se realizan las consultas para insertar los datos en las tablas correspondientes
    //Se utiliza el método prepare para preparar la consulta SQL
    $sql_persona = $con->prepare("INSERT INTO tb_persona (Nombre, Apellido, DNI, Telefono, Sexo) VALUES (?, ?, ?, ?, ?)");
    $resultado_persona = $sql_persona->execute([$nombre, $apellido, $dni, $telefono, $genero_valor]);

    //Se obtiene el ID de la última persona insertada
    //Esto es necesario para poder insertar el ID en la tabla de usuario y cliente
    $id_persona = $con->lastInsertId();

    $sql_usuario = $con->prepare("INSERT INTO tb_usuario (Id_Usuario, Correo, Contrasena) VALUES (?, ?, ?)");
    $resultado_usuario = $sql_usuario->execute([$id_persona , $correo, $contrasena_encriptada]);

    $sql_cliente = $con->prepare("INSERT INTO tb_cliente (Id_Cliente, Id_Distrito, Direccion_Cliente) VALUES (?, ?, ?)");
    $resultado_cliente = $sql_cliente->execute([$id_persona, $distrito, $direccion]);

    //Verifica si las consultas se ejecutaron correctamente
    //Si todas las consultas se ejecutaron correctamente, se devuelve un mensaje de éxito
    if ($resultado_persona && $resultado_usuario && $resultado_cliente) {

        echo json_encode(["status" => "success", "message" => "Usuario agregado correctamente"]);
        exit;
    } else {
        
        echo json_encode(["status" => "error", "message" => "Error al agregar el usuario"]);
        exit;
    }

}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registro - Panadería</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container .select2-selection--single {
            height: 45px !important;
            display: flex;
            align-items: center;
            
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px !important;
        }
        /* Centrar el icono del menú desplegable */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px !important;
            top: 0 !important;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
    </style>
</head>
<body class="font-sans bg-white">

    <section class="flex flex-col-reverse md:flex-row h-auto md:h-screen items-center">
        
    <!-- Formulario -->
    <div class="flex-1 w-full flex items-center justify-center">
      <div class="w-full max-w-2xl">
        <h1 class="text-center text-3xl md:text-4xl font-bold text-zinc-800 mb-10">
          Registro en la Panadería
        </h1>
        
        <form id="registerForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Cada campo -->
          <div>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre"
              maxlength="50"
              class="w-full bg-gray-200 h-11 rounded-lg px-3 focus:outline-none focus:ring-1 focus:ring-orange-400 focus:bg-white"/>
            <span id="validaNombre" class="text-red-500 text-sm mt-1 block"></span>
          </div>

          <div>
            <input type="text" id="apellido" name="apellido" placeholder="Apellido"
              maxlength="40"
              class="w-full bg-gray-200 h-11 rounded-lg px-3 focus:outline-none focus:ring-1 focus:ring-orange-400 focus:bg-white"/>
            <span id="validaApellido" class="text-red-500 text-sm mt-1 block"></span>
          </div>

          <div>
            <input type="email" id="email" name="email" placeholder="Correo electrónico"
              class="w-full bg-gray-200 h-11 rounded-lg px-3 focus:outline-none focus:ring-1 focus:ring-orange-400 focus:bg-white"/>
            <span id="validaEmail" class="text-red-500 text-sm mt-1 block"></span>
          </div>

          <div>
            <input type="text" id="telefono" name="telefono" placeholder="Teléfono" maxlength="9"
              class="w-full bg-gray-200 h-11 rounded-lg px-3 focus:outline-none focus:ring-1 focus:ring-orange-400 focus:bg-white"/>
            <span id="validaTelefono" class="text-red-500 text-sm mt-1 block"></span>
          </div>



          <!-- Dirección: ocupará toda la fila -->
          <div class="md:col-span-2">
            <input type="text" id="direccion" name="direccion" placeholder="Dirección"
              class="w-full bg-gray-200 h-11 rounded-lg px-3 focus:outline-none focus:ring-1 focus:ring-orange-400 focus:bg-white"/>
            <span id="validaDireccion" class="text-red-500 text-sm block mt-1"></span>
          </div>

          <!-- Género y Distrito en la misma fila -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">
            <div>
              <select id="genero" name="genero"
                class="w-full bg-gray-200 h-11 rounded-lg px-3" required>
                <option value="">Selecciona género</option>
                <option value="0">Masculino</option>
                <option value="1">Femenino</option>
                <option value="2">Otro</option>
              </select>
              <span id="validaGenero" class="text-red-500 text-sm block mt-1"></span>
            </div>
            
            <div>
              <select id="distrito" name="distrito"
                class="w-full bg-gray-200 h-11 rounded-lg px-3" required>
                <option value="">Selecciona un distrito</option>
                <option value="1">Ancón</option>
                <option value="2">Ate</option>
                <option value="3">Barranco</option>
                <option value="4">Breña</option>
                <option value="5">Carabayllo</option>
                <option value="6">Cercado de Lima</option>
                <option value="7">Chaclacayo</option>
                <option value="8">Chorrillos</option>
                <option value="9">Cieneguilla</option>
                <option value="10">Comas</option>
                <option value="11">El Agustino</option>
                <option value="12">Independencia</option>
                <option value="13">Jesús María</option>
                <option value="14">La Molina</option>
                <option value="15">La Victoria</option>
                <option value="16">Lince</option>
                <option value="17">Los Olivos</option>
                <option value="18">Lurigancho</option>
                <option value="19">Lurín</option>
                <option value="20">Magdalena del Mar</option>
                <option value="21">Miraflores</option>
                <option value="22">Pachacámac</option>
                <option value="23">Pucusana</option>
                <option value="24">Pueblo Libre</option>
                <option value="25">Puente Piedra</option>
                <option value="26">Punta Hermosa</option>
                <option value="27">Punta Negra</option>
                <option value="28">Rímac</option>
                <option value="29">San Bartolo</option>
                <option value="30">San Borja</option>
                <option value="31">San Isidro</option>
                <option value="32">San Juan de Lurigancho</option>
                <option value="33">San Juan de Miraflores</option>
                <option value="34">San Luis</option>
                <option value="35">San Martín de Porres</option>
                <option value="36">San Miguel</option>
                <option value="37">Santa Anita</option>
                <option value="38">Santa María del Mar</option>
                <option value="39">Santa Rosa</option>
                <option value="40">Santiago de Surco</option>
                <option value="41">Surquillo</option>
                <option value="42">Villa El Salvador</option>
                <option value="43">Villa María del Triunfo</option>
              </select>
              <span id="validaDistrito" class="text-red-500 text-sm block mt-1"></span>
            </div>
          </div>

          <div>
            <input type="text" id="dni" name="dni" placeholder="DNI" maxlength="8"
              class="w-full bg-gray-200 h-11 rounded-lg px-3 focus:outline-none focus:ring-1 focus:ring-orange-400 focus:bg-white"/>
            <span id="validaDni" class="text-red-500 text-sm block mt-1"></span>
          </div>

          <div>
            <div class="relative">
              <input type="password" id="password" name="password" placeholder="Contraseña"
                class="w-full bg-gray-200 h-11 rounded-lg px-3 pr-10 focus:outline-none focus:ring-1 focus:ring-orange-400 focus:bg-white"/>
              <button type="button" id="verContraUsuario"
                class="absolute inset-y-0 right-3 flex items-center text-gray-600">
                <i id="eyeIcon" class="fa-solid fa-eye"></i>
              </button>
            </div>
            <span id="validaPassword" class="text-red-500 text-sm block mt-1"></span>
          </div>


            <!-- Botón de registro -->
            <div class="col-span-1 md:col-span-2">
                <button type="submit"
                class="w-full bg-gradient-to-bl from-amber-400/90 to-amber-500 hover:from-amber-400 hover:to-amber-500 text-zinc-800 font-semibold py-3 rounded-xl transition-all duration-300">
                Registrar
                </button>
            </div>
            </form>

            <!-- Link a login -->
            <div class="mt-6 text-center">
            <p class="text-gray-700">
                ¿Ya tienes una cuenta?
                <a href="login.php"
                class="text-transparent bg-clip-text bg-amber-500 hover:bg-gradient-to-r hover:from-amber-500/90 hover:to-amber-600/60 transition-all duration-300 font-semibold">
                Iniciar sesión
                </a>
            </p>
            </div>
        </div>
        </div>

    <!-- Imagen -->
        <div class="hidden md:block flex-1 h-full p-3">
        <img src="assets/bakery.png" class="w-full h-full object-cover rounded-xl" alt="Bakery" />
        </div>
    </section>

  <!-- Scripts -->
    <script src="assets/js/validarRegistro.js"></script>
    <script>
    $(document).ready(function () {
      $('#distrito').select2({
        placeholder: "Selecciona un distrito",
        width: '100%'
      });
      $('#genero').select2({
        placeholder: "Selecciona género",
        width: '100%'
      });
    });
    </script>
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