const searchInput = document.getElementById('buscar-servicios');
const filterButtons = document.querySelectorAll('.filtro');
const serviceCards = document.querySelectorAll('.servicio-card');
const openServiceButton = document.getElementById('abrir-servicio');
const serviceModal = document.getElementById('modal-servicio');
const closeServiceButton = document.getElementById('cerrar-servicio');
const closeBackdrop = document.getElementById('cerrar-fondo-servicio');
const resultCount = document.getElementById('resultado-count');
const serviceForm = serviceModal ? serviceModal.querySelector('form') : null;
const negocioId = serviceForm ? serviceForm.querySelector('input[name="negocio_id"]').value : '';
let currentFilter = 'todos';

// Asigna índice de aparición escalonada a cada tarjeta
serviceCards.forEach((card, i) => {
    card.style.setProperty('--i', i);
});

function actualizarContador() {
    if (!resultCount) return;
    const visibles = [...serviceCards].filter((card) => !card.hidden).length;
    resultCount.textContent = serviceCards.length > 0
        ? visibles + ' de ' + serviceCards.length + ' servicios'
        : '';
}

function filterServices() {
    const query = searchInput ? searchInput.value.trim().toLowerCase() : '';

    serviceCards.forEach((card) => {
        const matchesFilter = currentFilter === 'todos' || card.dataset.category.includes(currentFilter);
        const matchesSearch = query === '' || card.dataset.name.includes(query);
        card.hidden = !(matchesFilter && matchesSearch);
    });
    actualizarContador();
}

filterButtons.forEach((button) => {
    button.addEventListener('click', () => {
        filterButtons.forEach((item) => item.classList.remove('active'));
        button.classList.add('active');
        currentFilter = button.dataset.filter;
        filterServices();
    });
});

if (searchInput) {
    searchInput.addEventListener('input', filterServices);
}

function abrirModal() {
    if (!serviceModal) return;
    serviceModal.hidden = false;
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    if (!serviceModal) return;
    serviceModal.hidden = true;
    document.body.style.overflow = '';
}

// Reinicia el modal para el modo "Agregar"
function resetearFormulario() {
    if (!serviceForm) return;
    serviceForm.reset();
    serviceForm.querySelector('input[name="action"]').value = 'guardar';
    serviceForm.querySelector('input[name="servicio_id"]').value = '0';
    serviceForm.querySelector('#servicio-modal-titulo').textContent = 'Agregar Servicio';
    serviceForm.querySelector('.btn-guardar-servicio').textContent = 'Guardar Servicio';
    const preview = serviceForm.querySelector('#servicio-imagen-preview');
    preview.classList.remove('visible');
    preview.removeAttribute('src');
}

// Rellena el formulario con los datos de un servicio existente
function rellenarEdicion(boton) {
    if (!serviceForm) return;
    serviceForm.reset();
    serviceForm.querySelector('input[name="action"]').value = 'actualizar';
    serviceForm.querySelector('input[name="servicio_id"]').value = boton.dataset.id;
    serviceForm.querySelector('#servicio-modal-titulo').textContent = 'Editar Servicio';
    serviceForm.querySelector('#servicio-modal-titulo').parentElement.querySelector('p').textContent = 'Modifica los datos del servicio';
    serviceForm.querySelector('.btn-guardar-servicio').textContent = 'Guardar Cambios';
    serviceForm.querySelector('#servicio-nombre').value = boton.dataset.nombre || '';
    serviceForm.querySelector('#servicio-categoria').value = boton.dataset.categoria || '';
    serviceForm.querySelector('#servicio-descripcion').value = boton.dataset.descripcion || '';
    serviceForm.querySelector('#servicio-precio').value = boton.dataset.precio || '';
    serviceForm.querySelector('#servicio-duracion').value = boton.dataset.duracion || '';
    serviceForm.querySelector('#servicio-estado').value = boton.dataset.estado || 'disponible';

    const preview = serviceForm.querySelector('#servicio-imagen-preview');
    if (boton.dataset.imagen) {
        preview.src = boton.dataset.imagen;
        preview.classList.add('visible');
    } else {
        preview.classList.remove('visible');
        preview.removeAttribute('src');
    }
}

if (openServiceButton) {
    openServiceButton.addEventListener('click', () => {
        resetearFormulario();
        abrirModal();
    });
}

document.querySelectorAll('[data-editar]').forEach((boton) => {
    boton.addEventListener('click', () => {
        rellenarEdicion(boton);
        abrirModal();
    });
});

document.querySelectorAll('[data-eliminar]').forEach((boton) => {
    boton.addEventListener('click', () => {
        const nombre = boton.dataset.nombre || 'este servicio';
        if (!window.confirm('¿Eliminar el servicio "' + nombre + '"? Esta acción no se puede deshacer.')) return;

        const formulario = document.createElement('form');
        formulario.method = 'post';
        formulario.action = 'controller/ServicioController.php';
        const datos = {
            action: 'eliminar',
            servicio_id: boton.dataset.id,
            negocio_id: negocioId,
        };
        Object.keys(datos).forEach((clave) => {
            const campo = document.createElement('input');
            campo.type = 'hidden';
            campo.name = clave;
            campo.value = datos[clave];
            formulario.appendChild(campo);
        });
        document.body.appendChild(formulario);
        formulario.submit();
    });
});

if (closeServiceButton) {
    closeServiceButton.addEventListener('click', cerrarModal);
}

if (closeBackdrop) {
    closeBackdrop.addEventListener('click', cerrarModal);
}

document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape') cerrarModal();
});

// Preview de la imagen seleccionada del servicio
const imagenInput = document.getElementById('servicio-imagen-input');
if (imagenInput) {
    imagenInput.addEventListener('change', () => {
        const archivo = imagenInput.files && imagenInput.files[0];
        const preview = document.getElementById('servicio-imagen-preview');
        if (archivo && archivo.type.startsWith('image/')) {
            const lector = new FileReader();
            lector.onload = (e) => {
                preview.src = e.target.result;
                preview.classList.add('visible');
            };
            lector.readAsDataURL(archivo);
        }
    });
}

if (serviceForm) {
    serviceForm.addEventListener('submit', () => {
        const btn = serviceForm.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Guardando...';
    });
}

actualizarContador();

// Auto-esconde el mensaje del sistema
const mensajeSistema = document.getElementById('sistema-mensaje');
if (mensajeSistema) {
    setTimeout(() => {
        mensajeSistema.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        mensajeSistema.style.opacity = '0';
        mensajeSistema.style.transform = 'translateY(-8px)';
    }, 3200);
}