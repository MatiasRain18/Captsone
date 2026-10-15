// =====================================================================
// Sistema Unidad Territorial - JavaScript
// Aquí está todo lo que pasa en el navegador:
//   - Menú, tamaño de letra y mostrar clave
//   - Formato y validación del RUT (con dígito verificador)
//   - AJAX: los formularios se envían con fetch() sin recargar la página,
//     la respuesta llega en JSON y se muestra como aviso con el diseño del sitio,
//     y las listas se actualizan solas.
//   - AJAX de consulta: RUT disponible, vecino de mi junta, contadores del panel
//     y verificación de certificados.
// =====================================================================

// ---------- Menú (celular) ----------

const menuToggle = document.getElementById('menuToggle');
const menuDestino = document.getElementById('mainNav') || document.getElementById('menuApp');

if (menuToggle && menuDestino) {
  menuToggle.addEventListener('click', () => {
    const abierto = menuDestino.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', abierto);
  });
}

// ---------- Tamaño de letra (se guarda entre páginas) ----------

function leerPreferencia(clave) {
  try { return localStorage.getItem(clave); } catch (e) { return null; }
}
function guardarPreferencia(clave, valor) {
  try {
    if (valor) localStorage.setItem(clave, valor); else localStorage.removeItem(clave);
  } catch (e) { /* el navegador no permite guardar: no pasa nada */ }
}

const tamañoGuardado = leerPreferencia('tamañoTexto');
if (tamañoGuardado) {
  document.body.classList.add(tamañoGuardado);
}

document.querySelectorAll('[data-texto]').forEach((boton) => {
  boton.addEventListener('click', () => {
    document.body.classList.remove('texto-grande', 'texto-xgrande');
    if (boton.dataset.texto === 'aumentar') {
      const actual = leerPreferencia('tamañoTexto');
      const nuevo = actual === 'texto-grande' ? 'texto-xgrande' : 'texto-grande';
      document.body.classList.add(nuevo);
      guardarPreferencia('tamañoTexto', nuevo);
    } else {
      guardarPreferencia('tamañoTexto', null);
    }
  });
});

// ---------- Mostrar/ocultar clave ----------

document.addEventListener('click', (evento) => {
  const boton = evento.target.closest('.btn-ver-clave');
  if (!boton) return;
  const input = document.getElementById(boton.dataset.target);
  const mostrando = input.type === 'text';
  input.type = mostrando ? 'password' : 'text';
  boton.classList.toggle('mostrando', !mostrando);
  boton.setAttribute('aria-pressed', mostrando ? 'false' : 'true');
  boton.setAttribute('aria-label', mostrando ? 'Mostrar clave' : 'Ocultar clave');
});

// =====================================================================
// RUT
// En la base de datos se guarda sin puntos y con el dígito verificador aparte.
// En pantalla se muestra con puntos y guión para que sea fácil de leer.
// =====================================================================

// Calcula el dígito verificador (módulo 11)
function calcularDv(cuerpo) {
  let suma = 0;
  let multiplicador = 2;
  for (let i = cuerpo.length - 1; i >= 0; i--) {
    suma += parseInt(cuerpo[i], 10) * multiplicador;
    multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
  }
  const resto = 11 - (suma % 11);
  if (resto === 11) return '0';
  if (resto === 10) return 'K';
  return String(resto);
}

function limpiarRut(texto) {
  return texto.replace(/[^\dkK]/g, '').toUpperCase();
}

function rutValido(texto) {
  const limpio = limpiarRut(texto);
  if (limpio.length < 7 || limpio.length > 9) return false;
  const cuerpo = limpio.slice(0, -1);
  const dv = limpio.slice(-1);
  return /^\d+$/.test(cuerpo) && calcularDv(cuerpo) === dv;
}

// Mientras se escribe: 12345678K -> 12.345.678-K
document.querySelectorAll('[data-rut]').forEach((campo) => {
  campo.addEventListener('input', () => {
    const valor = limpiarRut(campo.value).slice(0, 9);
    if (valor.length > 1) {
      const cuerpo = valor.slice(0, -1).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      campo.value = cuerpo + '-' + valor.slice(-1);
    } else {
      campo.value = valor;
    }
  });
});

// ---------- Fechas: no se pueden elegir días pasados ----------

function hoyLocal() {
  const d = new Date();
  d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
  return d.toISOString().split('T')[0];
}
document.querySelectorAll('input[type="date"][data-desde-hoy]').forEach((campo) => {
  campo.min = hoyLocal();
});

