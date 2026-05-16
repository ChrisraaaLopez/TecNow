# Guía de Configuración de Docker — TecNow

Instructivo paso a paso para configurar e iniciar el entorno de desarrollo con Docker para el proyecto **TecNow** (red social en Laravel 12).

---

## 📋 Requisitos previos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

| Herramienta | Versión mínima | Descarga |
|---|---|---|
| Docker Desktop | 4.x o superior | https://www.docker.com/products/docker-desktop |
| Git | Cualquier versión reciente | https://git-scm.com |

> ⚠️ **Windows:** Docker Desktop debe estar corriendo con el motor WSL2 activado.  
> Ve a **Docker Desktop → Settings → General** y verifica que esté marcado **"Use the WSL 2 based engine"**.

---

## 📁 Estructura de archivos necesaria

Antes de levantar Docker, verifica que tu proyecto tenga estos archivos en la raíz:

```
TecNow/
├── app/
├── public/
├── resources/
├── routes/
├── docker/
│   ├── nginx.conf        ← Configuración del servidor web
│   └── start.sh          ← Script de inicio del contenedor
├── .env                  ← Variables de entorno (NO se sube a GitHub)
├── .dockerignore         ← Archivos ignorados por Docker
├── docker-compose.yml    ← Orquestación de contenedores
└── Dockerfile            ← Imagen del contenedor de Laravel
```

Si alguno de estos archivos falta, revisa la sección **"Archivos de configuración"** al final de este documento.

---

## ⚙️ Paso 1 — Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/tecnow.git
cd tecnow
```

---

## ⚙️ Paso 2 — Configurar el archivo `.env`

El repositorio incluye un archivo `.env.example`. Cópialo y renómbralo:

```bash
# En Windows (PowerShell)
Copy-Item .env.example .env

# En macOS / Linux
cp .env.example .env
```

Luego abre el `.env` y configura las credenciales para Docker:

```env
APP_NAME=TecNow
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db           # ← IMPORTANTE: debe ser "db", no localhost
DB_PORT=3306
DB_DATABASE=tecnow
DB_USERNAME=tecnow
DB_PASSWORD=tecnow123
```

> 🔑 **Importante:** El valor de `DB_HOST` debe ser `db` (el nombre del servicio en Docker), **no** `localhost` ni `127.0.0.1`.

---

## ⚙️ Paso 3 — Construir y levantar los contenedores

Abre una terminal en la raíz del proyecto y ejecuta:

```bash
docker compose up -d --build
```

Este comando:
- Construye la imagen de Laravel con PHP, Nginx y Node.js
- Descarga la imagen de MySQL 8.0
- Levanta ambos contenedores en segundo plano

> ⏳ La primera vez puede tardar entre 3 y 5 minutos dependiendo de tu conexión a internet.

Deberías ver una salida similar a esta:

```
✔ Image tecnow-app       Built
✔ Network tecnow-network Created
✔ Container tecnow-db    Started
✔ Container tecnow-app   Started
```

---

## ⚙️ Paso 4 — Verificar que los contenedores están corriendo

```bash
docker compose ps
```

Ambos contenedores deben aparecer con estado `running`:

```
NAME          IMAGE        STATUS
tecnow-app    tecnow-app   running
tecnow-db     mysql:8.0    running
```

> ⚠️ Si `tecnow-db` aparece como `restarting`, espera 30 segundos y vuelve a verificar. Si persiste, revisa la sección de **Solución de problemas** al final.

---

## ⚙️ Paso 5 — Generar la clave de la aplicación

```bash
docker compose exec app php artisan key:generate
```

---

## ⚙️ Paso 6 — Ejecutar las migraciones

```bash
docker compose exec app php artisan migrate
```

Si el proyecto tiene seeders para datos de prueba:

```bash
docker compose exec app php artisan migrate --seed
```

---

## ⚙️ Paso 7 — Compilar los assets de Vite

```bash
docker compose exec app npm run build
```

---

## ✅ Paso 8 — Abrir el proyecto

Abre tu navegador en:

```
http://localhost:8000
```

¡Listo! El proyecto TecNow debería estar corriendo correctamente.

---

## 🔄 Comandos de uso frecuente

| Acción | Comando |
|---|---|
| Levantar contenedores | `docker compose up -d` |
| Detener contenedores | `docker compose down` |
| Ver logs en tiempo real | `docker compose logs -f app` |
| Entrar al contenedor | `docker compose exec app sh` |
| Limpiar caché de Laravel | `docker compose exec app php artisan cache:clear` |
| Limpiar caché de configuración | `docker compose exec app php artisan config:clear` |
| Reconstruir imagen | `docker compose up -d --build` |

---

## 🔁 Volver al desarrollo local (sin Docker)

Si normalmente trabajas con `php artisan serve` y `npm run dev`, simplemente detén Docker y cambia el `.env`:

```bash
docker compose down
```

Luego en tu `.env` cambia:

```env
DB_HOST=127.0.0.1
DB_USERNAME=root
DB_PASSWORD=
```

Y trabaja como de costumbre:

```bash
# Terminal 1
npm run dev

