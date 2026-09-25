# Sistema Unidad Territorial — Capstone DUOC UC

## Nombre del proyecto
Sistema Unidad Territorial

## Descripción
Servicio web responsivo, sin necesidad de una App, que digitaliza la gestión de una junta de vecinos: inscripción y aprobación de vecinos de la unidad territorial mayores de 14 años, solicitud y aprobación de certificados de residencia y de vecino vigente, publicación de noticias y avisos (generales, por grupo o dirigidos a un vecino específico), reserva de espacios comunitarios (canchas, salas, plazas), inscripción a actividades vecinales según cupo disponible, y postulación a proyectos vecinales. El sistema soporta multi-operación: una misma plataforma puede ser usada por distintas juntas de vecinos de forma simultánea e independiente, sin que una vea o interfiera con los datos de otra — cada registro queda etiquetado con la junta a la que pertenece.

## Tecnologías utilizadas (lenguajes, frameworks, base de datos, cloud)
- **Frontend:** HTML5, CSS3 (diseño propio, responsivo y accesible, con tipografía Atkinson Hyperlegible pensada para adultos mayores), JavaScript (validación de formularios, formato automático de RUT, ajuste de tamaño de letra)
- **Backend:** PHP (lógica de negocio, sesiones, validaciones y flujos de aprobación)
- **Base de datos:** MySQL / MariaDB
- **Entorno de desarrollo local:** XAMPP (Apache + PHP + MariaDB)
- **Control de versiones:** Git / GitHub

## Instrucciones para ejecutar el proyecto localmente
1. Instalar XAMPP.
2. Copiar la carpeta `JuntaVecinos` (dentro de `Fase 2`) a `C:\xampp\htdocs\`.
3. Iniciar los módulos Apache y MySQL desde el Panel de Control de XAMPP.
4. Entrar a `http://localhost/phpmyadmin/`, crear la base de datos `unidad_territorial`, e importar el archivo `.sql` incluido en este repositorio (estructura de tablas + datos de prueba).
5. Acceder al sitio desde el navegador en `http://localhost/JuntaVecinos/HTML/Index.html`.

## Integrantes del equipo con sus roles

| Integrante | Rol en el proyecto |
|---|---|
| Matías Rain Cheuquecoy | Coordinación general del proyecto, modelo de datos, módulo de inscripción de vecinos, módulo de certificados |
| Daniel Stiven Cruz | Módulo de proyectos vecinales, módulo de noticias/avisos, calendario de espacios comunitarios y actividades vecinales, pruebas funcionales |

**Docente guía:** Carlos Andrés Herrera — Curso Capstone (PTY4614), DUOC UC, Sede Alameda.

## Metodología de trabajo del equipo (Scrum, Kanban, DevOps, etc.)
**Kanban.** El equipo trabaja con un tablero de tareas compartido (columnas: Por hacer / Haciendo / Listo), lo que da visibilidad total del avance de cada integrante y permite reordenar tareas con flexibilidad según el feedback recibido del docente en cada revisión.

## Arquitectura de la solución (descripción o diagrama)
Arquitectura cliente-servidor tradicional (stack LAMP/WAMP): el navegador consume páginas PHP que renderizan HTML dinámico según el rol y la sesión activa del usuario. La autenticación se maneja con sesiones de PHP y dos roles principales: **vecino** (usuario público que puede inscribirse, pedir certificados, ver noticias, postular a proyectos, reservar espacios e inscribirse en actividades con cupo) y **directorio** (usuario interno que aprueba/rechaza solicitudes y publica noticias). El soporte multi-junta se logra mediante una columna `id_junta` presente en las tablas principales, de modo que cada consulta filtra automáticamente por la junta a la que pertenece quien inició sesión — así ningún directorio puede ver o modificar datos de una junta distinta a la suya.

**Estructura de carpetas del código** (`Fase 2/JuntaVecinos`):
- `HTML/` — Vistas del sistema: páginas públicas, panel del vecino, y panel del directorio.
- `CSS/` — Hoja de estilos compartida por todas las páginas.
- `JS/` — Validación de formularios, accesibilidad y comportamiento del menú móvil.
- `PHP/` — Conexión a la base de datos y procesamiento de formularios (registro, login, aprobaciones, publicaciones).
