// ---------- Menú móvil ----------

const menuToggle = document.getElementById('menuToggle');
const mainNav = document.getElementById('mainNav');

menuToggle.addEventListener('click', () => {
  const isOpen = mainNav.classList.toggle('open');
  menuToggle.setAttribute('aria-expanded', isOpen);
});

// ---------- Tamaño de letra (se guarda entre páginas) ----------

const tamañoGuardado = localStorage.getItem('tamañoTexto');
if (tamañoGuardado) {
  document.body.classList.add(tamañoGuardado);
}

document.querySelectorAll('[data-texto]').forEach((boton) => {
  boton.addEventListener('click', () => {
    document.body.classList.remove('texto-grande', 'texto-xgrande');
    const accion = boton.dataset.texto;
    if (accion === 'aumentar') {
      const actual = localStorage.getItem('tamañoTexto');
      const nuevo = actual === 'texto-grande' ? 'texto-xgrande' : 'texto-grande';
      document.body.classList.add(nuevo);
      localStorage.setItem('tamañoTexto', nuevo);
    } else {
      localStorage.removeItem('tamañoTexto');
    }
  });
});

// ---------- Mostrar/ocultar clave ----------

document.querySelectorAll('.btn-ver-clave').forEach((boton) => {
  boton.addEventListener('click', () => {
    const input = document.getElementById(boton.dataset.target);
    const mostrando = input.type === 'text';
    input.type = mostrando ? 'password' : 'text';
    boton.textContent = mostrando ? '👁' : '🙈';
    boton.setAttribute('aria-label', mostrando ? 'Mostrar clave' : 'Ocultar clave');
  });
});

// ---------- Formato automático de RUT ----------

const campoRut = document.getElementById('rut');
if (campoRut) {
  campoRut.addEventListener('input', () => {
    let valor = campoRut.value.replace(/[^\dkK]/g, '').toUpperCase();
    if (valor.length > 1) {
      const dv = valor.slice(-1);
      let cuerpo = valor.slice(0, -1);
      cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      campoRut.value = cuerpo + '-' + dv;
    } else {
      campoRut.value = valor;
    }
  });
}

// ---------- Fecha mínima = hoy (Espacios) ----------

const campoFecha = document.getElementById('fecha');
if (campoFecha) {
  campoFecha.min = new Date().toISOString().split('T')[0];
}

// ---------- Validación de formularios ----------

document.querySelectorAll('form[data-validar]').forEach((form) => {

  form.querySelectorAll('input, select').forEach((campo) => {
    campo.addEventListener('input', () => {
      campo.classList.remove('campo-invalido');
      const contenedor = campo.closest('.campo-clave') || campo;
      const siguiente = contenedor.nextElementSibling;
      if (siguiente && siguiente.classList.contains('field-error')) {
        siguiente.remove();
      }
    });
  });

  form.addEventListener('submit', (event) => {
    let valido = true;
    let primerInvalido = null;

    form.querySelectorAll('.field-error').forEach((error) => error.remove());
    form.querySelectorAll('.campo-invalido').forEach((campo) => campo.classList.remove('campo-invalido'));

    form.querySelectorAll('input[required], select[required]').forEach((campo) => {
      let mensaje = '';

      if (!campo.value.trim()) {
        mensaje = 'Este campo es obligatorio.';
      } else if (campo.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(campo.value)) {
        mensaje = 'Escribe un correo válido, por ejemplo: nombre@correo.cl';
      } else if (campo.id === 'rut' && !/^\d{1,2}\.?\d{3}\.?\d{3}-[\dkK]$/.test(campo.value.trim())) {
        mensaje = 'Escribe tu RUT con guión, por ejemplo: 12.345.678-9';
      } else if (campo.id === 'claveConfirmar' && campo.value !== form.querySelector('#clave').value) {
        mensaje = 'Las claves no coinciden.';
      } else if (campo.type === 'password' && campo.value.length < 8) {
        mensaje = 'La clave debe tener al menos 8 caracteres.';
      }

      if (mensaje) {
        valido = false;
        campo.classList.add('campo-invalido');
        const error = document.createElement('p');
        error.className = 'field-error';
        error.textContent = mensaje;
        const contenedor = campo.closest('.campo-clave') || campo;
        contenedor.insertAdjacentElement('afterend', error);
        if (!primerInvalido) primerInvalido = campo;
      }
    });

    if (!valido) {
      event.preventDefault();
      primerInvalido.focus();
      primerInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
});