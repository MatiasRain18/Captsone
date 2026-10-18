# Sistema Unidad Territorial — Capstone DUOC UC

## Nombre del proyecto
Sistema Unidad Territorial — Gestión de Junta de Vecinos

## Descripción

**Qué problema resuelve:** en muchas juntas de vecinos los trámites se hacen en papel y en persona. Para pedir un certificado de residencia, inscribirse o enterarse de un aviso hay que ir a la sede en su horario, los registros quedan en cuadernos o planillas sueltas que se pierden o se duplican, y no hay forma de comprobar si un certificado es auténtico. Esto le quita tiempo al directorio y deja fuera a vecinos que trabajan o que no pueden trasladarse.

**A quién va dirigido:** a las juntas de vecinos y a sus dos tipos de usuarios. Por un lado, los vecinos de la unidad territorial, incluidos los adultos mayores; por eso el diseño usa letra grande, alto contraste y un botón para agrandar el texto. Por otro, el directorio de cada junta (presidente, secretario y tesorero), que administra las solicitudes.

**Qué hace:** es un servicio web responsivo que digitaliza la gestión de una junta de vecinos. Los vecinos de la unidad territorial (14 años o más) se inscriben en línea y el directorio de su junta aprueba o rechaza la solicitud. Una vez aprobados, pueden pedir certificados de residencia y de vecino vigente, que se descargan en PDF con firma del presidente, sello digital y un código de validación que cualquier persona puede comprobar en una página pública, sin iniciar sesión. Además pueden leer noticias y avisos (generales, para socios activos o dirigidos a un vecino específico), reservar espacios comunitarios (cancha, sala y plaza), inscribirse en actividades con cupo y, si son socios activos, postular a proyectos vecinales.

El directorio, formado por presidente, secretario y tesorero, revisa todas estas solicitudes desde su panel, y el sistema avisa al vecino por correo cada vez que se aprueba o rechaza una inscripción, un certificado o una postulación. Los proyectos se cierran solos cuando pasa su fecha límite.

La plataforma es multi-junta: varias juntas de vecinos la usan al mismo tiempo y cada una solo ve y modifica sus propios datos.

## Tecnologías utilizadas (lenguajes, frameworks, base de datos, cloud)
- **Frontend:** HTML5, CSS3 propio (responsivo y accesible, con tipografías Atkinson Hyperlegible para los textos y Bricolage Grotesque para los títulos) y JavaScript.
- **Interacción:** AJAX con `fetch`. Los formularios se envían sin recargar la página y el servidor responde en JSON. Incluye validación de formularios, formato automático de RUT, revisión en vivo de RUT disponible, botones para agrandar la letra y menú adaptado al celular.
- **Backend:** PHP 8.2 (sesiones, roles, validaciones, consultas preparadas y flujos de aprobación).
- **Base de datos:** MariaDB 10.4 / MySQL, base `unidad_territorial` con 9 tablas. El RUT se guarda sin puntos y con el dígito verificador en una columna aparte.
- **Librerías incluidas:** FPDF 1.9 (certificados en PDF) y PHPMailer (correos por SMTP de Gmail con TLS). Vienen dentro del proyecto, no hay que instalarlas.
- **Entorno local:** XAMPP (Apache + PHP + MariaDB).
- **Control de versiones:** Git / GitHub.
- **Cloud:** no se usa. El sistema funciona en un servidor local y la publicación en un hosting queda como mejora futura.

## Instrucciones para ejecutar el proyecto localmente
1. Instalar XAMPP y abrir su Panel de Control.
2. Copiar la carpeta `JuntaVecinos`, que está en `Fase 2/Evidencias Proyectos (version Agil)/Proyecto De Titulo/JuntaVecinos`, dentro de `C:\xampp\htdocs\`.
3. Iniciar los módulos **Apache** y **MySQL**.
4. Entrar a `http://localhost/phpmyadmin/`, crear la base de datos `unidad_territorial` (cotejamiento `utf8mb4_unicode_ci`) e importar el archivo `unidad_territorial.sql`, que está en la misma carpeta `Proyecto De Titulo`.
5. Abrir en el navegador `http://localhost/JuntaVecinos/HTML/Index.html`.

