$(document).ready(function (){

    $('#formAgregarProducto').submit(function (e) {
      e.preventDefault(); // Detenemos el envío normal del formulario

      // Validamos el formulario antes de enviarlo
      // Si la validación es exitosa, procedemos a enviar los datos
      if(validarFormularioAgreP()){
            // Creamos un objeto FormData para enviar los datos del formulario
            var formData = new FormData(this);

            formData.append('accion', 'agregar'); // Agregamos el parámetro 'accion' y el valor 'agregar'

            // Realizamos una petición AJAX para enviar los datos al servidor
            $.ajax({
            type: 'POST',
            url: 'productos-admin.php', // Archivo PHP que procesará la inserción
            data: formData,
            dataType: 'json',
            processData: false, // Necesario para enviar archivos
            contentType: false, // Necesario para enviar archivos
            success: function (data) {
                if (data.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Éxito",
                        text: data.message,
                        confirmButtonColor: "#3085d6",
                        allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message,
                        confirmButtonColor: "#d33",
                        allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                    });
                }
                },
            error: function (xhr, status, error) {
                    console.log("Respuesta completa del servidor:", xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Hubo un problema al agregar el producto: " + xhr.responseText,
                        confirmButtonColor: "#d33",
                        allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                    });
                    }
            });
        }else{
            console.log("Validación fallida, no se envían datos.");
        }
    }); 
      
    //Asignar elementos de validación en tiempo real


    //Valida nombre
    document.getElementById("nombreP").addEventListener("input", function () {
      validarCampoVacio("nombreP", "validaNombreP");
    });

    //valida stock
    document.getElementById("stockP").addEventListener("input", function () {
      validarEntero("stockP","stockMinP", "validaStockP","validaStockMinP");
    });

    //valida stock min
    document.getElementById("stockMinP").addEventListener("input", function () {
      validarEnteroMin("stockP","stockMinP","validaStockP", "validaStockMinP");
    });

    //valida precio
    document.getElementById("precioP").addEventListener("input", function () {
      validarDecimal("precioP", "validaPrecioP");
    });


    //Función de verificación de los campos: Permite el envio de los datos siemore y cuando este sea verdadero
    function validarFormularioAgreP() {
      console.log("Ejecutando validación del formulario de agregar...");
      let valido = true; // Inicia como válido
  
      // Ejecuta cada validación y actualiza 'valido' correctamente
      valido = validarCampoVacio("nombreP", "validaNombreP") && valido;
      valido = validarEntero("stockP","stockMinP", "validaStockP","validaStockMinP") && valido;
      valido = validarEnteroMin("stockP","stockMinP","validaStockP", "validaStockMinP") && valido;
      valido = validarDecimal("precioP", "validaPrecioP") && valido;

      console.log("Resultado de validación:", valido);
      return valido;
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


    function validarCampoVacio(campoId, errorId) {
      const campo = document.getElementById(campoId);
      const error = document.getElementById(errorId);

      if (!campo.value.trim()) {
          error.textContent = "Este campo no puede estar vacío.";
          return false;
      }

      error.textContent = "";
      return true;
    }


     function validarEntero(campoId1,campoId2, errorId1, errorId2) {
      const campo1 = document.getElementById(campoId1);//Stock actual
      const campo2 = document.getElementById(campoId2);//Stock minimo
      const error1 = document.getElementById(errorId1);//mensaje Stock actual
      const error2 = document.getElementById(errorId2);//mensaje Stock minimo
      const regex = /^[1-9]\d*$/; // Solo permite números enteros positivos (mayores a 0)
  
      if (!campo1.value.trim()) {
          error1.textContent = "Este campo no puede estar vacío.";
          return false;
      }else{
           error2.textContent = "";
      }
      if (!regex.test(campo1.value)) {
          error1.textContent = "Solo se permiten números enteros positivos (mayores a 0).";
          return false;
      }

      const campoInt1 = parseInt(campo1.value.trim());
      const campoInt2 = parseInt(campo2.value.trim());

      if (campoInt1 <= campoInt2){
          error1.textContent = "El stock Actual no puede ser menor o igual al stock minimo";
          return false;
      }else if (campoInt1 > campoInt2){
         error2.textContent = "";
      }
      
      // Si todo está en orden, se limpia el mensaje de error
      error1.textContent = "";
      return true;
    }

    //Valida el stock mínimo
    function validarEnteroMin(campoId1,campoId2, errorId1,errorId2) {
      const campo1 = document.getElementById(campoId1);//Stock actual
      const campo2 = document.getElementById(campoId2);//Stock minimo
      const error1 = document.getElementById(errorId1);//mensaje Stock actual
      const error2 = document.getElementById(errorId2);//mensaje Stock minimo
      const regex = /^[0-9]\d*$/; // Solo permite números enteros positivos (mayores a 0)

      if (!campo2.value.trim()) {
          error2.textContent = "Este campo no puede estar vacío.";
          return false;
      }
      if (!regex.test(campo2.value)) {
          error2.textContent = "Solo se permiten números enteros positivos.";
          return false;
      }

      if(!campo1.value.trim()){
          error2.textContent = "Primero llene el campo stock actual";
          return false;
      }

      const campoInt1 = parseInt(campo1.value.trim());
      const campoInt2 = parseInt(campo2.value.trim());

      if (campoInt2 >= campoInt1){
          error2.textContent = "El stock mínimo no puede ser mayor o igual al stock actual";
          return false;
      }else if (campoInt1 > campoInt2){
         error1.textContent = "";
      }
      // Si todo está en orden, se limpia el mensaje de error
      error2.textContent = "";
      return true;
    }
  
    function validarDecimal(campoId, errorId) {
      const campo = document.getElementById(campoId);
      const error = document.getElementById(errorId);
      const regex = /^(?!0(\.00)?)\d+(\.\d{2})$/; // Permite solo números positivos con 2 decimales, mayor a 0
  
      if (!campo.value.trim()) {
          error.textContent = "Este campo no puede estar vacío.";
          return false;
      }
      if (!regex.test(campo.value)) {
          error.textContent = "Debe ingresar un número decimal positivo mayor a 0 de 2 decimales.";
          return false;
      }
      // Si todo está en orden, se limpia el mensaje de error
      error.textContent = "";
      return true;
    }

    function validarDecimal2(campoId, errorId) {
      const campo = document.getElementById(campoId);
      const error = document.getElementById(errorId);
      //const regex = /^(?:0|[1-9]\d*)(\.\d{2})?$/; // Acepta 0.00 y números positivos con hasta 2 decimales
      const regex = /^(?:0\.00|[1-9]\d*(\.\d{2})?)$/;
      if (!campo.value.trim()) {
          error.textContent = "Este campo no puede estar vacío.";
          return false;
      }
      if (!regex.test(campo.value)) {
          error.textContent = "Debe ingresar un número decimal positivo mayor igual a 0 de 2 decimales.";
          return false;
      }
      // Si todo está en orden, limpia el mensaje de error
      error.textContent = "";
      return true;
    }
  

  });

