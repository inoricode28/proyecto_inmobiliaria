# 🚀 Guía Completa de Despliegue con Docker

## 📋 Resumen del Proyecto

**Proyecto:** Sistema Inmobiliario Laravel con Docker
**Arquitectura:** Multi-contenedor con Docker Compose
**Servicios:** Laravel + MySQL + Nginx

---

## 🏗️ Arquitectura de Contenedores

### Contenedores Principales:
1. **helper1-server** - Aplicación Laravel (PHP-FPM)
2. **helper1-mysql** - Base de datos MySQL 5.7
3. **helper1-nginx** - Servidor web Nginx (Proxy reverso)

### Puertos Expuestos:
- **80** - HTTP (Nginx)
- **443** - HTTPS (Nginx)
- **3306** - MySQL (solo interno)

---

## 🔧 Cambios Realizados Durante el Despliegue

### 1. Preparación del Entorno VPS

#### ✅ Instalación de Docker Compose
```bash
# Verificar si docker-compose existe
which docker-compose

# Si no existe, instalar:
sudo apt update
sudo apt install docker-compose-plugin

# O descargar la versión más reciente:
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### 2. Configuración de Archivos Nginx

#### ❌ Problema Encontrado: nginx.conf era un directorio
```bash
# Error original:
# failed to mount '/home/app/nginx/nginx.conf' to '/etc/nginx/nginx.conf' as it's not a directory

# Diagnóstico:
ls -la nginx/
# Resultado: nginx.conf aparecía como directorio (d)
```

#### ✅ Solución Implementada:
```bash
# 1. Eliminar el directorio incorrecto
rm -rf nginx/nginx.conf