**Envío de correos (opcional):** en `PHP/Config-correo.php` se escribe una cuenta de Gmail y su contraseña de aplicación. Si no se configura, el sistema funciona igual y solo avisa que el correo no se pudo enviar.

**Ver el sistema desde un celular:** el celular y el computador deben estar en la misma red WiFi (o el computador conectado al hotspot del celular). En el computador se ejecuta `ipconfig`, se copia la "Dirección IPv4" del adaptador Wi-Fi y en el celular se abre `http://ESA-IP/JuntaVecinos/HTML/Index.html`. Si no carga, hay que permitir Apache en el firewall de Windows.

## Integrantes del equipo con sus roles

| Integrante | Rol en el proyecto |
|---|---|
| Matias Enrique Rain Cheuquecoy | Coordinación general, modelo de datos, módulo de inscripción de vecinos, módulo de certificados (solicitud, aprobación, PDF y verificación pública) y envío de correos |
| Daniel Stiven Cruz Bernal | Módulo de proyectos vecinales y postulaciones, noticias y avisos, espacios comunitarios y actividades, y pruebas funcionales |

**Docente:** Carlos Andrés Herrera, Capstone (PTY4614), DUOC UC Sede Alameda.

## Metodología de trabajo del equipo (Scrum, Kanban, DevOps, etc.)
**Ágil con tablero Kanban.** Organizamos el trabajo en un tablero con las columnas Por hacer / Haciendo / Listo, lo que nos permitió ver el avance de cada uno y reordenar tareas según el feedback del docente en cada revisión. Los artefactos ágiles del proyecto (Product Vision, Product Backlog, Definition of Done y Documento de Diseño) están en `Fase 2/Evidencias Proyectos (version Agil)`.

## Arquitectura de la solución (descripción o diagrama)
Arquitectura cliente-servidor sobre XAMPP. El navegador carga las vistas y envía las acciones por AJAX. Apache con PHP procesa cada petición, revisa la sesión, el rol y la junta del usuario, trabaja con MariaDB mediante consultas preparadas y responde en JSON, de modo que la página se actualiza sin recargarse.

Hay dos roles: **vecino**, que se inscribe, pide certificados, reserva espacios, se inscribe en actividades y postula a proyectos, y **directorio** (presidente, secretario y tesorero), que aprueba o rechaza solicitudes, publica noticias, administra la directiva y puede crear nuevas juntas con su directiva completa. El soporte multi-junta funciona con la columna `id_junta`: antes de cada acción del directorio, el sistema verifica que el registro pertenezca a su misma junta.

Los certificados se generan en PDF al momento de la descarga y solo si pertenecen al vecino que inició sesión y ya fueron aprobados. Cada uno trae un código único (XXXX-XXXX-XXXX) y un enlace a `Verificar.php`, donde cualquier persona puede comprobar si es auténtico.

Los diagramas completos (casos de uso, modelo E-R, clases, secuencia y componentes) están en el Documento de Diseño.

**Estructura de carpetas del código (`JuntaVecinos/`):**
- `HTML/`: páginas públicas (inicio, servicios, cómo funciona, acceso, registro y verificación), panel del vecino y panel del directorio.
- `CSS/`: hoja de estilos `Styles.css`.
- `JS/`: `Script.js`, con AJAX, validaciones, formato de RUT, accesibilidad y menú móvil.
- `PHP/`: conexión (`Config.php`), sesión (`Verificar-sesion.php`), funciones comunes (`Funciones.php`), procesadores de formularios (`Procesar-*.php`), consultas en vivo (`Api-*.php`), generación de certificados (`Generar-certificado.php`) y envío de correos (`Enviar-correo.php`).
- `PHP/fpdf/` y `PHP/PHPMailer/`: librerías incluidas.
