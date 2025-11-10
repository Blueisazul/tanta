<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <?php 
    include 'components/Navbar.php'; 
    ?>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Resumen de Compra</h1>

        <ul id="lista-productos" class="mt-4 space-y-2"></ul>

        <p id="total-pago" class="font-bold mt-4"></p>

        <button onclick="finalizarCompra()" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-4">
            Finalizar compra
        </button>
    </div>
</body>
</html>