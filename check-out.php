<?php 
session_start(); // Iniciar sesión para acceder a las variables de sesión
require 'Config/config.php';
require 'database.php';


if (isset($_SESSION['usuario'])) {

    if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'Administrador'){
        // Si ya hay una sesión iniciada, redirige al usuario según su tipo
        header("Location: Admin/admin-page.php");
        exit(); 
    }
}else {
    header("Location: login.php");
    exit(); 
}

if(isset($_SESSION['usuario'])){
    $db = new Database();
    $con = $db->conectar();
    
    // Consulta SQL para seleccionar los datos del usuario utilizando su ID al LOGUEARSE
    $sql = $con->prepare("SELECT Nombre, Apellido, DNI, Telefono FROM tb_persona WHERE Id_Persona = :id");
    $sql->bindParam(':id', $_SESSION['id'] , PDO::PARAM_INT);
    $sql->setFetchMode(PDO::FETCH_ASSOC); // Establecer el modo de recuperación de datos
    $sql->execute();
    $datos_persona = $sql->fetch(PDO::FETCH_ASSOC);
    
    // Asignar los datos del usuario a variables individuales
    $nombre = $datos_persona['Nombre'];
}

// Navbar según si el usuario está logueado
if (isset($_SESSION['usuario'])) {
    include 'components/Navbar_cliente.php';
} else {
    include 'components/Navbar.php';
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Zona de Pago</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { display: none; }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.body.style.display = "block";
        });
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .select2-container .select2-selection--single {
            height: 45px !important;
            display: flex;
            align-items: center;
            
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px !important;
        }
        /* Centrar el icono del menÃº desplegable */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px !important;
            top: 0 !important;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
    </style>
    <script>
        window.addEventListener("load", () => {
            document.body.style.visibility = "visible";
            document.body.style.opacity = "1";
        });
    </script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Zona de Pago</h1>
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Productos del carrito -->
            <div id="checkout-content" class="space-y-4 lg:w-2/3">
                <!-- Se insertan con JS -->
            </div>

            <!-- Resumen de compra -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-6 ">
                    <h2 class="text-xl font-semibold mb-4">Resumen de Compra</h2>

                        <div class="mb-4">
                            <label for="distrito_envio" class="block text-sm font-medium text-gray-700">Distrito</label>
                            <select id="distrito_envio" name="distrito_envio" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2">
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
                                <!-- Agrega más distritos según sea necesario -->
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="direccion_envio" class="block text-sm font-medium text-gray-700">Dirección</label>
                            <input type="text" id="direccion_envio" name="direccion_envio" required placeholder="Av. Ejemplo 123" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2">
                        </div>

    
                    <div class="space-y-3 mb-6" id="resumen-compra">
                        <!-- Se actualiza con JS -->
                    </div>

                    <button id="pagarBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition duration-200">
                        Pagar
                    </button>

                    <!-- Sección de QR -->
                    <div id="seccionPago" class="hidden mt-6">
                        <div class="border border-dashed border-gray-300 p-4 rounded-lg mb-4">
                            <p class="text-center text-gray-600 mb-2">Código de pago</p>
                            <img src="./assets/qr.png" alt="Código de pago" class="mx-auto mb-4">
                            <button id="pagadoBtn" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition duration-200">
                                Pagado
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="recibo-container" class="hidden">
        <div id="recibo" 
            style="padding:6px 8px; max-width:240px; margin:0 auto; background:#fff; border:1px solid #ccc; border-radius:4px; 
                    font-family: Arial, sans-serif; font-size:8px; color:#333; line-height:1.2;">
            
            <!-- Encabezado -->
            <div style="text-align:center; margin-bottom:6px; border-bottom:1px solid #ccc; padding-bottom:6px;">
                <div style="font-weight:bold; font-size:10px; color:#222; line-height:1.1;">TANTA S.A.C.</div>
                <div style="font-size:7px; color:#666; line-height:1.1;">Pastelería y Repostería Finas</div>
                <div style="font-size:7px; color:#555; margin-top:4px; line-height:1.1;">RUC: 20608170805</div>
            </div>
            
            <!-- Datos -->
            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:6px 8px; margin-bottom:6px; font-size:7.5px;">
                <div>
                    <strong>RECIBO #</strong><br>
                    <span id="recibo-numero" style="color:#555; line-height:1.2;">001-001-0000123</span>
                </div>
                <div>
                    <strong>FECHA</strong><br>
                    <span id="recibo-fecha" style="color:#555; line-height:1.2;"></span>
                </div>
                <div style="grid-column: span 2;">
                    <strong>CLIENTE</strong><br>
                    <span id="recibo-cliente" style="color:#555; line-height:1.2;">Nombre del Cliente</span>
                </div>
                <div style="grid-column: span 2;">
                    <strong>DIRECCIÓN</strong><br>
                    <span id="recibo-direccion" style="color:#555; line-height:1.2;">Dirección del Cliente</span>
                </div>
            </div>
            
            <!-- Tabla -->
            <div style="margin-bottom:6px;">
                <div style="font-weight:bold; font-size:8px; margin-bottom:4px; line-height:1.1;">DETALLES DE LA COMPRA</div>
                <table style="width:100%; border-collapse: collapse; font-size:7.5px;">
                    <thead style="background:#f9f9f9;">
                    <tr>
                        <th style="text-align:left; padding:4px 6px; border-bottom:1px solid #ccc;">Producto</th>
                        <th style="text-align:right; padding:4px 6px; border-bottom:1px solid #ccc;">Cant.</th>
                        <th style="text-align:right; padding:4px 6px; border-bottom:1px solid #ccc;">P. Unit.</th>
                        <th style="text-align:right; padding:4px 6px; border-bottom:1px solid #ccc;">Total</th>
                    </tr>
                    </thead>
                    <tbody id="recibo-productos" style="border-top:1px solid #ccc;">
                    <!-- Productos aquí -->
                    </tbody>
                </table>
            </div>
            
            <!-- Totales -->
            <div style="margin-bottom:6px; font-size:7.5px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:4px; line-height:1.2;">
                    <span>Subtotal:</span>
                    <span id="recibo-subtotal" style="font-weight:bold;">S/ 0.00</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:4px; line-height:1.2;">
                    <span>Envío:</span>
                    <span id="recibo-envio" style="font-weight:bold;">S/ 0.00</span>
                </div>
                <div style="display:flex; justify-content:space-between; border-top:1px solid #ccc; padding-top:4px; font-weight:bold; font-size:8px; line-height:1.1;">
                    <span>TOTAL:</span>
                    <span id="recibo-total">S/ 0.00</span>
                </div>
            </div>
            
            <!-- Pie de página -->
            <div style="text-align:center; font-size:6.5px; color:#999; border-top:1px solid #ccc; padding-top:6px; line-height:1.2;">
                <div>¡Gracias por su preferencia!</div>
            </div>
        </div>
    </div>




    <!-- Modal de pago exitoso -->
    <div id="pagoExitoso" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4 text-center">
            <div class="text-green-500 text-5xl mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="text-2xl font-semibold mb-2">¡Pago exitoso!</h3>
            <p class="text-gray-600 mb-4">Tu pago ha sido procesado correctamente.</p>
            <p class="text-gray-600 mb-4">El recibo se ha descargado automáticamente.</p>
            <button id="cerrarModal" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-200">
                Cerrar
            </button>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const cartData = sessionStorage.getItem("checkoutCart");
        const container = document.getElementById("checkout-content");
        const resumen = document.getElementById("resumen-compra");

        if (!cartData) {
            container.innerHTML = "<p class='text-gray-600'>Tu carrito está vacío.</p>";
            resumen.innerHTML = "<p class='text-gray-600'>No hay productos para calcular.</p>";
            return;
        }

        const cart = JSON.parse(cartData);
        let subtotal = 0;

        cart.forEach((item, index) => {
            const total = (item.price * item.quantity).toFixed(2);
            subtotal += parseFloat(total);

            const productHTML = document.createElement('div');
            productHTML.className = "bg-white rounded-lg shadow-md p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between";
            productHTML.innerHTML = `
                <div class="flex items-center mb-3 sm:mb-0">
                    <img src="${item.img}" class="w-16 h-16 object-cover rounded mr-4">
                    <div>
                        <h3 class="font-semibold text-lg">${item.title}</h3>
                        <p class="text-gray-600">Precio unitario: S/${item.price.toFixed(2)}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="mr-6 text-center">
                        <span class="block text-gray-600 text-sm">Cantidad</span>
                        <span class="font-semibold">${item.quantity}</span>
                    </div>
                    <div class="mr-6 text-center">
                        <span class="block text-gray-600 text-sm">Total</span>
                        <span class="font-semibold">S/${total}</span>
                    </div>
                    
                </div>
            `;
            container.appendChild(productHTML);
        });

        const envio = 10.00;
        const total = subtotal + envio;

        resumen.innerHTML = `
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>S/${subtotal.toFixed(2)}</span>
            </div>
            <div class="flex justify-between">
                <span>Envío</span>
                <span>S/${envio.toFixed(2)}</span>
            </div>
            <div class="flex justify-between font-bold text-lg border-t pt-3">
                <span>Total</span>
                <span>S/${total.toFixed(2)}</span>
            </div>
        `;


    });

    // Mostrar sección de pago
    document.getElementById('pagarBtn').addEventListener('click', () => {
        document.getElementById('seccionPago').classList.remove('hidden');
    });

    // Función para obtener el nombre del distrito según el ID seleccionado
    function obtenerNombreDistrito(id) {
        const select = document.getElementById('distrito_envio');
        const option = select.querySelector(`option[value="${id}"]`);
        return option ? option.textContent : '';
    }

    // Generar y descargar recibo en PDF
    async function generarReciboPDF() {
        const cartData = sessionStorage.getItem("checkoutCart");
        const cart = JSON.parse(cartData);
        let subtotal = 0;

        cart.forEach(item => {
            subtotal += item.price * item.quantity;
        });

        const envio = 10.00;
        const total = subtotal + envio;

        // Formatear fecha
        const hoy = new Date();
        const fecha = hoy.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        // Obtener dirección y distrito seleccionados
        const direccionEntrega = document.getElementById('direccion_envio').value;
        const idDistrito = document.getElementById('distrito_envio').value;
        const nombreDistrito = obtenerNombreDistrito(idDistrito);

        // Llenar datos del recibo
        document.getElementById('recibo-fecha').textContent = fecha;
        document.getElementById('recibo-cliente').textContent = nombreCliente || "Nombre del Cliente";
        document.getElementById('recibo-direccion').textContent = `${direccionEntrega} - ${nombreDistrito}`;
        document.getElementById('recibo-subtotal').textContent = `S/${subtotal.toFixed(2)}`;
        document.getElementById('recibo-envio').textContent = `S/${envio.toFixed(2)}`;
        document.getElementById('recibo-total').textContent = `S/${total.toFixed(2)}`;

        // Llenar productos
        const productosContainer = document.getElementById('recibo-productos');
        productosContainer.innerHTML = '';

        cart.forEach(item => {
            const totalItem = (item.price * item.quantity).toFixed(2);
            const productoHTML = `
                <tr>
                    <td style="padding:2px 4px;">${item.title}</td>
                    <td style="text-align:right; padding:2px 4px;">${item.quantity}</td>
                    <td style="text-align:right; padding:2px 4px;">S/${item.price.toFixed(2)}</td>
                    <td style="text-align:right; padding:2px 4px;">S/${totalItem}</td>
                </tr>
            `;
            productosContainer.innerHTML += productoHTML;
        });

        // Mostrar el recibo temporalmente para capturarlo
        const reciboContainer = document.getElementById('recibo-container');
        reciboContainer.classList.remove('hidden');

        await new Promise(resolve => setTimeout(resolve, 500));

        const recibo = document.getElementById('recibo');
        const canvas = await html2canvas(recibo, {
            scale: 2,
            logging: false,
            useCORS: true
        });

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm'
        });

        const imgData = canvas.toDataURL('image/png');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);

        reciboContainer.classList.add('hidden');

        pdf.save(`Recibo_${new Date().getTime()}.pdf`);
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.textContent = message;

        toast.className = `
            fixed top-5 left-1/2 transform -translate-x-1/2
            bg-red-600 text-white text-sm px-5 py-3 rounded-lg shadow-lg
            z-[9999] transition-opacity duration-300 opacity-0
            pointer-events-none
        `;

        document.body.appendChild(toast);

        // Forzar renderizado para que se active la transición
        void toast.offsetWidth;
        toast.classList.add('opacity-100');

        // Desaparece luego de 3 segundos
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    document.getElementById('pagadoBtn').addEventListener('click', async () => {
        const cartData = sessionStorage.getItem("checkoutCart");
        if (!cartData) return alert("No hay productos en el carrito.");

        // Obtener los valores del formulario
        const distrito = document.getElementById('distrito_envio').value;
        const direccion = document.getElementById('direccion_envio').value;

        if (!distrito) {
            showToast("Por favor selecciona un distrito.");
            return;
        }

        if (!direccion.trim()) {
            showToast("Por favor ingresa una dirección.");
            return;
        }


        try {
            // Crear objeto con carrito + dirección
            const dataToSend = {
                carrito: JSON.parse(cartData),
                distrito: distrito,
                direccion: direccion
            };

            // Enviar carrito y dirección al servidor
            const response = await fetch('pedido_CRUD.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataToSend)
            });

        
            const result = await response.json();

            if (result.success) {
                console.log("Stock actualizado:", result.updatedProducts);

                document.getElementById('recibo-numero').textContent = result.pedido_id.toString().padStart(7, '0');
                await generarReciboPDF();

                localStorage.removeItem("cart");
                sessionStorage.removeItem("checkoutCart");

                document.getElementById('pagoExitoso').classList.remove('hidden');
                document.getElementById('seccionPago').classList.add('hidden');

                if (window.parent.updateCartUI) {
                    window.parent.updateCartUI();
                }
                // Enviar correo al cliente
                fetch('correo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        id_pedido: result.pedido_id, // asegúrate de tener el id
                        nombre: result.nombre,
                        apellido: result.apellido,
                        email: result.email,
                        telefono: result.telefono,
                        //mensaje: '¡Gracias por tu compra! Tu pedido ha sido recibido.'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        console.log('Correo enviado correctamente');
                    } else {
                        console.error('Error al enviar correo:', data.message);
                        alert('Error al enviar correo: ' + data.message);
                    }
                })
                 .catch(err => {
                    if (err.name === 'AbortError') {
                        // El usuario cerró/navegó, no mostrar nada
                        return;
                    }
                    console.error('Error en fetch correo.php:', err);
                    // Solo alerta si el usuario sigue en la página
                    if (document.visibilityState === 'visible') {
                        alert('Error en fetch correo.php: ' + err);
                    }
                });
            } else {
                throw new Error(result.message || "Error al actualizar stock");
            }
        } catch (error) {
            console.error("Error:", error);
            alert(`Error: ${error.message}. Por favor, intenta de nuevo.`);
        }
    });

    // Cerrar modal y redirigir
    document.getElementById('cerrarModal').addEventListener('click', () => {
        // Redirigir a la página principal con un parámetro para limpiar el carrito UI
        window.location.href = 'catalogo.php';
    });

    $(document).ready(function () {
        $('#distrito_envio').select2({
          placeholder: "Selecciona un distrito",
          width: '100%'
        });
    });
    
    // Pasa el nombre del usuario de PHP a JS
    const nombreCliente = <?php echo json_encode($nombre ?? ""); ?>;


    // Genera el número de recibo a partir del 27 y lo incrementa en cada compra (solo frontend, no persistente)
/*
function generarNumeroRecibo() {
    // Puedes guardar el último número en localStorage para simular incremento
    let ultimo = localStorage.getItem('ultimoRecibo');
    if (!ultimo || isNaN(ultimo) || ultimo < 27) ultimo = 27;
    else ultimo = parseInt(ultimo) + 1;
    localStorage.setItem('ultimoRecibo', ultimo);
    // Formato: 001-001-0000027
    return `${ultimo.toString().padStart(7, '0')}`;
}
*/
// Reemplaza la línea del número de recibo al generar el recibo PDF
//document.getElementById('recibo-numero').textContent = generarNumeroRecibo();
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