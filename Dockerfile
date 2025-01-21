FROM php:7.4-apache

# Installer les extensions nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application dans le conteneur
COPY . /var/www/html/

# Installer les dépendances avec Composer
RUN composer install 

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80
EXPOSE 80