# syntax=docker/dockerfile:1

FROM node:16.17.0-bullseye-slim

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY .env .env
COPY . .

# Instalar dependencias de sistema + PHP
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        software-properties-common \
        gnupg2 wget unzip curl git && \
    echo "deb https://packages.sury.org/php/ bullseye main" > /etc/apt/sources.list.d/sury-php.list && \
    wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add - && \
    apt-get update && \
    apt-get install -y --no-install-recommends \
        php8.1 php8.1-cli php8.1-curl php8.1-xml php8.1-zip php8.1-gd php8.1-mbstring php8.1-mysql &&  apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependencias PHP y JS + compilar frontend
RUN composer install --no-dev --optimize-autoloader && \
    npm install && \
    npm run build && \
    php artisan key:generate

# Comando al iniciar
CMD ["bash", "./run.sh"]
