# MySQL support tools inside PHP container for the two service (db and php-apache) to work correctly.

FROM php:8.0-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli