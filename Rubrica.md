# Propone la creación de componentes. 
El proyecto SERCOTEC Landing adopta una arquitectura basada en componentes reutilizables dentro de WordPress, donde los mismos bloques de contenido pueden utilizarse en distintas secciones, administrarse desde el panel de control y exponerse mediante una API REST. Su elemento principal es la Tarjeta Universal (about-card), un componente compartido que evita la duplicación de código y mantiene una experiencia visual consistente. Cada componente combina interfaz, datos y comportamiento bajo un contrato definido, encapsulando presentación, configuración y modelo de datos mediante plantillas parametrizadas, Custom Post Types y funciones adaptadoras que conectan la base de datos con la vista.

Tambien se incorporo un sistema de layouts adaptativos que permite organizar dinámicamente las tarjetas como cuadrículas o carruseles según sus metadatos, ofreciendo flexibilidad de diseño sin necesidad de modificar código. Además, utiliza un único componente configurable capaz de representar distintos formatos visuales mediante propiedades compartidas, reduciendo la complejidad de mantenimiento y garantizando uniformidad entre secciones. También integra la comunicación entre componentes, permitiendo que acciones realizadas en una tarjeta, como la selección de un servicio, se reflejen automáticamente en el formulario de contacto para mejorar la experiencia del usuario.

En materia de seguridad, las validaciones del formulario, la protección contra automatizaciones maliciosas y las restricciones de acceso se encuentran desacopladas de la capa de presentación mediante mecanismos como nonces, limitación de solicitudes por IP y CAPTCHA, mejorando la mantenibilidad y robustez del sistema. Finalmente, la infraestructura se encuentra completamente containerizada con Docker, separando las responsabilidades entre servidor web, procesamiento PHP y base de datos, lo que facilita la instalación, el despliegue y la reproducción del entorno en distintos equipos y plataformas.

---

# Resuelve problemas complejos y multifacéticos en el desarrollo de componentes. 

El análisis del sitio web, identificó problemas de experiencia de usuario, accesibilidad, arquitectura técnica y gestión territorial. Entre los principales hallazgos destacan la alta carga cognitiva generada por extensos bloques de texto, la dispersión de la información de contacto, la incertidumbre respecto a horarios y puntos de atención, la presencia de secciones incompletas y elementos sin personalizar, además de dificultades para orientar a distintos tipos de usuarios mediante un flujo claro de navegación y contacto.

Como respuesta a estos problemas, se propone una arquitectura basada en componentes reutilizables que mejora la organización y presentación de la información. Las métricas de impacto se transforman en elementos visuales destacados, los servicios se presentan mediante grids y carruseles adaptativos, el contacto se centraliza en un único formulario conectado con las tarjetas de servicios y las preguntas frecuentes funcionan como mecanismo de autoservicio para reducir fricciones. Además, la incorporación de una API REST y un tema personalizado permite una futura evolución hacia soluciones desacopladas y aplicaciones externas.

Durante la implementación se abordaron desafíos técnicos relacionados con la configuración de la API REST, autenticación, protección de datos, reutilización de componentes y prevención de spam. Para ello se aplicaron soluciones como reglas de reescritura, permisos diferenciados, uso de un único componente reutilizable para múltiples secciones, organización simplificada del panel administrativo y mecanismos de seguridad como nonces, limitación de solicitudes y CAPTCHA.

---

# Formula una solución que incluye los conceptos de accesibilidad y usabilidad.

##### Barra de accesibilidad
Se añadió una pestaña fija “Accesibilidad” con el símbolo internacional ISO, visible en todas las páginas del tema. Permite aumentar y disminuir el tamaño del texto (7 niveles), activar alto contraste, aplicar escala de grises y restablecer la configuración.

##### Formulario de contacto
El formulario está pensado para ser usable y accesible: labels visibles en cada campo, autocomplete (name, email, tel), aria-describedby hacia mensajes de error, role="alert" en errores y aria-live="polite" en el feedback de envío. La validación funciona en cliente y servidor; los campos inválidos reciben aria-invalid. Hay nonce CSRF, rate limiting por IP y CAPTCHA Turnstile opcional. Tras un error, el foco va al primer campo con problema.

