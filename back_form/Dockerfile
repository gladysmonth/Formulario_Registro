FROM php:7.3-apache

# Actualizar repositorios e instalar dependencias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Habilitar módulos de Apache requeridos (reescritura de URL y cabeceras CORS)
RUN a2enmod rewrite headers

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Exponer el puerto estándar HTTP
EXPOSE 80
