# Base de datos

- `unidad_territorial.sql`: estructura de las 9 tablas y datos de prueba (seis juntas con sus directivos y vecinos). Con XAMPP se importa desde phpMyAdmin; con Docker se importa sola al crear el contenedor.
- `Diagrama_Entidad_Relacion.png`: modelo entidad-relación con claves primarias, foráneas, únicas y la cardinalidad de cada relación.
- `Documento 08- Modelo de Datos.docx`: documento del modelo de datos con el diagrama explicado, el diccionario de datos de las 9 tablas, las 11 relaciones, las reglas de integridad, la separación entre juntas, la seguridad, los datos de prueba y la instalación.

Todas las tablas se relacionan con su junta (`id_junta`), lo que permite que varias juntas usen el mismo sistema sin ver los datos de las otras.
