/* ============================================
   AGENDAPRO — Script principal del panel
   ============================================ */

// --- Sidebar ---
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebarOverlay = document.getElementById('sidebar-overlay');

function toggleSidebar() {
    const isMobile = window.innerWidth <= 768;
    const stateClass = isMobile ? 'sidebar-open' : 'sidebar-collapsed';
    document.body.classList.toggle(stateClass);
    const isVisible = isMobile
        ? document.body.classList.contains('sidebar-open')
        : !document.body.classList.contains('sidebar-collapsed');

    sidebarToggle.setAttribute('aria-expanded', String(isVisible));
    sidebarToggle.setAttribute('aria-label', isVisible ? 'Ocultar menú' : 'Mostrar menú');
}

if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleSidebar);
}

if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', toggleSidebar);
}

document.querySelectorAll('.btn-nav').forEach((link) => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            document.body.classList.remove('sidebar-open');
        }
    });
});

// --- Sistema global de toasts ---
(function () {
    let contenedor = document.getElementById('toast-contenedor');
    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.id = 'toast-contenedor';
        contenedor.className = 'toast-contenedor';
        document.body.appendChild(contenedor);
    }

    window.mostrarToast = function (mensaje, tipo = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast toast-' + tipo;
        toast.innerHTML = mensaje;
        contenedor.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('visible'));
        setTimeout(() => {
            toast.classList.remove('visible');
            setTimeout(() => toast.remove(), 350);
        }, 3000);
    };
})();

// --- Revelado al hacer scroll (animaciones de aparición) ---
(function () {
    const elementos = document.querySelectorAll('.profile-card, .panel-card, .stat-card, .servicio-card, .negocio-card');
    if (!('IntersectionObserver' in window)) return;

    const observador = new IntersectionObserver((entradas) => {
        entradas.forEach((entrada) => {
            if (entrada.isIntersecting) {
                entrada.target.style.animationDelay = '0s';
                observador.unobserve(entrada.target);
            }
        });
    }, { threshold: 0.05 });

    elementos.forEach((el) => observador.observe(el));
})();