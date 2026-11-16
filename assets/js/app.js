// =========================================================
// Programación orientada a eventos en el navegador
// El código no se ejecuta en orden de arriba hacia abajo como en la
// línea de comandos: espera a que el usuario haga algo (enviar un
// formulario, escribir, hacer clic) y responde a ese evento.
// =========================================================

document.addEventListener('DOMContentLoaded', () => {

    // --- Evento "submit": confirmar antes de eliminar un contacto ---
    document.querySelectorAll('.form-eliminar').forEach((form) => {
        form.addEventListener('submit', (evento) => {
            const nombre = form.dataset.nombre || 'este contacto';
            if (!confirm(`¿Seguro que desea eliminar a "${nombre}"? Esta acción no se puede deshacer.`)) {
                evento.preventDefault(); // cancela el envío si el usuario dice "Cancelar"
            }
        });
    });

    // --- Evento "submit": validación rápida en el navegador ---
    // (La validación real y obligatoria se hace en el servidor con PHP.)
    document.querySelectorAll('form[data-validar]').forEach((form) => {
        form.addEventListener('submit', (evento) => {
            let valido = true;

            form.querySelectorAll('input[required]').forEach((campo) => {
                let mensaje = '';

                if (campo.value.trim() === '') {
                    mensaje = 'Este campo es obligatorio.';
                } else if (!campo.checkValidity()) {
                    mensaje = 'Revise el formato de este campo.';
                }

                // Confirmar que las dos contraseñas sean iguales
                if (!mensaje && campo.dataset.igualA) {
                    const otro = form.querySelector('#' + campo.dataset.igualA);
                    if (otro && otro.value !== campo.value) {
                        mensaje = 'Las contraseñas no coinciden.';
                    }
                }

                marcarCampo(campo, mensaje);
                if (mensaje) valido = false;
            });

            if (!valido) evento.preventDefault();
        });

        // --- Evento "input": quitar el error mientras el usuario corrige ---
        form.querySelectorAll('input').forEach((campo) => {
            campo.addEventListener('input', () => marcarCampo(campo, ''));
        });
    });

    // --- Evento "input": filtrar la tabla de contactos al escribir ---
    const buscador = document.getElementById('buscador');
    if (buscador) {
        buscador.addEventListener('input', () => {
            const texto = buscador.value.toLowerCase();
            document.querySelectorAll('#tabla-contactos tbody tr').forEach((fila) => {
                fila.style.display = fila.textContent.toLowerCase().includes(texto) ? '' : 'none';
            });
        });
    }
});

// Muestra u oculta el mensaje de error debajo de un campo
function marcarCampo(campo, mensaje) {
    let aviso = campo.nextElementSibling;
    if (!aviso || !aviso.classList.contains('error-campo')) {
        aviso = document.createElement('small');
        aviso.className = 'error-campo';
        campo.insertAdjacentElement('afterend', aviso);
    }
    aviso.textContent = mensaje;
    campo.classList.toggle('campo-invalido', mensaje !== '');
}
