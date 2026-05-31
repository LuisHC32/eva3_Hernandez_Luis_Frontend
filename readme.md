# SERCOTEC Landing — Frontend

Landing page de SERCOTEC desarrollada con **WordPress** y un tema custom (`sercotec-landing`). El entorno local corre con **Docker** (MySQL, WordPress PHP-FPM y Nginx).

## Inicio rápido

### 1. Clonar e instalar variables de entorno

```bash
git clone https://github.com/LuisHC32/eva3_Hernandez_Luis_Frontend
cd eva3_Hernandez_Luis_Frontend
cp .env.example .env
```

### 2. Levantar los contenedores

```bash
docker compose up -d
```

### 3. Acceder al sitio

| Recurso | URL |
|---------|-----|
| Sitio web | http://localhost:8080 |
| Panel WordPress | http://localhost:8080/wp-admin |

> El puerto por defecto es `8080`. Puedes cambiarlo con `WORDPRESS_PORT` en `.env`.

### 4. Instalación de WordPress (primera vez)

Si es una instalación nueva, completa el asistente en:

```
http://localhost:8080/wp-admin/install.php
```

### 5. Activar el tema y configurar la página de inicio

```bash
docker compose exec wordpress php /var/www/html/wp-content/themes/sercotec-landing/setup.php
```

Este script:

- Activa el tema `sercotec-landing`
- Crea la página **Inicio** como front page
- Inserta servicios de ejemplo

## Variables de entorno

Definidas en `.env` (ver `.env.example`):

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| `WORDPRESS_PORT` | Puerto local de Nginx | `8080` |
| `MYSQL_DATABASE` | Nombre de la base de datos | `wordpress` |
| `MYSQL_USER` | Usuario MySQL | `wordpress` |
| `MYSQL_PASSWORD` | Contraseña del usuario | `change_me` |
| `MYSQL_ROOT_PASSWORD` | Contraseña root de MySQL | `change_me_root` |
| `PROJECT_NAME` | Carpeta WordPress montada en Docker | `SERCOTEC` |

## Arquitectura Docker

```
┌─────────────┐     ┌──────────────────┐     ┌─────────┐
│   Nginx     │────▶│  WordPress FPM   │────▶│  MySQL  │
│  :8080→80   │     │  PHP 8.5         │     │  8.0    │
└─────────────┘     └──────────────────┘     └─────────┘
```

| Servicio | Imagen | Función |
|----------|--------|---------|
| `nginx` | nginx:alpine | Servidor web y proxy a PHP-FPM |
| `wordpress` | wordpress:php8.5-fpm | WordPress |
| `db` | mysql:8.0 | Base de datos |

Los datos de MySQL se persisten en un volumen Docker (`db_data`). El código WordPress se monta desde `./SERCOTEC`.

## Estructura del proyecto

```
.
├── .env.example          # Plantilla de variables de entorno
├── docker-compose.yml    # Orquestación Docker
├── nginx/
│   └── default.conf      # Configuración Nginx
└── SERCOTEC/             # Instalación WordPress
    └── wp-content/
        └── themes/
            └── sercotec-landing/   # Tema custom
                ├── assets/         # CSS, JS, iconos
                ├── inc/            # CPTs, settings, seguridad
                ├── template-parts/ # Secciones de la landing
                ├── front-page.php
                └── setup.php       # Script de activación
```

## Tema `sercotec-landing`

Landing de una sola página con secciones modulares:

| Sección | Contenido |
|---------|-----------|
| Hero (Inicio) | Badge, título, botones, estadísticas, tarjetas |
| Nosotros | Tarjetas editables (CPT) |
| Servicios | Carrusel / grid de servicios (CPT) |
| Testimonios | Carrusel con videos o imágenes |
| FAQ | Preguntas frecuentes (CPT) |
| Contacto | Formulario AJAX con validación |

### Panel de administración

Desde **WP Admin** puedes editar cada sección sin tocar código:

| Menú | Qué configura |
|------|---------------|
| **Inicio** | Hero section |
| **Tarjetas Nosotros** | Cards + configuración de sección |
| **Servicios** | Servicios + textos de sección |
| **Testimonios** | Testimonios + configuración |
| **FAQ** | Preguntas + encabezado |
| **Contacto** | Datos de contacto, mensajes recibidos |
| **Contacto → CAPTCHA y seguridad** | Turnstile + rate limiting |
| **Footer** | Logo, enlaces, redes sociales |

### Formulario de contacto — seguridad

- Validación en cliente (JavaScript) y servidor (PHP)
- Nonce CSRF
- Rate limiting por IP (configurable en admin)
- CAPTCHA con [Cloudflare Turnstile](https://dash.cloudflare.com/?to=/:account/turnstile) (opcional)

Claves Turnstile: **Contacto → CAPTCHA y seguridad**.