/*Funcion para mostrar los datos de los productos en el modal para editarlos*/
$(document).ready(function () {
    // Manejar el evento de clic en el botón de editar producto
    $('.btn-editar-producto').click(function () {
      // Obtener los datos del producto del atributo data-
      var codigo = $(this).data('codigo');
      var categoria = $(this).data('categoria');
      var nombre = $(this).data('nombre');
      var descripcion = $(this).data('descripcion');
      var stock = $(this).data('stock');
      var stockMin = $(this).data('stock-min');
      var precio = $(this).data('precio');
      var disponibilidad = $(this).data('disponibilidad');
      var estado = $(this).data('estado');

      //var estado = string(estado);
      
      //var disponibilidad_valor = disponibilidad === 1 ? 'Disponible' : 'Agotado';
    //var estado_valor = estado === 1 ? 'Activo' : 'Inactivo';

      
      
      // Llenar los campos del modal con los datos del producto
      $('#editProductModal #codigoEditarP').val(codigo).prop('readonly', true); // El campo de código es de solo lectura
      $('#editProductModal #categoriaEditarP').val(categoria);
      $('#editProductModal #nombreEditarP').val(nombre);
      $('#editProductModal #descripcionEditarP').val(descripcion);
      $('#editProductModal #stockEditarP').val(stock);
      $('#editProductModal #stockMinEditarP').val(stockMin);
      $('#editProductModal #precioEditarP').val(precio);
      $('#editProductModal #disponibilidadP').val(disponibilidad).prop('readonly', true);
      $('#editProductModal #estadoEditarP').val(estado);

      
    });
  });

  $(document).ready(function () {
    $('#formEditarProducto').submit(function (e) {
      e.preventDefault(); // Evitar el envío normal del formulario
  
      if(validarFormularioEditP()){
        // Crear un objeto FormData para enviar archivos
        var formData = new FormData(this);
        formData.append('accion', 'actualizar'); // Añadir un parámetro extra
  
        $.ajax({
            type: 'POST',
            url: 'productos-admin.php',
            data: formData,
            dataType: 'json',
            contentType: false, // Importante para enviar archivos
            processData: false, // Importante para enviar archivos
            success: function (data) {
              if (data.status === "success") {
                  Swal.fire({
                      icon: "success",
                      title: "Éxito",
                      text: data.message,
                      confirmButtonColor: "#3085d6",
                      allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                  }).then(() => {
                      location.reload();
                  });
              } else {
                  Swal.fire({
                      icon: "error",
                      title: "Error",
                      text: data.message,
                      confirmButtonColor: "#d33",
                      allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                  });
              }
            },
            error: function (xhr, status, error) {
                console.log("Respuesta completa del servidor:", xhr.responseText);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Hubo un problema al actualizar el producto: " + xhr.responseText,
                    confirmButtonColor: "#d33",
                    allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                });
            }
          });
      }else{
        console.log("Validación fallida, no se envían datos.");
      }
      
    });
  
     // Asigna eventos de validación en tiempo real

     //Validar nomnbre
     document.getElementById("nombreEditarP").addEventListener("input", function () {
      validarCampoVacio("nombreEditarP", "validaNombreEditarP");
    });
    /*
     //Validar stock
    document.getElementById("stockEditarP").addEventListener("input", function () {
      validarEntero("stockEditarP","stockMinEditarP", "validaStockEditarP","validaStockMinEditarP");
    });

     //Validar stock minimo
    document.getElementById("stockMinEditarP").addEventListener("input", function () {
      validarEnteroMinEdit("stockEditarP","stockMinEditarP","validaStockEditarP", "validaStockMinEditarP");
    });
    */
    //Validar Precio
    document.getElementById("precioEditarP").addEventListener("input", function () {
      validarDecimal("precioEditarP", "validaPrecioEditarP");
    });

  
   
    //Función de verificación de los campos: Permite el envio de los datos siemore y cuando este sea verdadero
    function validarFormularioEditP() {
      console.log("Ejecutando validación del formulario de agregar...");
      let valido = true; // Inicia como válido
  
      // Ejecuta cada validación y actualiza 'valido' correctamente
      valido = validarCampoVacio("nombreEditarP", "validaNombreEditarP") && valido;
      /*
      valido = validarEntero("stockEditarP","stockMinEditarP", "validaStockEditarP","validaStockMinEditarP") && valido;
      valido = validarEnteroMinEdit("stockEditarP","stockMinEditarP","validaStockEditarP", "validaStockMinEditarP") && valido;
      */
      valido = validarDecimal("precioEditarP", "validaPrecioEditarP") && valido;
      console.log("Resultado de validación:", valido);
      return valido;
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
  
  
    function validarCampoVacio(campoId, errorId) {
      const campo = document.getElementById(campoId);
      const error = document.getElementById(errorId);
  
      if (!campo.value.trim()) {
          error.textContent = "Este campo no puede estar vacío.";
          return false;
      }
  
      error.textContent = "";
      return true;
    }
  
  
    function validarEntero(campoId1,campoId2, errorId1, errorId2) {
      const campo1 = document.getElementById(campoId1);//Stock actual
      const campo2 = document.getElementById(campoId2);//Stock minimo
      const error1 = document.getElementById(errorId1);//mensaje Stock actual
      const error2 = document.getElementById(errorId2);//mensaje Stock minimo
      const regex = /^[1-9]\d*$/; // Solo permite números enteros positivos (mayores a 0)
  
      if (!campo1.value.trim()) {
          error1.textContent = "Este campo no puede estar vacío.";
          return false;
      }else{
           error2.textContent = "";
      }
      if (!regex.test(campo1.value)) {
          error1.textContent = "Solo se permiten números enteros positivos (mayores a 0).";
          return false;
      }

      const campoInt1 = parseInt(campo1.value.trim());
      const campoInt2 = parseInt(campo2.value.trim());

      if (campoInt1 <= campoInt2){
          error1.textContent = "El stock Actual no puede ser menor o igual al stock minimo";
          return false;
      }else if (campoInt1 > campoInt2){
         error2.textContent = "";
      }
      
      // Si todo está en orden, se limpia el mensaje de error
      error1.textContent = "";
      return true;
    }

    //Valida el stock mínimo
    function validarEnteroMinEdit(campoId1,campoId2, errorId1,errorId2) {
      const campo1 = document.getElementById(campoId1);//Stock actual
      const campo2 = document.getElementById(campoId2);//Stock minimo
      const error1 = document.getElementById(errorId1);//mensaje Stock actual
      const error2 = document.getElementById(errorId2);//mensaje Stock minimo
      const regex = /^[0-9]\d*$/; // Solo permite números enteros positivos (mayores a 0)

      if (!campo2.value.trim()) {
          error2.textContent = "Este campo no puede estar vacío.";
          return false;
      }
      if (!regex.test(campo2.value)) {
          error2.textContent = "Solo se permiten números enteros positivos.";
          return false;
      }

      if(!campo1.value.trim()){
          error2.textContent = "Primero llene el campo stock actual";
          return false;
      }

      const campoInt1 = parseInt(campo1.value.trim());
      const campoInt2 = parseInt(campo2.value.trim());

      if (campoInt2 >= campoInt1){
          error2.textContent = "El stock mínimo no puede ser mayor o igual al stock actual";
          return false;
      }else if (campoInt1 > campoInt2){
         error1.textContent = "";
      }
      // Si todo está en orden, se limpia el mensaje de error
      error2.textContent = "";
      return true;
    }
  
    function validarDecimal(campoId, errorId) {
      const campo = document.getElementById(campoId);
      const error = document.getElementById(errorId);
      const regex = /^(?!0(\.00)?)\d+(\.\d{2})$/; // Permite solo números positivos con 2 decimales, mayor a 0
  
      if (!campo.value.trim()) {
          error.textContent = "Este campo no puede estar vacío.";
          return false;
      }
      if (!regex.test(campo.value)) {
          error.textContent = "Debe ingresar un número decimal positivo mayor a 0 de 2 decimales.";
          return false;
      }
      // Si todo está en orden, se limpia el mensaje de error
      error.textContent = "";
      return true;
    }
  
    function validarDecimal2(campoId, errorId) {
      const campo = document.getElementById(campoId);
      const error = document.getElementById(errorId);
      //const regex = /^(?:0|[1-9]\d*)(\.\d{2})?$/; // Acepta 0.00 y números positivos con hasta 2 decimales
      const regex = /^(?:0\.00|[1-9]\d*(\.\d{2})?)$/;
      if (!campo.value.trim()) {
          error.textContent = "Este campo no puede estar vacío.";
          return false;
      }
      if (!regex.test(campo.value)) {
          error.textContent = "Debe ingresar un número decimal positivo mayor igual a 0 de 2 decimales.";
          return false;
      }
      // Si todo está en orden, limpia el mensaje de error
      error.textContent = "";
      return true;
    }
  
  
  });

//Eliminar producto
  $('.btn-eliminar-producto').click(function () {
    var codigoProducto = $(this).data('codigo');
  
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción eliminará el producto permanentemente.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        allowOutsideClick: false // Evita que se cierre al hacer clic fuera
    }).then((result) => {
        if (result.isConfirmed) {
            console.log("Enviando código a AJAX:", codigoProducto);
  
            $.ajax({
                type: 'POST',
                url: 'productos-admin.php',
                data: {
                    codigo: codigoProducto,
                    elimina: 'eliminar'
                },
                dataType: 'json',
                success: function (data) {
                    if (data.status === "success") {
                        Swal.fire({
                            icon: "success",
                            title: "Eliminado",
                            text: data.message,
                            confirmButtonColor: "#3085d6",
                            allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: data.message,
                            confirmButtonColor: "#d33",
                            allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log("Respuesta completa del servidor:", xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Hubo un problema al eliminar el producto: " + xhr.responseText,
                        confirmButtonColor: "#d33",
                        allowOutsideClick: false, // Evita que se cierre al hacer clic fuera
                    });
                }
            });
        } else {
            console.log("Eliminación cancelada por el usuario.");
        }
    });
  });