##### Carruseles (servicios y testimonios)
Los carruseles incluyen botones Anterior/Siguiente con aria-label, puntos de navegación con role="tablist", slides ocultos con aria-hidden y etiquetas descriptivas por slide. En móvil se adapta la cantidad de tarjetas visibles.

##### FAQ (acordeón)
Las preguntas usan el patrón acordeón accesible: botones con aria-expanded, aria-controls, respuestas con role="region" y aria-labelledby. Solo una respuesta abierta a la vez; el icono decorativo lleva aria-hidden="true".

##### Contraste y color en tarjetas
Las tarjetas de Nosotros y Servicios permiten elegir color de fondo y color de texto desde presets, con lógica que calcula contraste legible (text_color según fondo claro u oscuro). En modo alto contraste de la barra, se fuerzan bordes y colores legibles.

##### Imágenes e iconos
El logo del navbar/footer incluye texto alt editable. Los iconos sociales del footer tienen aria-label descriptivo (ej. “Síguenos en Instagram”). Las imágenes usan loading="lazy" donde corresponde.

##### Usabilidad general
El CMS editable (WP Admin) permite cambiar contenidos sin tocar código. Los servicios con acceso rápido enlazan al formulario y preseleccionan el servicio (data-contact-service). La API REST expone el contenido para otros consumidores.

---

# Gestiona el despliegue y la integración de los componentes en entornos complejos y multidisciplinarios necesarios según la naturaleza del proyecto.

El proyecto integra infraestructura (DevOps), CMS (WordPress/PHP), base de datos (MySQL), servidor web (Nginx), seguridad y API REST en un mismo ecosistema.

La solución adoptada fue containerizar el entorno con Docker Compose, separando responsabilidades en tres servicios: Nginx (entrada HTTP y proxy FastCGI), WordPress PHP 8.5-FPM (lógica de aplicación) y MySQL 8.0 (persistencia). MySQL arranca solo cuando pasa el healthcheck; WordPress depende de la base de datos y Nginx expone el puerto configurable (WORDPRESS_PORT, por defecto 8080) y un archivo .env.

El código WordPress se monta como volumen desde ./SERCOTEC, de modo que los cambios en el tema se reflejan al instante sin reconstruir imágenes. Los datos de MySQL persisten en el volumen db_data.

---

# Elabora un detalle de buenas prácticas. 


## 1. Containerización y orquestación de servicios

**Descripción:** Empaquetar la aplicación en contenedores independientes con dependencias explícitas y arranque ordenado.

**Elementos observables y medibles:**
- Tres servicios definidos en `docker-compose.yml` (Nginx, WordPress FPM, MySQL).
- Healthcheck de MySQL con reintentos configurados (`interval: 10s`, `retries: 5`).
- Tiempo de levantamiento reproducible: `docker compose up -d` en cualquier máquina con Docker instalado.

**Acciones, herramientas y técnicas:**
- Docker Compose para orquestación declarativa.
- Imágenes oficiales: `nginx:alpine`, `wordpress:php8.5-fpm`, `mysql:8.0`.
- `depends_on` con `condition: service_healthy` para evitar condiciones de carrera.

**Resultado esperado:** Entorno idéntico entre desarrolladores, evaluadores y futuros despliegues; reducción de errores del tipo «funciona en mi PC».

**Ejemplo concreto:** WordPress no inicia hasta que MySQL responde al ping; Nginx expone el sitio en `http://localhost:8080` mapeando `${WORDPRESS_PORT}:80`.

## 2. Configuración externalizada mediante variables de entorno

**Descripción:** Separar secretos y parámetros configurables del código fuente versionado.

**Elementos observables y medibles:**
- Archivo `.env.example` con seis variables documentadas (`WORDPRESS_PORT`, credenciales MySQL, `PROJECT_NAME`).
- Ninguna contraseña hardcodeada en `docker-compose.yml`.
- Cambio de puerto verificable modificando solo `.env` sin editar YAML.

