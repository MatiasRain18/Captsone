# Imagen del Sistema Unidad Territorial: Apache + PHP 8.2 (la misma versión de PHP que usamos en XAMPP)
FROM php:8.2-apache

# Hora de Chile para PHP y para el sistema
ENV TZ=America/Santiago
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Extensión mysqli para conectarse a MariaDB (mbstring, iconv, openssl y zlib ya vienen en la imagen)
RUN docker-php-ext-install mysqli

# Configuración de PHP para producción, sin mostrar errores en pantalla
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "date.timezone = America/Santiago" > "$PHP_INI_DIR/conf.d/zona-horaria.ini"

# El proyecto queda en la misma ruta que en XAMPP: /JuntaVecinos/HTML/Index.html
COPY JuntaVecinos/ /var/www/html/JuntaVecinos/

# Entrar a la raíz del servidor lleva directo a la página de inicio
RUN printf '<?php\nheader("Location: /JuntaVecinos/HTML/Index.html");\n' > /var/www/html/index.php \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80
