FROM node:20.17.0-bullseye-slim

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias del sistema y PHP
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        software-properties-common \
        gnupg2 wget unzip curl git ca-certificates && \
    echo "deb https://packages.sury.org/php/ bullseye main" > /etc/apt/sources.list.d/sury-php.list && \
    wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add - && \
    apt-get update && \
    apt-get install -y --no-install-recommends \
        php8.1 php8.1-cli php8.1-curl php8.1-xml php8.1-zip php8.1-gd php8.1-mbstring php8.1-mysql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependencias y compilar assets
RUN composer install --no-dev --optimize-autoloader && \
    npm install && \
    npm run build

RUN mkdir -p storage/framework/cache/data && \
    mkdir -p storage/framework/sessions && \
    mkdir -p storage/framework/views && \
    mkdir -p storage/logs && \
    mkdir -p storage/app/public && \
    mkdir -p storage/app/public/clientes/fotos && \
    mkdir -p storage/app/public/comprobantes/yape && \
    mkdir -p storage/app/public/comprobantes/efectivo && \
    mkdir -p storage/app/public/departamentos

RUN php artisan key:generate

RUN chown -R node:node /app && \
    chmod -R 775 storage && \
    chmod -R 775 bootstrap/cache

USER node

EXPOSE 8000

CMD ["bash", "./run.sh"]
