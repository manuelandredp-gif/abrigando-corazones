# Abrigando Corazones

Sitio web de **Abrigando Corazones**, campaña solidaria de Tacna que reúne abrigo, esperanza y solidaridad para las familias de las zonas altoandinas.

El sitio informa sobre la campaña, muestra los puntos de acopio, publica noticias y recibe solicitudes de donación, voluntariado y consultas por formulario.

## Características

- Páginas públicas: inicio, quiénes somos, cómo ayudar, donar, voluntariado, puntos de acopio, aliados, impacto, noticias, convocatoria artística y contacto.
- Formulario de contacto con protección CSRF y aviso por correo al equipo según el tipo de solicitud.
- Envío de correo por SMTP (PHPMailer) o por API HTTPS (Brevo, Resend, MailerSend) para hostings que bloquean el puerto 587.
- Métricas propias de visitas y eventos, con reporte diario por correo y panel de solicitudes protegido por token.
- Cabeceras de seguridad, política de contenido con nonce y bloqueo por `.htaccess` del código interno y los archivos de configuración.

## Tecnologías

| Capa | Tecnología |
| --- | --- |
| Backend | PHP 7.4 o superior, arquitectura MVC sin framework |
| Base de datos | MySQL / MariaDB (PDO) |
| Correo | PHPMailer 6.9 |
| Frontend | HTML, CSS y JavaScript sin dependencias |
| Servidor | Apache con `mod_rewrite` |

## Estructura

```
├── index.php            Front controller: sesión, autoload y enrutador
├── .htaccess            Reescritura de URLs, bloqueos y cabeceras
├── app/
│   ├── config/          Configuración general, base de datos, correo y métricas
│   ├── controllers/     HomeController, ContactController, MetricsController
│   ├── core/            Router, Controller, Model, Database, Csrf, Migrator
│   ├── models/          Contacto, puntos de acopio, metas y estadísticas
│   ├── services/        Notificaciones, transporte de correo, métricas y reporte diario
│   └── views/           Layouts, páginas, plantillas de correo y parciales
├── css/  js/  images/   Recursos estáticos
├── logs/  uploads/      Carpetas de trabajo (su contenido no se versiona)
└── vendor/              PHPMailer y autoload
```

## Instalación

1. Clona el repositorio dentro de la carpeta pública de tu servidor (por ejemplo `htdocs` en XAMPP):

   ```bash
   git clone https://github.com/manuelandredp-gif/abrigando-corazones.git
   ```

2. Crea los archivos de configuración local a partir de las plantillas que están en `app/config/`:

   | Plantilla | Copiar como | Contenido |
   | --- | --- | --- |
   | `database.local.example.php` | `database.local.php` | Servidor, usuario, contraseña y nombre de la base de datos |
   | `mail.local.example.php` | `mail.local.php` | Cuenta SMTP o clave de API y destinatarios de los avisos |
   | `metrics.local.example.php` | `metrics.local.php` | Token del reporte, destinatarios y hora de envío |

3. Crea la base de datos MySQL con codificación `utf8mb4` y completa sus datos en `database.local.php`.

4. Abre el sitio en el navegador, por ejemplo `http://localhost/abrigando-corazones/`.

Las dependencias ya están incluidas en `vendor/`, así que no hace falta ejecutar Composer.

### Base de datos

Las tablas de métricas (`metricas_visitas`, `metricas_eventos`, `metricas_envios`) se crean solas la primera vez que se usan.

Las tablas `registros_contacto`, `puntos_acopio`, `metas_donacion` y `estadisticas` deben existir previamente: su esquema todavía no está incluido en este repositorio.

## Configuración

Cada valor se puede definir como variable de entorno o en el archivo `*.local.php` correspondiente. Además, `app/config/config.php` reconoce estas variables:

| Variable | Valor por defecto | Uso |
| --- | --- | --- |
| `APP_ENV` | `production` | `development` o `production` |
| `APP_DEBUG` | `false` | Muestra los errores en pantalla; solo para desarrollo |
| `APP_BASE_URL` | Se detecta sola | URL base del sitio si la detección automática no sirve |

## Rutas principales

| Ruta | Descripción |
| --- | --- |
| `/` | Inicio |
| `/quienes-somos`, `/como-ayudar`, `/donar`, `/voluntariado` | Información de la campaña |
| `/puntos-de-acopio` y `/puntos-de-acopio/{slug}` | Listado y detalle de puntos de acopio |
| `/noticias` y `/noticias/{slug}` | Listado y detalle de noticias |
| `/aliados`, `/impacto`, `/convocatoria-artistica`, `/contacto` | Otras páginas públicas |
| `/solicitudes?token=…` | Panel de solicitudes recibidas |
| `/metricas/reporte?token=…` | Dispara el reporte diario (pensado para un cron) |
| `/metricas/diagnostico-correo?token=…` | Diagnóstico del envío de correo |

Las rutas con `token` exigen el valor de `METRICS_REPORT_TOKEN`.

## Despliegue

El proyecto está preparado para hosting compartido como InfinityFree: sube todo el contenido a `htdocs` y crea los tres archivos `*.local.php` directamente en el servidor.

Si el hosting bloquea el puerto 587, define `MAIL_API_PROVIDER` y `MAIL_API_KEY` en `mail.local.php` para enviar el correo por HTTPS.

## Seguridad

- Los archivos `*.local.php` contienen credenciales: no se versionan y el `.htaccess` impide abrirlos desde el navegador.
- El contenido de `logs/` y `uploads/` tampoco se versiona, porque puede incluir datos personales.
- Si un token o una contraseña se filtra, cámbialo en el archivo local y en el servicio correspondiente.

## Licencia

MIT, según se declara en `composer.json`.
