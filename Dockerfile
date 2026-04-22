FROM php:8.2-apache

# Habilitar extensiones necesarias para conectar con MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite de Apache (muy útil para URLs amigables en MVC)
RUN a2enmod rewrite