// =====================================================================
// AVISOS (toasts) y VENTANA DE CONFIRMACIÓN
// =====================================================================

let contenedorAvisos = document.getElementById('avisos');
if (!contenedorAvisos) {
  contenedorAvisos = document.createElement('div');
  contenedorAvisos.className = 'avisos';
  contenedorAvisos.id = 'avisos';
  contenedorAvisos.setAttribute('aria-live', 'polite');
  document.body.appendChild(contenedorAvisos);
}

const ICONO_OK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="8 12.5 11 15.5 16 9.5"/></svg>';
const ICONO_ERROR = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="7" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';

function mostrarAviso(mensaje, ok = true) {
  const aviso = document.createElement('div');
  aviso.className = 'aviso-flotante ' + (ok ? 'ok' : 'error');
  aviso.setAttribute('role', ok ? 'status' : 'alert');
  aviso.innerHTML = (ok ? ICONO_OK : ICONO_ERROR) + '<span></span><button type="button" aria-label="Cerrar aviso">×</button>';
  aviso.querySelector('span').textContent = mensaje;

  const cerrar = () => {
    aviso.classList.add('saliendo');
    setTimeout(() => aviso.remove(), 200);
  };
  aviso.querySelector('button').addEventListener('click', cerrar);
  contenedorAvisos.appendChild(aviso);
  setTimeout(cerrar, ok ? 4500 : 7000);
}

// Ventana para confirmar (reemplaza a confirm() del navegador)
function confirmar(texto, peligro) {
  const modal = document.getElementById('modalConfirmar');
  if (!modal) {
    return Promise.resolve(window.confirm(texto));
  }
  return new Promise((resolver) => {
    const caja = modal.querySelector('.modal-caja');
    const aceptar = document.getElementById('modalAceptar');
    document.getElementById('modalTexto').textContent = texto;
    caja.classList.toggle('peligro', !!peligro);
    aceptar.textContent = peligro || 'Confirmar';
    modal.hidden = false;
    aceptar.focus();

    const terminar = (respuesta) => {
      modal.hidden = true;
      aceptar.removeEventListener('click', alAceptar);
      modal.querySelectorAll('[data-cerrar-modal]').forEach((el) => el.removeEventListener('click', alCancelar));
      document.removeEventListener('keydown', alTeclear);
      resolver(respuesta);
    };
    const alAceptar = () => terminar(true);
    const alCancelar = () => terminar(false);
    const alTeclear = (e) => { if (e.key === 'Escape') terminar(false); };

    aceptar.addEventListener('click', alAceptar);
    modal.querySelectorAll('[data-cerrar-modal]').forEach((el) => el.addEventListener('click', alCancelar));
    document.addEventListener('keydown', alTeclear);
  });
}

// =====================================================================
// VALIDACIÓN DE FORMULARIOS (antes de enviar)
// =====================================================================

function limpiarErrores(form) {
  form.querySelectorAll('.field-error').forEach((error) => error.remove());
  form.querySelectorAll('.campo-invalido').forEach((campo) => campo.classList.remove('campo-invalido'));
}

function marcarError(campo, mensaje) {
  campo.classList.add('campo-invalido');
  campo.setAttribute('aria-invalid', 'true');
  const error = document.createElement('p');
  error.className = 'field-error';
  error.textContent = mensaje;
  const contenedor = campo.closest('.campo-clave') || campo.closest('.casilla') || campo;
  contenedor.insertAdjacentElement('afterend', error);
}

