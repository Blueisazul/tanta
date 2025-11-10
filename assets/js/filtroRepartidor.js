document.addEventListener("DOMContentLoaded", function () {
    // Inputs de búsqueda
    const buscarDNI = document.getElementById("buscarDNI");
    const buscarNombre = document.getElementById("buscarNombre");
    const buscarApellido = document.getElementById("buscarApellido");
    const buscarTelefono = document.getElementById("buscarTelefono");
    const buscarCorreo = document.getElementById("buscarCorreo");
    const buscarGenero = document.getElementById("buscarGenero");
    const buscarEstadoU = document.getElementById("buscarEstadoU");
    const buscarEstadoR = document.getElementById("buscarEstadoR");

    // Selecciona el cuerpo de la tabla
    const tabla = document.querySelector("table tbody");

    function filtrarRepartidores() {
        const filtroDNI = buscarDNI.value.trim().toLowerCase();
        const filtroNombre = buscarNombre.value.trim().toLowerCase();
        const filtroApellido = buscarApellido.value.trim().toLowerCase();
        const filtroTelefono = buscarTelefono.value.trim().toLowerCase();
        const filtroCorreo = buscarCorreo.value.trim().toLowerCase();
        const filtroGenero = buscarGenero.value;
        const filtroEstadoU = buscarEstadoU.value;
        const filtroEstadoR = buscarEstadoR.value;

        Array.from(tabla.getElementsByTagName("tr")).forEach((fila) => {
            const dni = fila.querySelector(".dni")?.textContent.toLowerCase() || "";
            const nombre = fila.querySelector(".nombre")?.textContent.toLowerCase() || "";
            const apellido = fila.querySelector(".apellido")?.textContent.toLowerCase() || "";
            const telefono = fila.querySelector(".telefono")?.textContent.toLowerCase() || "";
            const correo = fila.querySelector(".correo")?.textContent.toLowerCase() || "";

            const genero = fila.getAttribute("data-genero");
            const estadoU = fila.getAttribute("data-estadou");
            const estadoR = fila.getAttribute("data-estador");

            const coincideDNI = dni.includes(filtroDNI);
            const coincideNombre = nombre.includes(filtroNombre);
            const coincideApellido = apellido.includes(filtroApellido);
            const coincideTelefono = telefono.includes(filtroTelefono);
            const coincideCorreo = correo.includes(filtroCorreo);
            const coincideGenero = (filtroGenero === "3" || genero === filtroGenero);
            const coincideEstadoU = (filtroEstadoU === "2" || estadoU === filtroEstadoU);
            const coincideEstadoR = (filtroEstadoR === "2" || estadoR === filtroEstadoR);

            fila.style.display = (coincideDNI && coincideNombre && coincideApellido && coincideTelefono && coincideCorreo && coincideGenero && coincideEstadoU && coincideEstadoR) ? "" : "none";
        });
    }

    buscarDNI.addEventListener("input", filtrarRepartidores);
    buscarNombre.addEventListener("input", filtrarRepartidores);
    buscarApellido.addEventListener("input", filtrarRepartidores);
    buscarTelefono.addEventListener("input", filtrarRepartidores);
    buscarCorreo.addEventListener("input", filtrarRepartidores);
    buscarGenero.addEventListener("change", filtrarRepartidores);
    buscarEstadoU.addEventListener("change", filtrarRepartidores);
    buscarEstadoR.addEventListener("change", filtrarRepartidores);
});