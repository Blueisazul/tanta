    <div class="iconos-flotantes">
        <div class="icono-circular icono-whatsapp" 
            onclick="window.open('https://wa.me/962779835?text=Hola,%20quiero%20obtener%20más%20información%20sobre%20los%20productos.', '_blank');">
            <span class="fab fa-whatsapp"></span> <!-- Cambios del texto en whatsaap -->
        </div>
    </div>
    <script>
        const iconoFlotante = document.querySelector('.iconos-flotantes');
        const footer = document.querySelector('footer'); // Asegúrate de que tu footer tenga la etiqueta <footer>

        let footerVisible = false;

        if (footer) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    footerVisible = entry.isIntersecting;
                    if (footerVisible) {
                        iconoFlotante.style.opacity = '0';
                        iconoFlotante.style.visibility = 'hidden';
                    } else if (window.scrollY > 10) {
                        iconoFlotante.style.opacity = '1';
                        iconoFlotante.style.visibility = 'visible';
                    }
                });
            }, { threshold: 0.1 });
            observer.observe(footer);
        }

        window.addEventListener('scroll', function() {
            if (window.scrollY > 10 && !footerVisible) {
                iconoFlotante.style.opacity = '1';
                iconoFlotante.style.visibility = 'visible';
            } else {
                iconoFlotante.style.opacity = '0';
                iconoFlotante.style.visibility = 'hidden';
            }
        });
    </script>