**Acciones, herramientas y técnicas:**
- Plantilla `.env.example` + `.env` local (excluido de Git).
- Sustitución de variables en Compose (`${MYSQL_PASSWORD}`, etc.).

**Resultado esperado:** Despliegue seguro y portable; rotación de credenciales sin cambios en código.

**Ejemplo concreto:** Un evaluador clona el repo, ejecuta `cp .env.example .env`, ajusta contraseñas y levanta el stack sin conflictos de puerto.

## 3. Separación de capas en el tema WordPress

**Descripción:** Dividir presentación, lógica de dominio, configuración e integración HTTP en carpetas con responsabilidad única.

**Elementos observables y medibles:**
- `template-parts/` — solo markup de secciones.
- `inc/` — CPTs, settings, REST API, seguridad.
- `assets/` — CSS y JS unificados (`main.css`, `main.js`).
- `functions.php` — únicamente requires y bootstrap.

**Acciones, herramientas y técnicas:**
- Patrón «capas» inspirado en arquitectura limpia adaptada a WordPress.
- `get_template_part()` con `$args` como contrato entre capas.

**Resultado esperado:** Mantenibilidad, localización rápida de bugs y onboarding de nuevos desarrolladores en menos de una jornada.

**Ejemplo concreto:** Editar el formulario implica `template-parts/contact-form.php`; la validación servidor está en `inc/cpt-contact-messages.php`; la seguridad en `inc/contact-security.php`.

## 4. Componentes reutilizables con contrato de datos

**Descripción:** Un mismo componente visual sirve a múltiples secciones mediante un array normalizado.

**Elementos observables y medibles:**
- Un único archivo `about-card.php` usado en Nosotros y Servicios.
- Propiedades documentadas: `title`, `content_html`, `layout`, `color`, `quick_access`.
- Reducción medible de duplicación: cero plantillas HTML paralelas para tarjetas.

**Acciones, herramientas y técnicas:**
- Composición con `get_template_part( 'template-parts/about-card', null, array( 'card' => $data ) )`.
- Motor `sercotec_landing_group_cards_for_display()` para layouts grid/carrusel.

**Resultado esperado:** Consistencia visual institucional y menor deuda técnica al agregar secciones.

**Ejemplo concreto:** `services.php` invoca `about-cards-display.php` con el mismo contrato que la sección Nosotros, cambiando solo la fuente de datos (`sercotec_landing_get_services()`).

## 5. API REST versionada con permisos diferenciados

**Descripción:** Exponer contenido mediante endpoints HTTP con namespace, permisos por recurso y operaciones CRUD completas.

**Elementos observables y medibles:**
- Namespace `sercotec/v1` con rutas registradas en `inc/rest-api.php`.
- Lectura pública en servicios, nosotros, testimonios y FAQ; comentarios restringidos a `edit_posts`.
- Respuestas JSON verificables en Postman (código 200/201/401/403 según caso).

**Acciones, herramientas y técnicas:**
- `register_rest_route()` con `permission_callback` por método.
- Formatters compartidos (`sercotec_landing_rest_format_servicio`, etc.).
- Autenticación con contraseñas de aplicación WordPress.

**Resultado esperado:** Integración con clientes externos (Postman, apps móviles, CRM) sin duplicar lógica de negocio.

**Ejemplo concreto:** `GET /wp-json/sercotec/v1/servicios/31` devuelve `{ "item": { ... } }`; `POST /wp-json/sercotec/v1/faq` crea una pregunta con auth Basic.

## 6. Sanitización y validación sistemática de entradas

**Descripción:** Toda data que entra al sistema — formulario web o API — se valida y sanitiza antes de persistirse.

**Elementos observables y medibles:**
- REST: `sanitize_text_field`, `wp_kses_post`, `sanitize_email`, `absint` en create/update.
- Formulario: validación JS + PHP; campos con `maxlength` y `aria-invalid`.
- Verificación de `post_type` al resolver IDs en la API (404 si no coincide).