function validarFormulario(form) {
  limpiarErrores(form);
  let primerInvalido = null;

  form.querySelectorAll('input, select, textarea').forEach((campo) => {
    if (campo.type === 'hidden' || campo.disabled) return;
    const valor = campo.value.trim();
    let mensaje = '';

    if (campo.required && campo.type === 'checkbox' && !campo.checked) {
      mensaje = campo.dataset.mensaje || 'Debes marcar esta casilla para continuar.';
    } else if (campo.required && !valor) {
      mensaje = 'Este campo es obligatorio.';
    } else if (valor && campo.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)) {
      mensaje = 'Escribe un correo válido, por ejemplo: nombre@correo.cl';
    } else if (valor && campo.hasAttribute('data-rut') && !campo.hasAttribute('data-sin-dv') && !rutValido(valor)) {
      mensaje = 'El RUT no es válido. Revisa el número y el dígito verificador (ej: 12.345.678-5).';
    } else if (valor && campo.dataset.igualA && valor !== form.querySelector('#' + campo.dataset.igualA).value) {
      mensaje = 'Las claves no coinciden.';
    } else if (valor && campo.closest('.campo-clave') && campo.minLength > 0 && valor.length < campo.minLength) {
      mensaje = 'La clave debe tener al menos ' + campo.minLength + ' caracteres.';
    } else if (valor && campo.type === 'date' && campo.min && valor < campo.min) {
      mensaje = 'Elige una fecha de hoy en adelante.';
    } else if (valor && campo.type === 'number' && campo.min && Number(valor) < Number(campo.min)) {
      mensaje = 'El número debe ser ' + campo.min + ' o más.';
    }

    if (mensaje) {
      marcarError(campo, mensaje);
      if (!primerInvalido) primerInvalido = campo;
    }
  });

  if (primerInvalido) {
    primerInvalido.focus();
    primerInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return false;
  }
  return true;
}

// Al corregir un campo se borra su mensaje de error
document.addEventListener('input', (evento) => {
  const campo = evento.target;
  if (!campo.classList || !campo.classList.contains('campo-invalido')) return;
  campo.classList.remove('campo-invalido');
  campo.removeAttribute('aria-invalid');
  const contenedor = campo.closest('.campo-clave') || campo.closest('.casilla') || campo;
  const siguiente = contenedor.nextElementSibling;
  if (siguiente && siguiente.classList.contains('field-error')) siguiente.remove();
});

// =====================================================================
// AJAX: ENVÍO DE FORMULARIOS
// Todo formulario con el atributo data-ajax se envía con fetch().
// El PHP responde JSON: { ok, mensaje, redirigir?, recargar?, limpiar? }
// =====================================================================

async function pedirJson(url, opciones = {}) {
  const respuesta = await fetch(url, {
    ...opciones,
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', ...(opciones.headers || {}) },
    credentials: 'same-origin',
  });
  const tipo = respuesta.headers.get('Content-Type') || '';
  if (!tipo.includes('application/json')) {
    throw new Error('Respuesta inesperada del servidor');
  }
  return respuesta.json();
}

