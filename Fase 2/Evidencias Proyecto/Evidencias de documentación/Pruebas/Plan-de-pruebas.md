# Plan de Pruebas y Resultados — Sistema Unidad Territorial

**Proyecto:** Sistema Unidad Territorial — Gestión de Junta de Vecinos
**Equipo:** Grupo 7 · Matias Rain Cheuquecoy y Daniel Stiven Cruz Bernal
**Curso:** Capstone PTY4614 · DUOC UC Sede Alameda

En esta carpeta juntamos todo lo relacionado con las pruebas del sistema: el plan que seguimos, los resultados de los casos de prueba manuales, los errores que encontramos y un script de pruebas automáticas que cualquiera puede ejecutar. El detalle completo, con las 37 capturas de evidencia, está en el **Documento 06- Control y Pruebas.docx**, y las evidencias de las pruebas finales (instalación desde cero con Docker y XAMPP, celular y pruebas automáticas) en el **Documento 07- Evidencias de Pruebas.docx**. Ambos están en `Fase 2/Evidencias Proyecto/Evidencias de documentación`, junto al video `Prueba Docker paso a paso.mp4`.

## Contenido de la carpeta

| Archivo | Qué es |
|---|---|
| `Plan-de-pruebas.md` | Este documento: plan, casos de prueba, errores y resultados |
| `pruebas_automaticas.php` | Script que recorre el sistema solo y revisa 45 comportamientos |

## 1. Objetivo

Queríamos comprobar dos cosas. Primero, que cada módulo hiciera lo que prometimos en el Product Backlog. Segundo, que el sistema bloqueara lo que no corresponde, porque un sistema que maneja datos de vecinos no solo tiene que funcionar, también tiene que impedir que alguien vea o toque lo que no es suyo.

## 2. Alcance

Probamos los cinco módulos (inscripción, certificados, proyectos, noticias y espacios con actividades), la directiva y la creación de juntas, la seguridad y los permisos, la separación entre juntas, el uso desde el celular y la instalación con Docker.

## 3. Estrategia

Usamos tres tipos de prueba que se complementan:

1. **Pruebas funcionales manuales (CP-01 a CP-32):** cada uno probó los módulos que programó el otro, para no revisar el propio trabajo con los mismos ojos. Matias desarrolló inscripción y certificados, y Daniel proyectos, noticias y espacios.
2. **Pruebas de "hacer trampa":** nos pusimos en el lugar de alguien que quiere saltarse las reglas, como un vecino entrando al panel del directorio o alguien cambiando el número en la dirección para bajar el PDF de otra persona.
3. **Pruebas automáticas (PA-01 a PA-45):** un script en PHP que inicia sesión con las cuentas de prueba, abre páginas, descarga certificados y revisa cada respuesta. Sirve para repetir las pruebas en segundos cada vez que cambiamos algo.

## 4. Entorno de pruebas

| Elemento | Detalle |
|---|---|
| Servidor local | XAMPP: Apache, PHP 8.2.12 y MariaDB 10.4.32 |
| Contenedores | Docker Desktop con docker-compose: web (php:8.2-apache), MariaDB 10.4 y phpMyAdmin 5.2 |
| Navegadores | Chrome y Edge en Windows; Chrome y Samsung Internet en Android |
| Redes | WiFi de la casa y hotspot de un celular |
| Correo | Gmail por SMTP con contraseña de aplicación, recibido en el celular |
| Datos | `unidad_territorial.sql` con juntas, vecinos y directivos de prueba |

## 5. Casos de prueba manuales

| ID | Qué probamos | Resultado esperado | Estado |
|---|---|---|---|
| CP-01 | Registrarse con un RUT cuyo dígito verificador no corresponde | Rechaza el registro y avisa que el RUT no es válido | Aprobada |
| CP-02 | Registrarse con datos correctos y confirmando tener 14 años o más | La inscripción queda pendiente de revisión | Aprobada |
| CP-03 | Iniciar sesión con una inscripción todavía pendiente | No deja entrar y explica que falta la aprobación | Aprobada |
| CP-04 | El directorio aprueba la inscripción | El vecino queda aprobado y le llega un correo | Aprobada |
| CP-05 | Iniciar sesión con una cuenta dada de baja | No deja entrar y lo explica con el diseño del sitio | Aprobada |
| CP-06 | Iniciar sesión con una clave incorrecta | Mensaje genérico, sin decir si falló el RUT o la clave | Aprobada |
| CP-07 | Solicitar un certificado de residencia o de vecino vigente | La solicitud queda "En revisión" | Aprobada |
| CP-08 | Pedir otro certificado del mismo tipo con uno pendiente | No deja duplicar la solicitud | Aprobada |
| CP-09 | El directorio aprueba el certificado | Se genera el código de validación y llega un correo | Aprobada |
| CP-10 | Descargar el PDF del certificado aprobado | PDF con datos, firma, sello y enlace de verificación | Aprobada |
| CP-11 | Verificar un certificado con su código real | "Certificado auténtico" con el RUT parcialmente oculto | Aprobada |
| CP-12 | Verificar con un código inventado | Avisa que no existe un certificado aprobado con ese código | Aprobada |
| CP-13 | Bajar el PDF de otra persona cambiando el número en la dirección | El sistema no entrega el PDF | Aprobada |
| CP-14 | Un socio activo postula a un proyecto y el directorio responde | Se aprueba o rechaza con aviso por correo | Aprobada |
| CP-15 | Un proyecto pasa su fecha límite | Se cierra solo y no acepta postulaciones | Aprobada |
| CP-16 | Enviar una noticia a un RUT de otra junta | El sistema no lo permite | Aprobada |
| CP-17 | Revisar una reserva que choca con otra aprobada | El directorio ve un aviso de choque | Aprobada |
| CP-18 | Inscribirse en una actividad con el cupo lleno | No deja inscribirse y marca "Cupos llenos" | Aprobada |
| CP-19 | Entrar a una junta a la que le faltan cargos | El panel avisa que la directiva está incompleta | Aprobada |
| CP-20 | Crear una junta nueva con su directiva | La junta parte sin datos de las demás | Aprobada |
| CP-21 | Dar de baja a un vecino de otra junta | El sistema lo bloquea | Aprobada |
| CP-22 | Un vecino abre una página del panel del directorio | Lo devuelve a su propio panel | Aprobada |
| CP-23 | Abrir páginas internas sin sesión | Pide iniciar sesión | Aprobada |
| CP-24 | Abrir el sistema en la pantalla del celular | Menú en botón y sin desplazamiento lateral | Aprobada |
| CP-25 | Usar los botones A y A+ | El texto crece y se mantiene al cambiar de página | Aprobada |
| CP-26 | Entrar desde un celular con la IP del computador | Funciona igual que en el computador | Aprobada |
| CP-27 | `docker compose up -d --build` en una carpeta recién descomprimida | Se levantan los tres contenedores | Aprobada |
| CP-28 | Revisar con `docker compose ps` y Docker Desktop | Los tres aparecen corriendo | Aprobada |
| CP-29 | Entrar a phpMyAdmin del contenedor | La base con sus 9 tablas, importada sola | Aprobada |
| CP-30 | Flujo completo de un certificado dentro de Docker | Todo funciona igual que en XAMPP | Aprobada |
| CP-31 | Entrar a Docker desde celulares con el puerto 8080 | El sistema carga y se puede usar | Aprobada |
| CP-32 | Apagar con `docker compose stop` y volver a encender | Los datos se mantienen | Aprobada |