**Acciones, herramientas y técnicas:**
- Validación dual cliente/servidor (defensa en profundidad).
- Presets de color/layout validados contra catálogos (`sercotec_landing_card_color_presets()`).

**Resultado esperado:** Reducción de XSS, inyección y corrupción de datos; respuestas HTTP 400 ante campos obligatorios ausentes.

**Ejemplo concreto:** Crear FAQ sin `question` devuelve error `rest_missing_field` con status 400; contenido HTML de tarjetas pasa por `wp_kses_post`.

## 7. Seguridad en capas para el formulario de contacto

**Descripción:** Proteger el endpoint transaccional más expuesto con múltiples controles independientes.

**Elementos observables y medibles:**
- Nonce CSRF verificado con `check_ajax_referer`.
- Rate limiting: máximo N envíos por IP en ventana configurable (admin).
- CAPTCHA Cloudflare Turnstile activable desde panel.
- Comentarios/mensajes no expuestos en API pública.

**Acciones, herramientas y técnicas:**
- Módulo `inc/contact-security.php` desacoplado de la vista.
- Transients de WordPress para conteo por IP.
- Integración Turnstile vía script externo condicional.

**Resultado esperado:** Resistencia a spam automatizado y bots; datos personales accesibles solo a usuarios autenticados.

**Ejemplo concreto:** Tras superar el límite de envíos, el usuario recibe mensaje de error sin que el mensaje se guarde en el CPT `sercotec_mensaje`.


## 8. Accesibilidad web (WCAG) integrada en diseño y código

**Descripción:** Incorporar patrones ARIA, barra de accesibilidad y contraste desde el diseño, no como parche final.

**Elementos observables y medibles:**
- Barra con A−/A+, alto contraste, escala de grises y persistencia en `localStorage`.
- FAQ con acordeón WAI-ARIA (`aria-expanded`, `aria-controls`, `role="region"`).
- Formulario con `role="alert"`, `aria-live="polite"`, labels asociados.
- Carruseles con `aria-label`, `aria-hidden` en slides fuera de vista.

**Acciones, herramientas y técnicas:**
- Script temprano en `<head>` (`sercotec_landing_a11y_early_script`) para evitar flash sin ajustes.
- CSS con clases `a11y-high-contrast`, `data-a11y-font-scale`.
- Icono ISO de accesibilidad en botón flotante.

**Resultado esperado:** Mayor inclusión para usuarios con baja visión, daltonismo o sensibilidad al contraste; cumplimiento parcial de criterios WCAG 2.x.

**Ejemplo concreto:** Usuario activa alto contraste; fondo pasa a blanco, texto a negro y foco visible en amarillo (`outline: 3px solid #ff0`).


## 10. Automatización del aprovisionamiento inicial

**Descripción:** Reducir pasos manuales post-instalación mediante script idempotente.

**Elementos observables y medibles:**
- `setup.php` activa tema, crea front page e inserta datos semilla.
- Ejecución documentada en README: un comando Docker exec.
- Tiempo de setup reducido de ~15 minutos manuales a ~30 segundos automatizados.

**Acciones, herramientas y técnicas:**
- WP-CLI programático vía PHP (`switch_theme`, `wp_insert_post`).
- Seeds condicionales (`sercotec_landing_seed_default_services()` solo si no hay datos).

**Resultado esperado:** Demo y evaluación reproducibles; evaluador ve landing completa al primer acceso.

**Ejemplo concreto:** `docker compose exec wordpress php .../setup.php` deja servicios, FAQ y página Inicio listos en `http://localhost:8080`.

---

# Gestiona la colaboración y trabajo de equipo con herramientas de control de versiones. 

Para el desarrollo del proyecto se creó un repositorio en GitHub llamado "eva3_Hernandez_Luis_Frontend", donde se almacenó y versionó todo el código fuente. El control de versiones se gestionó mediante Git y GitHub Desktop, realizando commits periódicos para registrar cada bloque importante de funcionalidades, correcciones y mejoras implementadas durante el desarrollo. Debido a que el proyecto fue realizado de manera individual, no fue necesario utilizar ramas de trabajo colaborativas ni gestionar contribuciones de otros desarrolladores, manteniendo un flujo de trabajo centralizado sobre un único repositorio. Esto permitió llevar un seguimiento ordenado de la evolución del proyecto y contar con un historial completo de cambios durante todo el proceso de desarrollo.

