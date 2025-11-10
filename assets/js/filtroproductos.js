document.addEventListener("DOMContentLoaded", function () {
    // Se guardan los id por los inputs de búsqueda
    const filtroCategoria = document.getElementById("filtroCategoria");
    const filtroDisponibilidad = document.getElementById("filtroDisponibilidad");
    const filtroEstado = document.getElementById("filtroEstado");
    const buscarCodigo = document.getElementById("buscarCodigo");
    const buscarNombre = document.getElementById("buscarNombre");

    //Se selecciona el apartado de la tabla
    //Selecciona el cuerpo de la tabla (<tbody>) donde están listados los productos. 
    // Se asume que los productos están en filas <tr> dentro de esta tabla.
    const tabla = document.querySelector(".table tbody");

    //Obtener las categorias seleccionadas
    function obtenerCategoriasSeleccionadas() {
        const seleccionadas = Array.from(filtroCategoria.selectedOptions).map(option => option.value);
        return seleccionadas.includes("0") ? [] : seleccionadas; // Si selecciona "Todas" (0), retorna un array vacío
    }

    function obtenerDisponibilidad() {
        const seleccionadass = Array.from(filtroDisponibilidad.selectedOptions).map(option => option.value);
        return seleccionadass.includes("3") ? [] : seleccionadass; // Si selecciona "Todas" (0), retorna un array vacío
    }

    function obtenerEstado() {
        const seleccionadasss = Array.from(filtroEstado.selectedOptions).map(option => option.value);
        return seleccionadasss.includes("2") ? [] : seleccionadasss; // Si selecciona "Todas" (0), retorna un array vacío
    }


    //Acción de la realizar el filtrado de productos
    function filtrarProductos() {
        //Se guardan los valores seleccionados en variables
        const filtroCodigo = buscarCodigo.value.toLowerCase();
        const filtroNombre = buscarNombre.value.toLowerCase();
        const categoriasSeleccionadas = obtenerCategoriasSeleccionadas();
        const filtroDispo = obtenerDisponibilidad();
        const filtroEstad = obtenerEstado();

        //Itera por cada fila de la tabla (cada producto).
        Array.from(tabla.getElementsByTagName("tr")).forEach((fila) => {
            //Extrae el texto del código del producto.
            //Extrae el texto de la descripción.
            //Obtiene el atributo personalizado data-id-categoria que representa la categoría de esa fila.
            const codigo = fila.querySelector(".codigo")?.textContent.toLowerCase() || "";
            const nombre = fila.querySelector(".nombre")?.textContent.toLowerCase() || "";
            const idCategoria = fila.getAttribute("data-categoria");
            const disponi = fila.getAttribute("data-disponibilidad");
            const esta = fila.getAttribute("data-estado");



            //El código contiene lo que se escribió en el input.
            //La descripción también lo contiene.
            //La categoría coincide o no se está filtrando por categoría (length === 0).


            const coincideCodigo = codigo.includes(filtroCodigo);
            const coincideNombre = nombre.includes(filtroNombre);
            const coincideCategoria = (categoriasSeleccionadas.length === 0 || categoriasSeleccionadas.includes(idCategoria));
            const coincideDisponibilidad = (filtroDispo.length === 0 || filtroDispo.includes(disponi));
            const coincideEstado = (filtroEstad.length === 0 || filtroEstad.includes(esta));
            //Si los tres criterios coinciden, muestra la fila. Si no, la oculta (display: none).
            fila.style.display = (coincideCodigo && coincideNombre && coincideCategoria && coincideDisponibilidad && coincideEstado) ? "" : "none";
        });
    }

    filtroCategoria.addEventListener("change", filtrarProductos);
    filtroDisponibilidad.addEventListener("change", filtrarProductos);
    filtroEstado.addEventListener("change", filtrarProductos);
    buscarCodigo.addEventListener("input", filtrarProductos);
    buscarNombre.addEventListener("input", filtrarProductos);
});