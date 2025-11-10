document.addEventListener("DOMContentLoaded", function () {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const lista = document.getElementById("lista-productos");
  const totalElemento = document.getElementById("total-pago");

  let total = 0;

  if (cart.length === 0) {
    lista.innerHTML = "<p class='text-gray-500 text-center'>Tu carrito está vacío.</p>";
    totalElemento.textContent = "Total: S/0.00";
  } else {
    cart.forEach(product => {
      const item = document.createElement("li");
      item.textContent = `${product.title} - S/${parseFloat(product.price).toFixed(2)}`;
      lista.appendChild(item);
      total += parseFloat(product.price) || 0;
    });

    totalElemento.textContent = "Total a pagar: S/" + total.toFixed(2);
  }
});

function finalizarCompra() {
  localStorage.removeItem("cart");
  alert("¡Compra realizada con éxito!");
  window.location.href = "/";
}