# Terminal 2
php artisan serve
```

> ⚠️ No puedes tener Docker y `php artisan serve` corriendo al mismo tiempo en el puerto 8000.

---

## 🛠️ Solución de problemas frecuentes

### ❌ `no configuration file provided: not found`
La terminal no está en la carpeta raíz del proyecto.
```bash
cd C:\ruta\a\TecNow
docker compose up -d --build
```

### ❌ Puerto 3306 ocupado
Tu MySQL local ya usa ese puerto. Cambia en `docker-compose.yml`:
```yaml
ports:
  - "3307:3306"  # Cambia 3306 por 3307
```

### ❌ `tecnow-db` en estado `restarting`
Los datos de MySQL están corruptos o las credenciales no coinciden. Borra los datos y reinicia:
```bash
docker compose down
Remove-Item -Recurse -Force docker\mysql   # PowerShell (Windows)
# rm -rf docker/mysql                      # macOS / Linux
docker compose up -d
```

### ❌ `Vite manifest not found`
Los assets no se compilaron. Ejecuta:
```bash
docker compose exec app npm install
docker compose exec app npm run build
```

### ❌ Error 500 — `Connection refused` al migrar
MySQL todavía está iniciándose. Espera 30 segundos y vuelve a intentarlo:
```bash
docker compose exec app php artisan migrate
```

---

## 📄 Archivos de configuración

Si algún archivo de configuración falta en el proyecto, aquí están los contenidos completos:

<details>
<summary><strong>Dockerfile</strong></summary>

```dockerfile
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx curl zip unzip git nodejs npm \
    libpng-dev libzip-dev oniguruma-dev libxml2-dev

RUN docker-php-ext-install \
    pdo_mysql mbstring bcmath pcntl zip exif

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --optimize-autoloader
RUN npm install && npm run build

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh
RUN sed -i 's/\r//' /start.sh

EXPOSE 8080
CMD ["/start.sh"]
```

</details>

<details>
<summary><strong>docker/nginx.conf</strong></summary>

```nginx
server {
    listen 8080;
    root /var/www/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

</details>

<details>
<summary><strong>docker/start.sh</strong></summary>

```bash
#!/bin/sh
php-fpm -D
nginx -g "daemon off;"
```

</details>

<details>
<summary><strong>.dockerignore</strong></summary>

```
node_modules
vendor
.env
.git
docker/mysql
*.log
```

</details>

---

## 📌 Notas finales

- Los datos de MySQL se guardan en `docker/mysql/` — esta carpeta **no se sube a GitHub**.
- El archivo `.env` contiene credenciales y **no se sube a GitHub**.
- En Windows la carga de páginas puede ser más lenta que en macOS/Linux; esto es normal debido a la virtualización de Docker.
- Para mejorar el rendimiento puedes ejecutar: `docker compose exec app php artisan config:cache && php artisan route:cache && php artisan view:cache`
