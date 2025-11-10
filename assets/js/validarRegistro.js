// Se establece el escucha y se establece el DOMContentLoaded 
// para que se ejecute el código una vez que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function () {
    console.log("Documento cargado");
    //Se obtiene todos los datos del elemento HTML con el id "registerForm"
    // Siempre y cuando se haga el submit del formulario
    $('#registerForm').submit(function (e) {
        //Se previene el comportamiento por defecto del formulario
        e.preventDefault();

        //Condición que garantiza el correcto llenado de los campos antes de ser enviado los datos
        if (validarFormularioAgre()) {
            console.log("Formulario enviado");
            //Se establece un formato con todos los datos del formulario
            // Se agrega la acción al final de los datos serializados
            var formData = $(this).serialize() + '&accion=agregarU';//acccion : Variable agregarU: Valor
            console.log("Datos serializados:", formData);

            $.ajax({
                type: 'POST',
                url: 'create-acount.php',
                data: formData,
                dataType: 'json',
                success: function (data) {// Ya se enviaron los datos
                    console.log("Respuesta del servidor:", data);
                    if (data.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Éxito",
                            text: data.message,
                            confirmButtonColor: "#3085d6",
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = 'login.php'; // Redirige al login solo si el registro fue exitoso
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: data.message,
                            confirmButtonColor: "#d33",
                            allowOutsideClick: false // Evita que se cierre al hacer clic fuera
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en AJAX:", xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Error en la petición",
                        text: "Algo salió mal." + xhr.responseText,
                        confirmButtonColor: "#d33",
                        allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                    });
                }
            }); 
        } else {
            console.log("Formulario no enviado, hay errores de validación.");
        }
    });

    // Asigna eventos de validación en tiempo real
    // Esto permite visualizar las validaciones ni bien se llenen los campos correspondientes del formulario

    document.getElementById("nombre").addEventListener("input", function () {
        validarTexto("nombre", "validaNombre");
    });

    document.getElementById("apellido").addEventListener("input", function () {
        validarTexto("apellido", "validaApellido");
    });

    document.getElementById("telefono").addEventListener("input", function () {
        validarTelefonoAgre("telefono", "validaTelefono");
    });

    document.getElementById("email").addEventListener("input", function () {
        validarCorreoAgre("email", "validaEmail");
    });

    document.getElementById("password").addEventListener("input", function () {
        validarContrasenaAgre("password", "validaPassword");
    });

    document.getElementById("dni").addEventListener("input", function () {
        validarDni("dni", "validaDni");
    });

    document.getElementById("direccion").addEventListener("input", function () {
        validarCampoVacio("direccion", "validaDireccion");
    });


     //Función que permite establecer el correcto llenado de los datos de todos los campos de su respectivo formulario
     function validarFormularioAgre() {
        console.log("Ejecutando validación del formulario de agregar...");
        let valido = true; // Inicia como válido
    
        // Ejecuta cada validación y actualiza 'valido' correctamente
        valido = validarTexto("nombre", "validaNombre") && valido;
        valido = validarTexto("apellido", "validaApellido") && valido;
        valido = validarTelefonoAgre("telefono", "validaTelefono") && valido;
        valido = validarCorreoAgre("email", "validaEmail") && valido;
        valido = validarContrasenaAgre("password", "validaPassword") && valido;
        valido = validarDni("dni", "validaDni") && valido;
        valido = validarCampoVacio("direccion", "validaDireccion") && valido;
        console.log("Resultado de validación:", valido);
        return valido;
    }
    

    function validarDni(campoId, errorId){

        const campo = document.getElementById(campoId);
        const error = document.getElementById(errorId);
        const regex =/^\d{8}$/;

        if (!regex.test(campo.value)) {
            error.textContent = "DNI debe contener 8 dígitos numéricos.";
            return false;
        }

        return verificarUnicidadAgre("dni", campo.value, error);
    }



    function validarTexto(campoId, errorId) {
        const campo = document.getElementById(campoId);
        const error = document.getElementById(errorId);
        //Formato que permite establecer solo letras
        const regex = /^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/;

        if (!campo.value.trim()) {
            error.textContent = "Este campo no puede estar vacío.";
            return false;
        }
        if (!regex.test(campo.value)) {
            error.textContent = "Solo se permiten letras.";
            return false;
        }
        // El campo span resulta vacio si todo esta en orden
        error.textContent = "";
        return true;
    }

    function validarTelefonoAgre(campoId, errorId) {
        const campo = document.getElementById(campoId);
        const error = document.getElementById(errorId);
        const regex = /^9\d{8}$/;

        if (!regex.test(campo.value)) {
            error.textContent = "Debe comenzar con 9 y tener 9 dígitos.";
            return false;
        }
        // Verificar si el celular es único
        return verificarUnicidadAgre("telefono", campo.value, error);
    }

    function validarCorreoAgre(campoId, errorId) {
        const campo = document.getElementById(campoId);
        const error = document.getElementById(errorId);
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (!regex.test(campo.value)) {
            error.textContent = "Debe ingresar un correo válido.";
            return false;
        }

        return verificarUnicidadAgre("correo", campo.value, error);
    }

    
    function validarContrasenaAgre(campo1Id, errorId) {
        const campo1 = document.getElementById(campo1Id);
        const error = document.getElementById(errorId);
    
        // Expresión regular que valida la contraseña
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{9,}$/;
    
        if (!regex.test(campo1.value)) {
            error.textContent = "En el campo 'Contraseña' debe tener más de 8 caracteres, incluir mayúsculas, minúsculas, números y caracteres especiales.";
            return false;
        }
        /*
        if (campo1.value != campo2.value) {
            error.textContent = "La contraseña no coiciden";
            return false;
        }
        */
        // Si cumple los requisitos, borrar mensaje de error
        error.textContent = "";
        return true;
    }
    /*
    function compararContrasena(campo1Id,campo2Id, errorId) {
        const campo1 = document.getElementById(campo1Id);
        const campo2 = document.getElementById(campo2Id);
        const error = document.getElementById(errorId);

        // Expresión regular que valida la contraseña
    
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{9,}$/;
    
        if ( !regex.test(campo1.value) || !regex.test(campo2.value)) {
            error.textContent = "En el campo 'Contraseña' o 'Confirmar Contraseña' deben tener más de 8 caracteres, incluir mayúsculas, minúsculas, números y caracteres especiales.";
            return false;
        }
    
        if (campo1.value != campo2.value) {
            error.textContent = "La contraseña no coiciden";
            return false;
        }

        
        // Si cumple los requisitos, borrar mensaje de error
        error.textContent = "";
        return true;
    }
    ¨*/

    function validarFechaNacimiento(campoId, errorId) {
        const campo = document.getElementById(campoId);
        const error = document.getElementById(errorId);
        const fechaIngresada = new Date(campo.value);
        const hoy = new Date();
        const edad = hoy.getFullYear() - fechaIngresada.getFullYear();

        if (isNaN(fechaIngresada.getTime())) {
            error.textContent = "Debe seleccionar una fecha válida.";
            return false;
        }

        if (edad < 18 || (edad === 18 && hoy < new Date(hoy.getFullYear(), fechaIngresada.getMonth(), fechaIngresada.getDate()))) {
            error.textContent = "El repartidor debe tener al menos 18 años.";
            return false;
        }

        error.textContent = "";
        return true;
    }


    function validarCampoVacio(campoId, errorId) {
        const campo = document.getElementById(campoId);
        const error = document.getElementById(errorId);

        if (!campo.value.trim()) {
            error.textContent = "Este campo no puede estar vacío.";
            return false;
        }else{
            error.textContent = "";
        }

        
        return true;
    }
    
    //Función que verifica que los datos sean unicos para determinados campos, dicha gestiòn lo hace el archivo verificar_unicidad.php
    function verificarUnicidadAgre(tipo, valor, errorElemento) {
        let resultado = false;
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "Clases/verificar_unicidad.php", false);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
        let dniUsuario = document.getElementById("dni").value.trim(); // Obtener el DNI del repartidor en edición
        let action = "agre_U";
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                console.log(xhr.responseText); // Para depuración
    
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === "existe") {
                        errorElemento.textContent = `El ${tipo} ya está registrado.`;
                        resultado = false;
                    } else {
                        errorElemento.textContent = "";
                        resultado = true;
                    }
                }
            }
        };
        // Se envian los datos para la gestión correspondiente de búsqueda
        xhr.send(`tipo=${tipo}&valor=${encodeURIComponent(valor)}&dni=${dniUsuario}&action=${action}`);
        return resultado;
    }

});
// Función que permite ver la contraseña de los usuarios
document.getElementById('verContraUsuario').addEventListener('click', function () {
    var passwordField = document.getElementById('password');// Se captura el Id del input contraseña
    var eyeIcon = document.getElementById('eyeIcon');// se captura el Id icono del ojo

    if (passwordField.type === "password") {// Si es de tipo password el input de la contraseña
        passwordField.type = "text";// Al darle clic se transforma en tipo text el cual permitara visualizar la contraseña
        eyeIcon.classList.remove("fa-eye");// se eliminar el icono del ojo tachado
        eyeIcon.classList.add("fa-eye-slash"); // y se reemplaza por el icono ojo
    } else {//Si no es de tipo password el input de la contraseña
        passwordField.type = "password";//Se establece de tipo password (para ocultar la contraseña nuevamente)
        eyeIcon.classList.remove("fa-eye-slash");// Se remueve icono del ojo 
        eyeIcon.classList.add("fa-eye"); // Se reemplaza por el icono ojo tachado
    }
});