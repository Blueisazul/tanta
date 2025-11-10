document.addEventListener("DOMContentLoaded", function () {
    // Se guardan los id por los inputs de búsqueda
    const filtroEstado = document.getElementById("filtroEstado");
    const fechaDesde = document.getElementById("fechaDesde");
    const fechaHasta = document.getElementById("fechaHasta");
    const buscarCodigo = document.getElementById("buscarCodigo");


    //Se selecciona el apartado de la tabla
    //Selecciona el cuerpo de la tabla (<tbody>) donde están listados los productos. 
    // Se asume que los productos están en filas <tr> dentro de esta tabla.
    const tabla = document.querySelector(".table tbody");

    //Obtener las categorias seleccionadas
    /*
    function obtenerCategoriasSeleccionadas() {
        const seleccionadas = Array.from(filtroCategoria.selectedOptions).map(option => option.value);
        return seleccionadas.includes("0") ? [] : seleccionadas; // Si selecciona "Todas" (0), retorna un array vacío
    }

    function obtenerDisponibilidad() {
        const seleccionadass = Array.from(filtroDisponibilidad.selectedOptions).map(option => option.value);
        return seleccionadass.includes("3") ? [] : seleccionadass; // Si selecciona "Todas" (0), retorna un array vacío
    }
     */
    function obtenerEstado() {
        const seleccionadass = Array.from(filtroEstado.selectedOptions).map(option => option.value);
        return seleccionadass.includes("4") ? [] : seleccionadass; // Si selecciona "Todas" (0), retorna un array vacío
    }
   
    //Acción de la realizar el filtrado de productos
    function filtrarPedidos() {
        //Se guardan los valores seleccionados en variables
        /*
        const filtroCodigo = buscarCodigo.value.toLowerCase();
        const filtroNombre = buscarNombre.value.toLowerCase();
        const categoriasSeleccionadas = obtenerCategoriasSeleccionadas();
        const filtroDispo = obtenerDisponibilidad();
        */
        const filtroEstado = obtenerEstado();
        const desde = fechaDesde.value ? new Date(fechaDesde.value) : null;
        const hasta = fechaHasta.value ? new Date(fechaHasta.value) : null;
        const filtroCodigo = buscarCodigo.value.trim().toLowerCase();
        //Itera por cada fila de la tabla (cada producto).
        Array.from(tabla.getElementsByTagName("tr")).forEach((fila) => {

            const estado = fila.getAttribute("data-estadoo");
            // Obtener la fecha de la celda (segunda columna)
            const fechaTexto = fila.cells[1]?.textContent.trim();

            // Obtener el N° Pedido (primera columna)
            const codigoTexto = fila.cells[0]?.textContent.replace('#', '').trim().toLowerCase();
            
            const coincideCodigo = codigoTexto.includes(filtroCodigo);
            

            const coincideEstado = (filtroEstado.length === 0 || filtroEstado.includes(estado));

             // Formato esperado: 'dd/mm/yyyy hh:mm'
            let coincideFecha = true;
            if (fechaTexto) {
                const [fecha, hora] = fechaTexto.split(' ');
                const [dia, mes, anio] = fecha.split('/');
                const fechaPedido = new Date(`${anio}-${mes}-${dia}T${hora || '00:00'}`);

                if (desde && fechaPedido < desde) coincideFecha = false;
                if (hasta && fechaPedido > hasta) coincideFecha = false;
            }



            //Si los tres criterios coinciden, muestra la fila. Si no, la oculta (display: none).
            fila.style.display = (coincideEstado && coincideFecha && coincideCodigo) ? "" : "none";
        });
    }
    /*
    filtroCategoria.addEventListener("change", filtrarProductos);
    filtroDisponibilidad.addEventListener("change", filtrarProductos);
    buscarCodigo.addEventListener("input", filtrarProductos);
    buscarNombre.addEventListener("input", filtrarProductos);
    */
    filtroEstado.addEventListener("change", filtrarPedidos);
    if (fechaDesde) fechaDesde.addEventListener("change", filtrarPedidos);
    if (fechaHasta) fechaHasta.addEventListener("change", filtrarPedidos);
    if (buscarCodigo) buscarCodigo.addEventListener("input", filtrarPedidos);
});