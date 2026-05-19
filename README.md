# RLCS CRM

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-4.4-FF6384?style=flat&logo=chartdotjs&logoColor=white)
![License](https://img.shields.io/badge/Licencia-MIT-green?style=flat)

**CRM deportivo para la gestión de la Rocket League Championship Series (RLCS)**

> Proyecto 1º ASIR · Aplicaciones Web / Base de datos

---

## Descripción

RLCS CRM es una aplicación web completa para gestionar datos de la competición profesional de Rocket League. Permite administrar equipos, jugadores, torneos, partidos y clasificaciones de toda la RLCS desde una interfaz moderna con temática esports.

Construido con **PHP puro + MySQLi**, **Bootstrap 5.3** y un diseño oscuro inspirado en el mundo de los esports. Sin frameworks PHP, sin ORMs — fácil de desplegar en cualquier servidor XAMPP o LAMP.

La base de datos incluye **datos reales precargados** de múltiples temporadas RLCS (2021-2025): 6 regiones, 48 equipos, 144 jugadores, 30 torneos y más de 200 partidos con estadísticas completas.

---

## Características

### Autenticación y roles
- Login y registro con email + contraseña cifrada (`password_hash` / `password_verify`)
- Gestión de sesiones segura con `session_regenerate_id(true)` en cada login
- Tres roles: **admin**, **editor** y **viewer** — cada página verifica el rol mínimo requerido
- Notificaciones tipo **toast** (bottom-right) en lugar de alerts tras acciones POST/PRG

### Panel principal (Dashboard)
- Tarjetas KPI: total de equipos, jugadores, torneos y partidos registrados
- Top 5 de la clasificación RLCS de la temporada más reciente
- Últimos 5 partidos disputados con resultado

### Equipos
- Listado con búsqueda en tiempo real y filtro por región (Bootstrap JS)
- **Ficha de equipo**: roster actual, historial de torneos con posición, puntos por temporada con gráfica de barras/línea (Chart.js), borde superior en el color del equipo
- Crear y editar: **color picker** de equipo, URL de logo (imagen en cabecera con fallback `onerror`)
- Paginación de 10 en 10

### Jugadores
- Listado con estadísticas totales acumuladas: goles, asistencias, salvadas, tiros y MVPs
- **Ficha de jugador**: stats acumuladas, historial de equipos (roster), avatar circular si hay URL de foto
- Crear, editar y transferir jugadores entre equipos (procedimiento almacenado `transferir_jugador`)
- Filtro por país; página de agentes libres (jugadores sin roster activo)

### Comparador H2H
- **Modo equipos**: historial de partidos directos, marcador de victorias y stats globales enfrentadas
- **Modo jugadores**: estadísticas lado a lado (goles, asistencias, salvadas, MVPs)
- Selector dual con búsqueda

### Clasificación RLCS
- Tabla de puntos por temporada y región (filtros con redirección GET)
- Top 3 destacado con colores oro / plata / bronce
- Columnas: posición, equipo, tag, región, regionals, majors, total

### Partidos y torneos
- Listado de partidos con resultado, filtro por torneo y ganador destacado en verde
- **Registrar partido desde cero**: selector de torneo, dos equipos, formato Bo1/Bo3/Bo5/Bo7
  - Marcador en vivo por juego (añadir/eliminar juegos dinámicamente con JS)
  - Validación automática de series: detecta ganador según el formato y bloquea nuevos juegos
  - Estadísticas por jugador: goles, asistencias, salvadas, tiros y MVP por equipo
  - Un único submit crea PARTIDO + todos los JUEGOs + todas las ESTADISTICAS_JUGADOR
- Listado de torneos con filtro por tipo y temporada
- **Ficha de torneo**: equipos participantes con posición, partidos del torneo

### Bracket visual de torneos (Admin)
- Selección de torneo → bracket generado automáticamente desde los datos de PARTIDO
- Columnas de rondas: Cuartos de Final → Semifinal → Gran Final (adapta al número de partidos)
- Tarjetas de enfrentamiento con marcador de juegos ganados, ganador resaltado en verde y perdedor atenuado
- **Líneas SVG de conexión** dibujadas por JavaScript entre rondas (árbol de bracket real)
- Tabla de resultados detallada debajo del bracket

### Panel de administración
Accesible desde `/pages/admin/index.php` (solo admin):

| Sección | Descripción |
|---|---|
| Usuarios | Listar, crear, editar y cambiar rol de usuarios del CRM |
| Equipos | Crear y editar equipos con color picker y URL de logo |
| Torneos | Crear y editar torneos con tipo y prize pool |
| Partidos | Listar todos los partidos; crear partido base (sin juegos) |
| Regiones | Ver y editar las regiones de la RLCS |
| Temporadas | Crear y gestionar temporadas por año |
| Roster | Asignar/liberar jugadores a equipos con fechas |
| Puntos RLCS | Gestionar puntos de regionals y majors por temporada |
| Participación | Vincular equipos a torneos con posición final |
| Bracket | Visualización gráfica del bracket de cada torneo |
| Estadísticas | Entrada manual de estadísticas por jugador/partido |
| Importar | Carga masiva de partidos y estadísticas desde CSV o JSON |
| Auditoría | Log de acciones registradas (INSERT/UPDATE/DELETE) |

### Búsqueda global
- Barra en el navbar → resultados en `/pages/busqueda.php`
- Agrupados por sección: equipos, jugadores, torneos

### Diseño UI/UX
- Tema oscuro esports: fondo `#0f0f0f` + acento azul eléctrico `#00d4ff`
- Toasts Bootstrap 5 en bottom-right (éxito en verde, error en rojo), autocierre a los 4 s
- Skeleton shimmer en tablas al enviar formularios de filtro
- `<title>` dinámico por página (ej: `"Team Vitality — Equipo | RLCS CRM"`)
- Favicon SVG inline (emoji 🎮)
- Logos de equipo con `onerror` fallback; avatar circular de jugador
- Totalmente responsive con Bootstrap 5 Grid

---

## Tecnologías

| Tecnología | Versión | Uso |
|---|---|---|
| PHP | 8.0+ | Backend y lógica de negocio |
| MySQL / MySQLi | 5.7+ | Base de datos relacional |
| Bootstrap | 5.3 | Interfaz responsive |
| Bootstrap Icons | 1.10 | Iconografía |
| Chart.js | 4.4 | Gráfica de puntos por temporada |
| JavaScript (ES6) | — | Bracket, marcador en vivo, búsqueda en tiempo real, toasts, skeleton |
| HTML5 / CSS3 | — | Estructura y estilos personalizados |
| SVG | — | Líneas de conexión del bracket |

---

## Requisitos

- **XAMPP** (Windows/macOS) o **LAMP** (Linux): Apache + PHP + MySQL
- PHP **8.0 o superior**
- MySQL **5.7 o superior**
- Navegador moderno (Chrome, Firefox, Edge)

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Pawored/rl-crm.git
```

Coloca la carpeta dentro de `htdocs` (XAMPP) o `/var/www/html` (LAMP) con la ruta `/RLCS/CRM/`:

```
C:/xampp/htdocs/RLCS/CRM/   (Windows XAMPP)
/var/www/html/RLCS/CRM/      (Linux LAMP)
```

### 2. Iniciar los servicios

Arranca **Apache** y **MySQL** desde el panel de XAMPP o con `systemctl` en Linux.

### 3. Crear la base de datos y el esquema

```bash
mysql -u root rlcs < sql/00_schema/estructura.sql
```

Esto crea la base de datos `rlcs` con las 13 tablas, relaciones y el esquema completo.

### 4. Aplicar migraciones visuales (columnas de logo y foto)

```bash
mysql -u root rlcs < sql/00_schema/alter_visual.sql
```

Añade `color_primario` y `logo_url` a `EQUIPO`, y `foto_url` a `JUGADOR`.

### 5. Crear las vistas y procedimientos almacenados

```bash
mysql -u root rlcs < sql/01_views/view_01_rosters_actuales.sql
mysql -u root rlcs < sql/01_views/view_02_clasificacion.sql
mysql -u root rlcs < sql/01_views/view_03_stats_totales.sql
mysql -u root rlcs < sql/03_procedures/procedimientos.sql
mysql -u root rlcs < sql/00_schema/auditoria.sql
```

### 6. Crear la tabla de usuarios e insertar el admin por defecto

```bash
mysql -u root rlcs < sql/crear_usuarios.sql
```

### 7. (Opcional) Cargar todos los datos de ejemplo

```bash
# Desde el cliente MySQL con SOURCE, dentro del directorio sql/
mysql -u root rlcs
mysql> SOURCE todos_los_inserts.sql;
```

Carga: 6 regiones, 48 equipos, 144 jugadores, 144 entradas de roster, 6 temporadas, 30 torneos, participaciones, puntos RLCS, usuarios de muestra y más de 200 partidos con juegos y estadísticas.

### 8. Configurar la conexión

Edita `config/conexion.php` si tu configuración MySQL difiere de la de XAMPP por defecto:

```php
define('DB_HOST',     'localhost');
define('DB_USUARIO',  'root');
define('DB_PASSWORD', '');          // Vacía en XAMPP
define('DB_NOMBRE',   'rlcs');
```

### 9. Abrir en el navegador

```
http://localhost/RLCS/CRM/
```

La raíz redirige automáticamente al login si no hay sesión activa, o al dashboard si la hay.

---

## Credenciales por defecto

| Campo | Valor |
|---|---|
| Email | `admin@rlcs.com` |
| Contraseña | `password` |
| Rol | `admin` |

> Cambia la contraseña del administrador tras el primer acceso.

---

## Base de datos

### Tablas

| Tabla | Descripción |
|---|---|
| `REGION` | Regiones de la RLCS: NA, EU, SAM, MENA, OCE, SSKC |
| `EQUIPO` | Equipos con nombre, tag, región, color y logo |
| `JUGADOR` | Jugadores con nickname, nombre real, país y foto |
| `ROSTER` | Historial de plantillas (jugador ↔ equipo con fechas) |
| `TEMPORADA` | Temporadas (2021-2025) con año y prize pool total |
| `TORNEO` | Torneos con nombre, tipo (Regional/Major/World Championship) y prize pool |
| `PARTICIPACION` | Equipos inscritos en cada torneo con posición final y puntos ganados |
| `PARTIDO` | Enfrentamientos con equipos, ganador, fecha y formato (Bo1-Bo7) |
| `JUEGO` | Juegos individuales de un partido: goles por equipo y duración |
| `ESTADISTICAS_JUGADOR` | Stats por jugador por partido: goles, asistencias, salvadas, tiros, MVP |
| `PUNTOS_RLCS` | Puntos de regionals y majors por equipo por temporada |
| `BRACKET` | Metadatos de bracket (tipo, ronda, fase) por torneo |
| `USUARIOS` | Usuarios del CRM con email, contraseña hash y rol |
| `AUDITORIA` | Log automático de cambios en tablas principales |

### Vistas

| Vista | Descripción |
|---|---|
| `vista_rosters_actuales` | Roster activo (sin fecha_fin) de cada equipo con id_equipo e id_jugador |
| `vista_clasificacion` | Clasificación general por puntos RLCS totales |
| `vista_stats_totales` | Estadísticas totales acumuladas por jugador (goles, asistencias, salvadas…) |

### Procedimientos almacenados

| Procedimiento | Descripción |
|---|---|
| `transferir_jugador` | Cierra el roster actual del jugador y abre uno nuevo en el equipo destino |
| `registrar_resultado_partido` | Asigna el ganador a un PARTIDO por ID |
| `liberar_jugador` | Cierra el roster activo sin asignar nuevo equipo |
| `calcular_puntos_temporada` | Recalcula los puntos totales de un equipo en una temporada |
| `obtener_forma_reciente` | Últimos N resultados (V/D) de un equipo |
| `clasificacion_temporada` | Clasificación completa de una temporada con filtro de región |
| `jugadores_agentes_libres` | Lista de jugadores sin roster activo |
| `historial_h2h` | Historial de enfrentamientos directos entre dos equipos |
| `top_goleadores` | Ranking de jugadores por goles totales |
| `equipos_por_region` | Equipos activos agrupados por región |
| `resumen_torneo` | Estadísticas agregadas de un torneo |
| `estadisticas_equipo` | Stats totales de todos los jugadores de un equipo |
| `actualizar_estado_jugador` | Cambia el campo `activo` de un jugador |
| `limpiar_auditoria_antigua` | Elimina registros de auditoría más antiguos de N días |
| `bracket_torneo` | Devuelve los partidos de un torneo agrupados por ronda |

### Archivos SQL

```
sql/
├── crear_usuarios.sql          # Crea USUARIOS e inserta el admin por defecto
├── todos_los_inserts.sql       # Master file: ejecuta los 11 archivos de inserts en orden
├── 00_schema/
│   ├── estructura.sql          # Esquema completo (13 tablas + FK)
│   ├── alter_visual.sql        # Migraciones: color_primario, logo_url, foto_url
│   └── auditoria.sql           # Tabla AUDITORIA y triggers
├── 01_views/
│   ├── view_01_rosters_actuales.sql
│   ├── view_02_clasificacion.sql
│   └── view_03_stats_totales.sql
├── 02_inserts/
│   ├── 01_regiones.sql         # 6 regiones
│   ├── 02_equipos.sql          # 48 equipos (8 por región)
│   ├── 03_jugadores.sql        # 144 jugadores (3 por equipo)
│   ├── 04_roster.sql           # 144 entradas de roster
│   ├── 05_temporadas.sql       # Temporadas 2021-2025
│   ├── 06_torneos.sql          # 30 torneos (regionals, majors, mundiales)
│   ├── 07_participacion.sql    # Participaciones de equipos en torneos
│   ├── 08_puntos_rlcs.sql      # Puntos por equipo y temporada
│   ├── 09_usuarios.sql         # Usuarios de muestra
│   ├── 10_partidos.sql         # Partidos, juegos y stats (torneos 1-2, 7, 9-10)
│   └── 11_partidos_resto.sql   # Partidos, juegos y stats (25 torneos restantes)
└── 03_procedures/
    └── procedimientos.sql      # 15 procedimientos almacenados
```

---

## Estructura de carpetas

```
RLCS/CRM/
├── config/
│   └── conexion.php                    # Conexión MySQLi y configuración de errores
├── includes/
│   ├── header.php                      # Navbar con búsqueda, toasts, título dinámico
│   ├── footer.php                      # Bootstrap JS, init de toasts, skeleton trigger
│   ├── sesion.php                      # requiereRol(), tieneRol(), estaAutenticado()
│   ├── db.php                          # Helpers prepared statements (db_run, db_fetch_all, db_fetch_one)
│   └── auditoria.php                   # Helper para registrar en AUDITORIA
├── auth/
│   ├── login.php
│   ├── registro.php
│   └── logout.php
├── pages/
│   ├── dashboard.php                   # KPIs, clasificación top 5, últimos partidos
│   ├── busqueda.php                    # Búsqueda global (equipos, jugadores, torneos)
│   ├── comparar.php                    # Comparador H2H: equipos y jugadores
│   ├── agentes_libres.php              # Jugadores sin roster activo
│   ├── clasificacion/
│   │   └── index.php                   # Tabla de puntos RLCS con filtros
│   ├── equipos/
│   │   ├── index.php                   # Listado con buscador en tiempo real
│   │   ├── detalle.php                 # Ficha: roster, historial, gráfica Chart.js
│   │   └── editar.php                  # Crear / editar (color picker + logo URL)
│   ├── jugadores/
│   │   ├── index.php                   # Listado con stats totales
│   │   ├── detalle.php                 # Ficha: stats, historial de equipos, avatar
│   │   └── editar.php                  # Crear / editar / transferir (foto URL)
│   ├── partidos/
│   │   ├── index.php                   # Listado con filtro por torneo
│   │   └── registrar.php               # Flujo unificado: partido + juegos + stats en un submit
│   ├── torneos/
│   │   ├── index.php                   # Listado con filtros
│   │   └── detalle.php                 # Ficha: equipos, partidos, bracket
│   └── admin/
│       ├── index.php                   # Panel de administración con módulos
│       ├── usuarios.php                # Gestión de usuarios del CRM
│       ├── auditoria/index.php         # Log de auditoría con filtros
│       ├── bracket/gestionar.php       # Bracket visual por torneo (SVG + JS)
│       ├── estadisticas/entrada.php    # Entrada manual de estadísticas
│       ├── importar/index.php          # Importación masiva CSV / JSON
│       ├── participacion/gestionar.php # Vincular equipos a torneos
│       ├── partidos/crear.php          # Crear partido base
│       ├── puntos/gestionar.php        # Puntos RLCS por equipo y temporada
│       ├── regiones/editar.php         # Editar regiones
│       ├── roster/gestionar.php        # Asignar / liberar jugadores
│       ├── temporadas/editar.php       # Crear / editar temporadas
│       └── torneos/editar.php          # Crear / editar torneos
├── css/
│   └── estilos.css                     # Tema oscuro, .bg-card, .bg-accent, skeleton shimmer
├── sql/                                # (ver sección Base de datos)
├── logs/
│   └── errores.log                     # Errores PHP (nunca mostrados en pantalla)
└── index.php                           # Punto de entrada → redirige a login o dashboard
```

---

## Páginas y acceso

### Públicas (sin sesión)

| URL | Descripción |
|---|---|
| `/auth/login.php` | Inicio de sesión |
| `/auth/registro.php` | Registro de nuevo usuario |

### Usuarios autenticados (todos los roles)

| URL | Descripción |
|---|---|
| `/pages/dashboard.php` | Panel principal con KPIs |
| `/pages/equipos/index.php` | Listado de equipos |
| `/pages/equipos/detalle.php?id=X` | Ficha de equipo con gráfica Chart.js |
| `/pages/jugadores/index.php` | Listado de jugadores con stats |
| `/pages/jugadores/detalle.php?id=X` | Ficha de jugador |
| `/pages/clasificacion/index.php` | Clasificación RLCS |
| `/pages/partidos/index.php` | Listado de partidos |
| `/pages/torneos/index.php` | Listado de torneos |
| `/pages/torneos/detalle.php?id=X` | Ficha de torneo |
| `/pages/busqueda.php?q=texto` | Búsqueda global |
| `/pages/comparar.php` | Comparador H2H (equipos y jugadores) |
| `/pages/agentes_libres.php` | Jugadores sin equipo |

### Admin y editor

| URL | Descripción |
|---|---|
| `/pages/equipos/editar.php` | Crear equipo |
| `/pages/equipos/editar.php?id=X` | Editar equipo (color, logo) |
| `/pages/jugadores/editar.php` | Crear jugador |
| `/pages/jugadores/editar.php?id=X` | Editar / transferir jugador (foto) |
| `/pages/partidos/registrar.php` | Registrar partido completo (juegos + stats) |

### Solo admin

| URL | Descripción |
|---|---|
| `/pages/admin/index.php` | Panel de administración |
| `/pages/admin/usuarios.php` | Gestión de usuarios |
| `/pages/admin/bracket/gestionar.php` | Bracket visual de torneos |
| `/pages/admin/auditoria/index.php` | Log de auditoría |
| `/pages/admin/importar/index.php` | Importación masiva CSV/JSON |
| `/pages/admin/partidos/crear.php` | Crear partido base |
| `/pages/admin/torneos/editar.php` | Crear / editar torneos |
| `/pages/admin/regiones/editar.php` | Editar regiones |
| `/pages/admin/temporadas/editar.php` | Crear / editar temporadas |
| `/pages/admin/roster/gestionar.php` | Gestionar roster |
| `/pages/admin/puntos/gestionar.php` | Gestionar puntos RLCS |
| `/pages/admin/participacion/gestionar.php` | Gestionar participaciones |
| `/pages/admin/estadisticas/entrada.php` | Entrada manual de stats |

---

## Sistema de roles

| Permiso | admin | editor | viewer |
|---|:---:|:---:|:---:|
| Ver cualquier dato | ✅ | ✅ | ✅ |
| Crear registros | ✅ | ✅ | ❌ |
| Editar registros | ✅ | ✅ | ❌ |
| Registrar partidos | ✅ | ✅ | ❌ |
| Eliminar registros | ✅ | ❌ | ❌ |
| Gestionar usuarios | ✅ | ❌ | ❌ |
| Panel de administración | ✅ | ❌ | ❌ |

---

## Seguridad

- **Prepared statements** en todos los accesos a datos via `includes/db.php` (`db_run`, `db_fetch_all`, `db_fetch_one`)
- **`password_hash()`** con bcrypt; verificación con `password_verify()`
- **`session_regenerate_id(true)`** al iniciar sesión (previene fijación de sesión)
- **`session_destroy()`** completo al cerrar sesión
- **`htmlspecialchars()`** en todos los outputs HTML (previene XSS)
- **Validación de color** con regex `/^#[0-9a-fA-F]{6}$/` antes de persistir
- **Sanitización de URLs** con `substr(trim(...), 0, 500)`
- **PRG pattern** (Post/Redirect/Get) en todos los formularios POST para evitar reenvíos
- Errores PHP redirigidos a `logs/errores.log`; `display_errors = 0` en producción
- `requiereRol()` en cada página protegida — redirige al dashboard si el rol es insuficiente

---

## Datos precargados

| Entidad | Cantidad |
|---|---|
| Regiones | 6 (NA, EU, SAM, MENA, OCE, SSKC) |
| Equipos | 48 (8 por región) |
| Jugadores | 144 (3 por equipo) |
| Temporadas | 6 (2021 – 2025) |
| Torneos | 30 (Regionals, Majors, World Championships) |
| Partidos | 200+ con formato Bo5/Bo7 |
| Juegos | 900+ (resultados por juego) |
| Estadísticas de jugador | 1 200+ entradas por partido |

---

## Autor

Proyecto desarrollado como trabajo de fin de curso para el módulo de **Aplicaciones Web** en **1º ASIR**.

- Temática basada en la **Rocket League Championship Series (RLCS)**
- Construido íntegramente con PHP 8, MySQL y Bootstrap 5.3
- Sin frameworks PHP ni ORMs — solo PHP procedimental + MySQLi con prepared statements

---

*RLCS CRM no está afiliado oficialmente con Psyonix, Epic Games ni la RLCS.*