---

# Diseña interfaces de usuario interactivas, comprensibles e intuitivas, incorporando conceptos de diseño centrado en el usuario y usabilidad. 

El proyecto aplica diseño centrado en el usuario en una landing de una sola página: secciones claras (Hero, Servicios, FAQ, Contacto), tarjetas escaneables en lugar de textos largos y un flujo simple hacia el formulario. Es interactiva (carruseles, acordeón FAQ, validación en vivo) e intuitiva (menú por anclas, CTA “Postula ahora”, servicios que preseleccionan el contacto). Incluye barra de accesibilidad (texto, contraste, grises) y patrones ARIA. El contenido se edita desde WP Admin sin tocar código.

---

# Aplica estrategias de optimización avanzadas. 

## Infraestructura y despliegue
Docker Compose con healthchecks evita arranques fallidos de WordPress sin base de datos. Variables en .env eliminan reconfiguración manual. Nginx cachea estáticos (expires max), usa FastCGI hacia PHP-FPM y resuelve /wp-json/ con rewrite dedicado — menos latencia en assets y respuestas JSON correctas sin Apache. setup.php reduce el aprovisionamiento de ~15 minutos manuales a un comando automatizado.

## Arquitectura y código
Un solo main.css y un solo main.js (frontend + admin) reducen peticiones HTTP y puntos de mantenimiento. El componente about-card reutilizado en Nosotros y Servicios elimina duplicación de plantillas y CSS. La API REST unificada en rest-api.php con formatters compartidos evita dos modelos de datos (web vs API). Versionado SERCOTEC_LANDING_VERSION invalida caché del navegador en cada entrega.

## Rendimiento frontend
loading="lazy" en imágenes, carruseles que solo muestran slides visibles (aria-hidden), preferencias de accesibilidad en localStorage.

## Eficiencia operativa y seguridad 
Rate limiting en contacto reduce carga de spam en BD y revisión manual. Validación dual cliente/servidor evita requests inválidos. CMS por secciones: el equipo SERCOTEC actualiza contenido sin redeploy.

---

# Integra el uso de frameworks en la interfaz de usuario. 

## Enfoque excepcional y creativo
En este proyecto no es solo una librería frontend aislada, sino un ecosistema integrado: WordPress como CMS y motor de aplicación, REST API nativa ampliada con sercotec/v1, Docker como orquestador de infraestructura y JavaScript/CSS modulares unificados en main.js y main.css. La interfaz se construye sobre capacidades del framework en lugar de pelear contra él.

---

# Aplica el consumo de endpoints.

Se implementó una API REST versionada (sercotec/v1) con operaciones CRUD y permisos diferenciados. El contenido de la landing se sirve actualmente mediante renderizado server-side (WordPress/PHP), mientras que la API queda disponible para consumo externo (Postman, aplicaciones móviles o futura migraciónes). El formulario de contacto utiliza AJAX nativo de WordPress; el consumo de endpoints REST desde el frontend JavaScript está diseñado como extensión arquitectónica.

---

# Implementa estrategias de seguridad avanzada.

El proyecto implementa una arquitectura de seguridad en capas que anticipa amenazas reales: CSRF, spam automatizado, bots, acceso no autorizado a la API, IDOR, XSS e inyección de datos. El formulario de contacto combina nonce WordPress, rate limiting configurable por IP, CAPTCHA Cloudflare Turnstile con verificación server-side y validación dual cliente/servidor con whitelist de servicios, todo desacoplado en un módulo dedicado (contact-security.php). La API REST versionada aplica permisos granulares por recurso y método, oculta mensajes con datos personales al público, valida tipos de post contra IDOR y sanitiza sistemáticamente todas las entradas. A nivel infraestructura, las credenciales se externalizan en .env y el stack Docker aísla servicios.