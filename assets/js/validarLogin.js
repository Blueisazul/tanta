document.addEventListener("DOMContentLoaded", function () {
    console.log("Documento cargado");
    $('#loginForm').submit(function (e) {
        e.preventDefault();

        let correo = document.getElementById("emailLogin").value.trim();
        let contrasena = document.getElementById("passwordLogin").value.trim();
  

        if (correo === "" || contrasena === "") {
            Swal.fire({ title: "Error", text: "Hay campos vacíos", icon: "error" });
            return;
        }
        var formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: 'control_sesion.php',
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log("Respuesta del servidor:", data);
                if (data.success) {
                    Swal.fire({
                        title: "🍪🍩Bienvenid@🥐🥨",
                        text: data.message,
                        icon: "success",
                        allowOutsideClick: false // Evita que se cierre al hacer clic fuera
                    }).then(() => {
                        window.location.href = data.redirect; // Redirige según el tipo de usuario
                    });
                } else {
                    Swal.fire({ title: "Error", text: data.message, icon: "error", allowOutsideClick: false });
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
    });
});

document.getElementById('verContraUsuario').addEventListener('click', function () {
    var passwordField = document.getElementById('passwordLogin');// Se captura el Id del input contraseña
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