document.addEventListener('submit', async (evento) => {
  const form = evento.target;

  // Formularios normales (sin AJAX): solo se validan
  if (!form.hasAttribute('data-ajax')) {
    if (form.hasAttribute('data-validar') && !validarFormulario(form)) {
      evento.preventDefault();
    }
    return;
  }

  evento.preventDefault();
  if (!validarFormulario(form)) return;

  const boton = evento.submitter || form.querySelector('[type="submit"]');

  // Algunas acciones piden confirmación (rechazar, dar de baja, eliminar…)
  const textoConfirmar = (boton && boton.dataset.confirmar) || form.dataset.confirmar;
  if (textoConfirmar) {
    const peligro = (boton && boton.dataset.peligro) || form.dataset.peligro;
    const seguro = await confirmar(textoConfirmar, peligro);
    if (!seguro) return;
  }

  const datos = new FormData(form);
  if (boton && boton.name) {
    datos.append(boton.name, boton.value);
  }

  // Mientras espera: botones desactivados y texto "Procesando…"
  const botones = form.querySelectorAll('button[type="submit"]');
  const textoOriginal = boton ? boton.innerHTML : '';
  botones.forEach((b) => { b.disabled = true; });
  if (boton) {
    boton.classList.add('cargando');
    boton.textContent = 'Procesando…';
  }

  let respuesta = null;
  try {
    respuesta = await pedirJson(form.action, { method: 'POST', body: datos });
  } catch (error) {
    respuesta = { ok: false, mensaje: 'No hubo conexión con el servidor. Revisa que XAMPP esté encendido e inténtalo de nuevo.' };
  }

  botones.forEach((b) => { b.disabled = false; });
  if (boton) {
    boton.classList.remove('cargando');
    boton.innerHTML = textoOriginal;
  }

  // Mensaje dentro de la página para formularios que lo piden (login, registro)
  const zonaMensaje = form.querySelector('[data-mensaje-form]');
  if (zonaMensaje) {
    zonaMensaje.hidden = false;
    zonaMensaje.className = 'aviso ' + (respuesta.ok ? 'aviso-ok' : 'aviso-espera');
    zonaMensaje.querySelector('p').textContent = respuesta.mensaje;
  } else {
    mostrarAviso(respuesta.mensaje, respuesta.ok);
  }

  if (respuesta.ok && form.dataset.alTerminar === 'reemplazar') {
    // Registro: se reemplaza el formulario por el mensaje de éxito
    const exito = document.getElementById(form.dataset.exito);
    if (exito) {
      exito.querySelector('[data-texto-exito]').textContent = respuesta.mensaje;
      form.hidden = true;
      exito.hidden = false;
      exito.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  if (respuesta.ok && respuesta.limpiar) {
    form.reset();
    form.querySelectorAll('.ayuda-vivo').forEach((ayuda) => { ayuda.textContent = ''; ayuda.className = 'ayuda-vivo'; });
    form.dispatchEvent(new Event('change'));
  }

  if (respuesta.redirigir) {
    setTimeout(() => { window.location.href = respuesta.redirigir; }, respuesta.ok ? 700 : 1800);
    return;
  }

  if (respuesta.recargar) {
    await refrescarZonas();
  }
});

// =====================================================================
// AJAX: ACTUALIZAR LAS LISTAS SIN RECARGAR LA PÁGINA
// Las partes de la página con data-zona se vuelven a pedir al servidor
// (GET con fetch) y se reemplazan solo esas partes.
// =====================================================================

async function refrescarZonas() {
  const zonas = document.querySelectorAll('[data-zona]');
  if (zonas.length) {
    try {
      const respuesta = await fetch(window.location.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      const tipo = respuesta.headers.get('Content-Type') || '';
      if (tipo.includes('application/json')) {
        // La sesión expiró
        const json = await respuesta.json();
        mostrarAviso(json.mensaje, false);
        if (json.redirigir) setTimeout(() => { window.location.href = json.redirigir; }, 1500);
        return;
      }
      const html = await respuesta.text();
      const nuevo = new DOMParser().parseFromString(html, 'text/html');
      zonas.forEach((zona) => {
        const reemplazo = nuevo.querySelector('[data-zona="' + zona.dataset.zona + '"]');
        if (reemplazo) zona.innerHTML = reemplazo.innerHTML;
      });
    } catch (error) {
      window.location.reload();
      return;
    }
  }
  cargarContadores();
}

// =====================================================================
// AJAX: CONSULTAS EN VIVO
// =====================================================================

// Contadores del menú y del panel (pendientes, listos, etc.)
async function cargarContadores() {
  const elementos = document.querySelectorAll('[data-contador], [data-dato]');
  if (!elementos.length) return;
  try {
    const respuesta = await pedirJson('../PHP/Api-resumen.php');
    if (!respuesta.ok) return;
    const datos = respuesta.datos;

    document.querySelectorAll('[data-contador]').forEach((el) => {
      const valor = datos[el.dataset.contador] || 0;
      el.textContent = valor;
      el.hidden = valor === 0;
      el.setAttribute('aria-label', valor + ' pendientes');
    });

    // Textos del panel: data-dato="certificados" data-uno="1 listo" data-varios="{n} listos" data-cero="..."
    document.querySelectorAll('[data-dato]').forEach((el) => {
      const n = datos[el.dataset.dato] || 0;
      let texto = n === 0 ? el.dataset.cero : (n === 1 ? el.dataset.uno : el.dataset.varios);
      el.textContent = (texto || '').replace('{n}', n);
      el.classList.toggle('pendiente', n > 0 && el.hasAttribute('data-alerta'));
    });
  } catch (error) {
    // Si falla, la página funciona igual sin los contadores
  }
}
cargarContadores();

// Espera a que la persona deje de escribir antes de consultar
function conPausa(funcion, ms = 400) {
  let temporizador;
  return (...args) => {
    clearTimeout(temporizador);
    temporizador = setTimeout(() => funcion(...args), ms);
  };
}

function mostrarAyuda(elemento, texto, estado) {
  elemento.textContent = texto;
  elemento.className = 'ayuda-vivo' + (estado ? ' ' + estado : '');
}

// Registro: ¿el RUT es válido y está disponible?
document.querySelectorAll('[data-revisar-rut]').forEach((campo) => {
  const ayuda = document.getElementById(campo.dataset.revisarRut);
  const revisar = conPausa(async () => {
    const valor = campo.value.trim();
    if (limpiarRut(valor).length < 8) { mostrarAyuda(ayuda, '', ''); return; }
    if (!rutValido(valor)) { mostrarAyuda(ayuda, 'El dígito verificador no corresponde a este RUT.', 'error'); return; }
    mostrarAyuda(ayuda, 'Revisando…', 'buscando');
    try {
      const r = await pedirJson('../PHP/Api-rut-disponible.php?rut=' + encodeURIComponent(valor));
      mostrarAyuda(ayuda, r.mensaje, r.disponible ? 'ok' : 'error');
    } catch (e) {
      mostrarAyuda(ayuda, '', '');
    }
  });
  campo.addEventListener('input', revisar);
});

// Noticias: ¿el RUT es de un vecino de MI junta?
document.querySelectorAll('[data-buscar-vecino]').forEach((campo) => {
  const ayuda = document.getElementById(campo.dataset.buscarVecino);
  const buscar = conPausa(async () => {
    const valor = campo.value.trim();
    if (limpiarRut(valor).length < 8) { mostrarAyuda(ayuda, '', ''); return; }
    mostrarAyuda(ayuda, 'Buscando en tu junta…', 'buscando');
    try {
      const r = await pedirJson('../PHP/Api-buscar-vecino.php?rut=' + encodeURIComponent(valor));
      mostrarAyuda(ayuda, r.mensaje, r.encontrado ? 'ok' : 'error');
    } catch (e) {
      mostrarAyuda(ayuda, '', '');
    }
  });
  campo.addEventListener('input', buscar);
});

// Noticias: el campo RUT solo aparece si se elige "Un vecino específico"
const selectTipo = document.querySelector('[data-mostrar-si]');
if (selectTipo) {
  const bloque = document.getElementById(selectTipo.dataset.mostrarSi);
  const input = bloque.querySelector('input');
  const actualizar = () => {
    const visible = selectTipo.value === 'especifico';
    bloque.hidden = !visible;
    input.required = visible;
    input.disabled = !visible;
  };
  selectTipo.addEventListener('change', actualizar);
  selectTipo.closest('form').addEventListener('change', actualizar);
  actualizar();
}

// Verificar certificado (página pública)
const formVerificar = document.getElementById('formVerificar');
if (formVerificar) {
  const campoCodigo = document.getElementById('codigo');
  const resultado = document.getElementById('resultadoVerificacion');

  campoCodigo.addEventListener('input', () => {
    const limpio = campoCodigo.value.replace(/[^0-9a-zA-Z]/g, '').toUpperCase().slice(0, 12);
    campoCodigo.value = limpio.match(/.{1,4}/g) ? limpio.match(/.{1,4}/g).join('-') : '';
  });

  const verificar = async () => {
    const boton = formVerificar.querySelector('[type="submit"]');
    boton.disabled = true;
    boton.classList.add('cargando');
    try {
      const r = await pedirJson('../PHP/Api-verificar-certificado.php?codigo=' + encodeURIComponent(campoCodigo.value));
      resultado.hidden = false;
      resultado.className = 'resultado-verificacion ' + (r.valido ? 'valido' : 'invalido');
      if (r.valido) {
        const c = r.certificado;
        resultado.innerHTML = '<h2>' + ICONO_OK + 'Certificado auténtico</h2><dl></dl>';
        const lista = resultado.querySelector('dl');
        [['Documento', c.tipo], ['Folio', 'N° ' + c.folio], ['A nombre de', c.nombre], ['RUT', c.rut],
         ['Emitido por', c.junta + ', comuna de ' + c.comuna], ['Fecha de emisión', c.fecha], ['Código', c.codigo]]
          .forEach(([etiqueta, valor]) => {
            const dt = document.createElement('dt'); dt.textContent = etiqueta;
            const dd = document.createElement('dd'); dd.textContent = valor;
            lista.append(dt, dd);
          });
      } else {
        resultado.innerHTML = '<h2>' + ICONO_ERROR + 'No se pudo verificar</h2><p></p>';
        resultado.querySelector('p').textContent = r.mensaje;
      }
    } catch (e) {
      mostrarAviso('No hubo conexión con el servidor. Inténtalo de nuevo.', false);
    }
    boton.disabled = false;
    boton.classList.remove('cargando');
  };

  formVerificar.addEventListener('submit', (evento) => {
    evento.preventDefault();
    if (validarFormulario(formVerificar)) verificar();
  });

  // Si se abrió desde el enlace del PDF, el código ya viene en la dirección
  const codigoUrl = new URLSearchParams(window.location.search).get('codigo');
  if (codigoUrl) {
    campoCodigo.value = codigoUrl;
    campoCodigo.dispatchEvent(new Event('input'));
    verificar();
  }
}