# 2. Crear el archivo nginx.conf correcto
cat > nginx/nginx.conf << 'EOF'
user nginx;
worker_processes auto;
error_log /var/log/nginx/error.log warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';
    
    access_log /var/log/nginx/access.log main;
    
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    
    include /etc/nginx/conf.d/*.conf;
}
EOF

# 3. Crear configuración del sitio
cat > nginx/conf.d/default.conf << 'EOF'
# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name _;
    return 301 https://$server_name$request_uri;
}

# HTTPS Server
server {
    listen 443 ssl http2;
    server_name _;
    
    # SSL Configuration (usar certificados reales en producción)
    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    
    # Laravel application
    location / {
        proxy_pass http://helper1-server:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
EOF
```

### 3. Estructura de Directorios Creada

```
/home/app/
├── docker-compose.yml
├── .env
├── nginx/
│   ├── nginx.conf          # ✅ Archivo (no directorio)
│   └── conf.d/
│       └── default.conf    # ✅ Configuración del sitio
└── ssl/
    └── README.md           # ✅ Directorio para certificados SSL
```

### 4. Configuración del Archivo .env

#### Variables de Entorno Críticas:
```bash
# Base de datos
DB_CONNECTION=mysql
DB_HOST=helper1-mysql
DB_PORT=3306
DB_DATABASE=helper_db
DB_USERNAME=helper_user
DB_PASSWORD=SecurePassword123

# Aplicación
APP_NAME="Helper Inmobiliaria"
APP_ENV=production
APP_KEY=base64:GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Cache y sesiones
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 🚀 Metodología de Despliegue Paso a Paso

### FASE 1: Preparación del VPS

```bash
# 1. Conectar al VPS
ssh root@tu-vps-ip

# 2. Navegar al directorio del proyecto
cd /home/app

# 3. Verificar que Docker esté funcionando
docker --version
docker-compose --version
```

### FASE 2: Obtener la Imagen Docker

```bash
# 4. Descargar la imagen más reciente
docker pull raynorman/helper1:latest

# 5. Verificar que la imagen se descargó
docker images | grep helper1
```

### FASE 3: Configuración de Archivos

```bash
# 6. Crear estructura de directorios
mkdir -p nginx/conf.d ssl

# 7. Crear nginx.conf (ver contenido arriba)
nano nginx/nginx.conf

# 8. Crear configuración del sitio
nano nginx/conf.d/default.conf

# 9. Verificar que son archivos (no directorios)
ls -la nginx/
file nginx/nginx.conf  # debe mostrar: ASCII text
```

### FASE 4: Configuración de Variables de Entorno

```bash
# 10. Crear/editar archivo .env
nano .env

# 11. Verificar configuración de base de datos
grep DB_ .env
```

### FASE 5: Despliegue de Contenedores

```bash
# 12. Detener contenedores existentes (si los hay)
docker-compose down

# 13. Limpiar contenedores antiguos
docker system prune -f

# 14. Iniciar todos los servicios
docker-compose up -d

# 15. Verificar que todos los contenedores estén funcionando
docker ps
```

### FASE 6: Configuración de Base de Datos

```bash
# 16. Ejecutar migraciones
docker exec helper1-server php artisan migrate --force

# 17. Ejecutar seeders (datos iniciales)
docker exec helper1-server php artisan db:seed --force

# 18. Verificar estado de migraciones
docker exec helper1-server php artisan migrate:status
```

### FASE 7: Configuración Final de Laravel

```bash
# 19. Limpiar cache de configuración
docker exec helper1-server php artisan config:clear

# 20. Generar clave de aplicación (si es necesario)
docker exec helper1-server php artisan key:generate --force

# 21. Crear enlaces simbólicos para storage
docker exec helper1-server php artisan storage:link

# 22. Optimizar para producción
docker exec helper1-server php artisan config:cache
docker exec helper1-server php artisan route:cache
docker exec helper1-server php artisan view:cache
```

### FASE 8: Verificación Final

```bash
# 23. Verificar logs de todos los contenedores
docker-compose logs

# 24. Verificar conectividad de base de datos
docker exec helper1-server php artisan tinker --execute="DB::connection()->getPdo();"

# 25. Verificar usuarios creados
docker exec helper1-server php artisan tinker --execute="echo App\Models\User::count();"

# 26. Probar la aplicación
curl -I http://localhost
```

---

## 🔍 Comandos de Diagnóstico

### Verificar Estado de Contenedores:
```bash
docker ps                           # Contenedores activos
docker-compose ps                   # Estado de servicios
docker stats                        # Uso de recursos
```

### Ver Logs:
```bash
docker-compose logs helper1-server  # Logs de Laravel
docker-compose logs helper1-mysql   # Logs de MySQL
docker-compose logs helper1-nginx   # Logs de Nginx
docker-compose logs -f              # Logs en tiempo real
```

### Acceso a Contenedores:
```bash
docker exec -it helper1-server bash # Acceder al contenedor Laravel
docker exec -it helper1-mysql bash  # Acceder al contenedor MySQL
docker exec -it helper1-nginx bash  # Acceder al contenedor Nginx
```

---

## 🛠️ Solución de Problemas Comunes

### 1. Error: "docker-compose not found"
```bash
# Instalar docker-compose
sudo apt install docker-compose-plugin
```

### 2. Error: "nginx.conf is a directory"
```bash
# Eliminar directorio y crear archivo
rm -rf nginx/nginx.conf
touch nginx/nginx.conf
# Agregar contenido del archivo
```

### 3. Contenedor reiniciando constantemente
```bash
# Ver logs para identificar el problema
docker-compose logs [nombre-contenedor]
```

### 4. Error de conexión a base de datos
```bash
# Verificar variables de entorno
docker exec helper1-server env | grep DB_
# Verificar que MySQL esté funcionando
docker exec helper1-mysql mysqladmin ping
```

---

## 📊 Datos de Acceso por Defecto

### Usuario Administrador (DefaultUserSeeder):
- **Email:** john.doe@helper.app
- **Password:** Passw@rd

### Base de Datos:
- **Host:** helper1-mysql (interno)
- **Puerto:** 3306
- **Base de datos:** helper_db
- **Usuario:** helper_user

---

## 🔄 Comandos para Futuros Despliegues

### Actualización Rápida:
```bash
# 1. Descargar nueva imagen
docker pull raynorman/helper1:latest

# 2. Recrear solo el contenedor de la aplicación
docker-compose up -d --force-recreate helper1-server

# 3. Ejecutar migraciones si hay cambios
docker exec helper1-server php artisan migrate --force
```

### Backup de Base de Datos:
```bash
# Crear backup
docker exec helper1-mysql mysqldump -u helper_user -p helper_db > backup_$(date +%Y%m%d).sql

# Restaurar backup
docker exec -i helper1-mysql mysql -u helper_user -p helper_db < backup_file.sql
```

---

## 🔧 DETALLES TÉCNICOS ESPECÍFICOS

### **Configuración Detallada de docker-compose.yml**

#### Servicio helper1-server (Laravel):
```yaml
helper1-server:
  image: raynorman/helper1:latest
  container_name: helper1-server
  environment:
    - APP_ENV=production
    - APP_DEBUG=false
  depends_on:
    - helper1-mysql
  restart: unless-stopped
  ports:
    - "8000:8000"
  volumes:
    - ./storage:/var/www/html/storage
    - ./.env:/var/www/html/.env
```

**Detalles importantes:**
- **Puerto interno:** 8000 (PHP-FPM + Laravel Serve)
- **Volúmenes críticos:** Storage para archivos subidos y .env para configuración
- **Dependencia:** Espera a que MySQL esté listo antes de iniciar
- **Política de reinicio:** `unless-stopped` - se reinicia automáticamente excepto si se detiene manualmente

#### Servicio helper1-mysql (Base de Datos):
```yaml
helper1-mysql:
  image: mysql:5.7
  container_name: helper1-mysql
  environment:
    MYSQL_DATABASE: helper_db
    MYSQL_USER: helper_user
    MYSQL_PASSWORD: SecurePassword123
    MYSQL_ROOT_PASSWORD: RootPassword456
  restart: unless-stopped
  ports:
    - "3306:3306"
  volumes:
    - mysql_data:/var/lib/mysql
```

**Detalles importantes:**
- **Versión específica:** MySQL 5.7 (compatibilidad con Laravel)
- **Volumen persistente:** `mysql_data` para mantener datos entre reinicios
- **Variables críticas:** Base de datos, usuario y contraseñas deben coincidir con .env

#### Servicio helper1-nginx (Servidor Web):
```yaml
helper1-nginx:
  image: nginx:alpine
  container_name: helper1-nginx
  ports:
    - "80:80"
    - "443:443"
  volumes:
    - ./nginx/nginx.conf:/etc/nginx/nginx.conf:ro
    - ./nginx/conf.d:/etc/nginx/conf.d:ro
    - ./ssl:/etc/nginx/ssl:ro
  depends_on:
    - helper1-server
  restart: unless-stopped
```

**Detalles importantes:**
- **Imagen ligera:** nginx:alpine para menor uso de recursos
- **Montajes de solo lectura:** `:ro` previene modificaciones accidentales
- **Proxy reverso:** Redirige tráfico web hacia helper1-server:8000

### **Configuración Específica de Nginx**

#### nginx.conf - Configuración Principal:
```nginx
# Configuración optimizada para producción
user nginx;
worker_processes auto;                    # Usa todos los cores disponibles
error_log /var/log/nginx/error.log warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;              # Conexiones simultáneas por worker
    use epoll;                           # Método de E/O eficiente en Linux
    multi_accept on;                     # Acepta múltiples conexiones
}

http {
    # Tipos MIME y configuración básica
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    # Formato de logs personalizado
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';
    
    # Optimizaciones de rendimiento
    sendfile on;                         # Transferencia eficiente de archivos
    tcp_nopush on;                       # Optimiza paquetes TCP
    tcp_nodelay on;                      # Reduce latencia
    keepalive_timeout 65;                # Tiempo de conexión persistente
    types_hash_max_size 2048;           # Tamaño hash para tipos MIME
    
    # Compresión gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;
    
    # Incluir configuraciones de sitios
    include /etc/nginx/conf.d/*.conf;
}
```

#### default.conf - Configuración del Sitio:
```nginx
# Configuración específica para Laravel
upstream laravel_backend {
    server helper1-server:8000;
    keepalive 32;                        # Conexiones persistentes al backend
}

# Redirección HTTP a HTTPS
server {
    listen 80;
    server_name _;
    
    # Redirigir todo el tráfico HTTP a HTTPS
    return 301 https://$server_name$request_uri;
}

# Servidor HTTPS principal
server {
    listen 443 ssl http2;
    server_name _;
    
    # Configuración SSL/TLS
    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;
    
    # Headers de seguridad
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
    # Configuración de archivos estáticos
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        proxy_pass http://laravel_backend;
        expires 1M;
        add_header Cache-Control "public, immutable";
    }
    
    # Configuración principal de Laravel
    location / {
        proxy_pass http://laravel_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $server_name;
        
        # Timeouts
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 60s;
        
        # Buffering
        proxy_buffering on;
        proxy_buffer_size 128k;
        proxy_buffers 4 256k;
        proxy_busy_buffers_size 256k;
    }
    
    # Bloquear acceso a archivos sensibles
    location ~ /\. {
        deny all;
    }
    
    location ~ \.(env|log)$ {
        deny all;
    }
}
```

### **Variables de Entorno Detalladas (.env)**

#### Configuración de Base de Datos:
```bash
# Conexión MySQL
DB_CONNECTION=mysql
DB_HOST=helper1-mysql              # Nombre del contenedor MySQL
DB_PORT=3306                       # Puerto interno del contenedor
DB_DATABASE=helper_db              # Nombre de la base de datos
DB_USERNAME=helper_user            # Usuario de la base de datos
DB_PASSWORD=SecurePassword123      # Contraseña del usuario

# Configuración de conexión
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

#### Configuración de Aplicación:
```bash
# Información básica
APP_NAME="Helper Inmobiliaria"
APP_ENV=production                 # CRÍTICO: Debe ser 'production'
APP_KEY=base64:GENERATED_KEY_HERE  # Generar con: php artisan key:generate
APP_DEBUG=false                    # CRÍTICO: Debe ser 'false' en producción
APP_URL=https://tu-dominio.com     # URL pública de la aplicación

# Configuración de zona horaria
APP_TIMEZONE=America/Mexico_City   # Ajustar según ubicación
```

#### Configuración de Cache y Sesiones:
```bash
# Drivers de cache
CACHE_DRIVER=file                  # Opciones: file, redis, memcached
SESSION_DRIVER=file                # Almacenamiento de sesiones
SESSION_LIFETIME=120               # Duración de sesión en minutos

# Queue (colas de trabajo)
QUEUE_CONNECTION=sync              # Para producción considerar: redis, database
```

#### Configuración de Email:
```bash
# Configuración SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com           # Servidor SMTP
MAIL_PORT=587                      # Puerto SMTP
MAIL_USERNAME=tu-email@gmail.com   # Usuario SMTP
MAIL_PASSWORD=tu-app-password      # Contraseña de aplicación
MAIL_ENCRYPTION=tls                # Encriptación TLS
MAIL_FROM_ADDRESS=noreply@helper.app
MAIL_FROM_NAME="${APP_NAME}"
```

### **Comandos de Mantenimiento Específicos**

#### Optimización de Laravel para Producción:
```bash
# 1. Limpiar todos los caches
docker exec helper1-server php artisan cache:clear
docker exec helper1-server php artisan config:clear
docker exec helper1-server php artisan route:clear
docker exec helper1-server php artisan view:clear

# 2. Generar caches optimizados
docker exec helper1-server php artisan config:cache
docker exec helper1-server php artisan route:cache
docker exec helper1-server php artisan view:cache

# 3. Optimizar autoloader de Composer
docker exec helper1-server composer dump-autoload --optimize

# 4. Crear enlaces simbólicos para storage
docker exec helper1-server php artisan storage:link
```

#### Monitoreo y Logs:
```bash
# Ver logs en tiempo real
docker-compose logs -f --tail=100

# Ver logs específicos por servicio
docker-compose logs -f helper1-server --tail=50
docker-compose logs -f helper1-mysql --tail=50
docker-compose logs -f helper1-nginx --tail=50

# Ver uso de recursos
docker stats --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.NetIO}}"

# Información detallada de contenedores
docker inspect helper1-server | jq '.[0].State'
```

#### Comandos de Base de Datos Avanzados:
```bash
# Conectar directamente a MySQL
docker exec -it helper1-mysql mysql -u helper_user -p helper_db

# Verificar tablas creadas
docker exec helper1-mysql mysql -u helper_user -p helper_db -e "SHOW TABLES;"

# Ver estado de migraciones
docker exec helper1-server php artisan migrate:status

# Rollback de migraciones (CUIDADO en producción)
docker exec helper1-server php artisan migrate:rollback --step=1

# Verificar conexión desde Laravel
docker exec helper1-server php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Conexión exitosa a la base de datos';
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage();
}
"
```

### **Estructura de Archivos de Configuración**

#### Ubicación de archivos críticos en el VPS:
```
/home/app/
├── docker-compose.yml              # Orquestación de contenedores
├── .env                           # Variables de entorno (SENSIBLE)
├── nginx/
│   ├── nginx.conf                 # Configuración principal de Nginx
│   └── conf.d/
│       └── default.conf           # Configuración del sitio Laravel
├── ssl/
│   ├── cert.pem                   # Certificado SSL (crear/obtener)
│   └── key.pem                    # Clave privada SSL (crear/obtener)
└── storage/                       # Archivos subidos por usuarios
    ├── app/
    ├── framework/
    └── logs/
```

#### Permisos de archivos importantes:
```bash
# Establecer permisos correctos
chmod 600 .env                     # Solo lectura para propietario
chmod 644 nginx/nginx.conf         # Lectura para todos
chmod 644 nginx/conf.d/default.conf
chmod 600 ssl/key.pem              # Clave privada solo para propietario
chmod 644 ssl/cert.pem             # Certificado público
chmod -R 755 storage/              # Permisos de escritura para Laravel
```

### **Certificados SSL para Producción**

#### Generar certificados auto-firmados (desarrollo):
```bash
# Crear directorio SSL si no existe
mkdir -p ssl

# Generar certificado auto-firmado
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout ssl/key.pem \
    -out ssl/cert.pem \
    -subj "/C=MX/ST=Estado/L=Ciudad/O=Helper/OU=IT/CN=helper.local"
```

#### Para producción con Let's Encrypt:
```bash
# Instalar certbot
sudo apt install certbot python3-certbot-nginx

# Obtener certificado (requiere dominio válido)
sudo certbot --nginx -d tu-dominio.com

# Copiar certificados a directorio del proyecto
sudo cp /etc/letsencrypt/live/tu-dominio.com/fullchain.pem ssl/cert.pem
sudo cp /etc/letsencrypt/live/tu-dominio.com/privkey.pem ssl/key.pem
```

### **Troubleshooting Avanzado**

#### Problemas comunes y soluciones:

**1. Contenedor Laravel no inicia:**
```bash
# Ver logs detallados
docker-compose logs helper1-server

# Verificar configuración PHP
docker exec helper1-server php -v
docker exec helper1-server php -m | grep -i mysql

# Verificar permisos de storage
docker exec helper1-server ls -la storage/
```

**2. Error de conexión a base de datos:**
```bash
# Verificar que MySQL esté funcionando
docker exec helper1-mysql mysqladmin ping -u root -p

# Verificar variables de entorno
docker exec helper1-server env | grep DB_

# Probar conexión manual
docker exec helper1-mysql mysql -u helper_user -p helper_db -e "SELECT 1;"
```

**3. Nginx devuelve 502 Bad Gateway:**
```bash
# Verificar que Laravel esté respondiendo
docker exec helper1-server curl -I http://localhost:8000

# Verificar configuración de Nginx
docker exec helper1-nginx nginx -t

# Ver logs de Nginx
docker-compose logs helper1-nginx | grep error
```

**4. Problemas de rendimiento:**
```bash
# Monitorear recursos
docker stats

# Verificar procesos dentro del contenedor
docker exec helper1-server ps aux

# Verificar logs de PHP-FPM
docker exec helper1-server tail -f /var/log/php-fpm.log
```

---

## ✅ Checklist de Despliegue

- [ ] VPS preparado con Docker y Docker Compose
- [ ] Imagen descargada: `raynorman/helper1:latest`
- [ ] Estructura de directorios creada
- [ ] Archivo `nginx/nginx.conf` creado (no directorio)
- [ ] Configuración `nginx/conf.d/default.conf` creada
- [ ] Archivo `.env` configurado correctamente
- [ ] Contenedores iniciados: `docker-compose up -d`
- [ ] Migraciones ejecutadas: `php artisan migrate --force`
- [ ] Seeders ejecutados: `php artisan db:seed --force`
- [ ] Cache optimizado para producción
- [ ] Aplicación accesible vía web

---

**Fecha de creación:** $(date)
**Versión:** 1.0
**Autor:** Asistente de Despliegue