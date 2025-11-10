/*
document.addEventListener('DOMContentLoaded', () => {
    const categoryFilters = document.querySelectorAll('.category-filter');
    const productGrid = document.getElementById('product-grid');

    // Escuchar cambios en los checkboxes
    categoryFilters.forEach(filter => {
        filter.addEventListener('change', () => {
            const selectedCategories = Array.from(categoryFilters)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            // Emitir un evento personalizado para el filtrado
            const filterEvent = new CustomEvent('filterProducts', {
                detail: { categories: selectedCategories }
            });
            productGrid.dispatchEvent(filterEvent);
        });
    });
});
*/