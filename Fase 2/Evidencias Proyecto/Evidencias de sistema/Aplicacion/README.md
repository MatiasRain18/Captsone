# Aplicación

- `JuntaVecinos/`: código del sistema, organizado en `HTML`, `CSS`, `JS` y `PHP`.
- `Dockerfile`: imagen `php:8.2-apache` con la extensión `mysqli`.
- `docker-compose.yml`: levanta la web (puerto 8080), MariaDB 10.4 y phpMyAdmin (puerto 8081). Toma la base de datos desde `../Base de datos/unidad_territorial.sql`.
- `.env.example`: variables de entorno que usa el sistema.
- `DockerJunta.zip`: el mismo sistema empaquetado en una sola carpeta, listo para descargar y levantar con `docker compose up -d --build`.

Las instrucciones completas para XAMPP y Docker están en el README principal del repositorio.