**Resultado: 32 de 32 casos aprobados.** Varios fallaron la primera vez y los volvimos a ejecutar después de corregirlos; esos errores están en la sección 7.

## 6. Pruebas automáticas

El script `pruebas_automaticas.php` revisa 45 comportamientos agrupados en nueve bloques: páginas públicas, inicio de sesión, permisos, páginas con sesión, separación entre juntas, certificados en PDF, verificación pública, validación del RUT y cierre de sesión. Solo lee datos, así que se puede ejecutar las veces que se quiera sin ensuciar la base.

**Cómo ejecutarlo** (con el sistema ya levantado y la base importada):

```
# Con XAMPP
C:\xampp\php\php.exe pruebas_automaticas.php

# Con Docker (el sistema está en el puerto 8080)
C:\xampp\php\php.exe pruebas_automaticas.php http://localhost:8080/JuntaVecinos
```

Cada prueba aparece como `[OK]` o `[FALLA]` y al final sale el resumen, por ejemplo `RESULTADO: 45 de 45 pruebas aprobadas`. Si se quiere guardar el resultado en un archivo, se agrega `> resultado_pruebas.txt` al final del comando.

Algunas de las pruebas automáticas que más nos importaban:

| ID | Qué revisa |
|---|---|
| PA-07 | El login muestra el mismo mensaje si falla el RUT o la clave, sin dar pistas |
| PA-14 | Un vecino que intenta abrir el panel del directorio es devuelto a su panel |
| PA-30 / PA-31 | Cada junta ve solo a sus propios vecinos |
| PA-33 | El directorio no puede encontrar (ni enviarle avisos) a un vecino de otra junta |
| PA-35 / PA-36 | Nadie puede bajar el certificado de otra persona cambiando el número |
| PA-39 | La verificación pública muestra el RUT parcialmente oculto |
| PA-41 | Un intento de inyección SQL en el código de verificación es rechazado |

## 7. Errores encontrados y cómo los resolvimos

| ID | Error | Solución | Estado |
|---|---|---|---|
| ER-01 | Mensajes del servidor en una página en blanco | Función `responder()` que devuelve JSON y se muestra con el diseño del sitio | Resuelto |
| ER-02 | MySQL se detenía solo en XAMPP | Reinstalamos XAMPP, restauramos el respaldo .sql y repetimos las pruebas | Resuelto |
| ER-03 | Apache respondía "Not Found" | Dejamos el proyecto en `htdocs/JuntaVecinos` | Resuelto |
| ER-04 | Google inhabilitó la cuenta de Gmail del sistema | Cuenta con verificación en dos pasos y contraseña de aplicación | Resuelto |
| ER-05 | El correo fallaba por el antivirus Avast | Ajustamos la conexión SSL de PHPMailer | Resuelto |
| ER-06 | Contadores vacíos visibles aunque estuvieran en cero | Regla `[hidden]{display:none!important}` en el CSS | Resuelto |
| ER-07 | Dos elementos con el mismo `id="contenido"` | Cambiamos el del contenedor a "principal" | Resuelto |
| ER-08 | El celular no podía abrir el sistema | Abrimos el puerto 80 en el firewall y desinstalamos Avast | Resuelto |
| ER-09 | Con el hotspot el celular no encontraba el servidor | Revisamos la nueva IP con `ipconfig` | Resuelto |
| ER-10 | En Docker, localhost rechazaba la conexión | Se entra por `localhost:8080`, explicado en el README | Resuelto |

## 8. Conclusión

Los 32 casos de prueba manuales quedaron aprobados, los 10 errores encontrados están resueltos y el script automático nos permite repetir 45 verificaciones en segundos, tanto en XAMPP como en Docker. Lo que más aprendimos es que probar no es solo ver que algo funcione, sino tratar de romperlo a propósito.
