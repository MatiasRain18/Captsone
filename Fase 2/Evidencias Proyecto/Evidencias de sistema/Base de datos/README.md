# Base de datos

- `unidad_territorial.sql`: estructura de las 9 tablas y datos de prueba (dos juntas con sus directivos y vecinos). Con XAMPP se importa desde phpMyAdmin; con Docker se importa sola al crear el contenedor.
- `Diagrama_Entidad_Relacion.png`: modelo entidad-relación con claves primarias, foráneas, únicas y la cardinalidad de cada relación.

Todas las tablas se relacionan con su junta (`id_junta`), lo que permite que varias juntas usen el mismo sistema sin ver los datos de las otras.
