<?php // Parte inferior de las páginas con sesión: cierra el contenido y carga el JavaScript ?>
  </main>
</div>

<!-- Ventana de confirmación (reemplaza el "localhost dice" del navegador) -->
<div class="modal" id="modalConfirmar" hidden>
  <div class="modal-fondo" data-cerrar-modal></div>
  <div class="modal-caja" role="dialog" aria-modal="true" aria-labelledby="modalTitulo">
    <h2 id="modalTitulo">¿Confirmas esta acción?</h2>
    <p id="modalTexto"></p>
    <div class="modal-botones">
      <button type="button" class="btn btn-secundario" data-cerrar-modal>Cancelar</button>
      <button type="button" class="btn btn-primary" id="modalAceptar">Confirmar</button>
    </div>
  </div>
</div>

<!-- Avisos (toasts) que muestran el resultado de cada acción AJAX -->
<div class="avisos" id="avisos" aria-live="polite"></div>

<script src="../JS/Script.js"></script>
</body>
</html>
