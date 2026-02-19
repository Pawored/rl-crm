# RLCS CRM

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/Licencia-MIT-green?style=flat)

**CRM deportivo para la gestión de la Rocket League Championship Series (RLCS)**

> Proyecto de fin de curso — 1º ASIR · Aplicaciones Web

---

## Descripción

RLCS CRM es una aplicación web para gestionar datos de la competición profesional de Rocket League. Permite administrar equipos, jugadores, torneos, partidos y clasificaciones de la RLCS desde una interfaz moderna con temática esports.

El proyecto está construido con **PHP puro + MySQLi**, **Bootstrap 5** y un diseño oscuro inspirado en el mundo de los esports. No usa ningún framework PHP, lo que lo hace fácil de entender y desplegar en cualquier servidor con XAMPP o LAMP.

---

## Características

### Autenticación y usuarios
- Registro e inicio de sesión con email y contraseña
- Contraseñas cifradas con `password_hash()` / `password_verify()`
- Gestión de sesiones segura con `session_regenerate_id()`
- Sistema de roles: **admin**, **editor** y **viewer**
- Panel de administración de usuarios (solo admin)

### Equipos
- Listado de equipos con buscador en tiempo real y filtro por región
- Ficha de equipo: roster actual, historial de torneos y puntos por temporada
- Crear y editar equipos (admin y editor)
- Paginación de 10 en 10

### Jugadores
- Listado con estadísticas totales: goles, asistencias, salvadas, MVPs
- Ficha de jugador: stats acumuladas y historial de equipos
- Crear, editar y transferir jugadores entre equipos (procedimiento almacenado)
- Filtro por país

### Clasificación
- Tabla de puntos RLCS por temporada y región
- Posiciones numeradas con colores especiales para el top 3 (oro, plata, bronce)
- Columnas: posición, equipo, tag, región, puntos regionals, puntos majors, total

### Partidos y torneos
- Listado de partidos con resultado, ganador destacado en verde y filtro por torneo
- Registro de resultados mediante procedimiento almacenado
- Añadir juegos individuales (goles y duración)
- Listado de torneos con filtro por tipo y temporada
- Ficha de torneo: equipos participantes, partidos y bracket

### Búsqueda global
- Barra de búsqueda en la barra de navegación
- Resultados agrupados por sección: equipos, jugadores y torneos

### Diseño
- Tema oscuro estilo esports (`#0f0f0f` + acento azul eléctrico `#00d4ff`)
- Responsive con Bootstrap 5
- Confirmación con modal de Bootstrap antes de acciones críticas

---

## Tecnologías

| Tecnología | Versión | Uso |
|---|---|---|
| PHP | 7.4+ | Backend y lógica de negocio |
| MySQL / MySQLi | 5.7+ | Base de datos relacional |
| Bootstrap | 5.3.0 | Interfaz responsive |
| Bootstrap Icons | 1.10.0 | Iconografía |
| HTML5 / CSS3 | — | Estructura y estilos personalizados |
| JavaScript | ES6 | Buscadores en tiempo real y modales |
| XAMPP / LAMP | — | Entorno de desarrollo local |

---

## Requisitos previos

- **XAMPP** (Windows/macOS) o **LAMP** (Linux) con Apache + PHP + MySQL
- PHP **7.4 o superior**
- MySQL **5.7 o superior**
- Navegador web moderno (Chrome, Firefox, Edge)
- La base de datos **RLCS** ya creada con todas sus tablas, vistas y procedimientos

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Pawored/rl-crm.git
cd rl-crm
```

Coloca la carpeta dentro de `htdocs` (XAMPP) o `/var/www/html` (LAMP):

```bash
# Ejemplo en XAMPP Windows
cp -r rl-crm C:/xampp/htdocs/RLCS_CRM
```

### 2. Iniciar los servicios

Arranca **Apache** y **MySQL** desde el panel de control de XAMPP (o con `systemctl` en Linux).

### 3. Importar la base de datos RLCS

Importa el dump de la base de datos RLCS desde phpMyAdmin o la terminal:

```bash
mysql -u root -p rlcs < ruta/al/dump_rlcs.sql
```

> La base de datos debe llamarse `rlcs` y tener todas las tablas descritas en la sección [Base de datos](#base-de-datos).

### 4. Crear la tabla de usuarios

Ejecuta el script SQL incluido en el proyecto:

```bash
mysql -u root rlcs < sql/crear_usuarios.sql
```

Esto crea la tabla `USUARIOS` e inserta un **administrador por defecto**.

### 5. Configurar la conexión

Edita `config/conexion.php` si tu configuración MySQL es distinta a la de XAMPP por defecto:

```php
define('DB_HOST',     'localhost');  // Servidor MySQL
define('DB_USUARIO',  'root');       // Usuario MySQL
define('DB_PASSWORD', '');           // Contraseña (vacía en XAMPP)
define('DB_NOMBRE',   'rlcs');       // Nombre de la base de datos
```

### 6. Abrir en el navegador

```
http://localhost/RLCS_CRM/
```

La página principal redirige automáticamente al login.

---

## Credenciales por defecto

| Campo | Valor |
|---|---|
| Email | `admin@rlcs.com` |
| Contraseña | `password` |
| Rol | `admin` |

> **Importante:** Cambia la contraseña del administrador tras el primer acceso.

---

## Base de datos

### Tablas principales

| Tabla | Descripción |
|---|---|
| `REGION` | Regiones de la RLCS (NA, EU, MENA…) |
| `EQUIPO` | Equipos profesionales con tag y región |
| `JUGADOR` | Jugadores con datos personales |
| `ROSTER` | Historial de plantillas (qué jugador estuvo en qué equipo) |
| `TEMPORADA` | Temporadas de la RLCS con prize pool |
| `TORNEO` | Torneos (Regionals, Majors, Mundial) |
| `PARTICIPACION` | Equipos inscritos en cada torneo con posición final |
| `PARTIDO` | Enfrentamientos entre equipos |
| `JUEGO` | Juegos individuales dentro de cada partido |
| `ESTADISTICAS_JUGADOR` | Stats de cada jugador por partido |
| `PUNTOS_RLCS` | Puntos acumulados por equipo por temporada |
| `BRACKET` | Información del bracket de cada torneo |
| `USUARIOS` | Usuarios del CRM con roles |

### Vistas disponibles

| Vista | Descripción |
|---|---|
| `vista_rosters_actuales` | Roster actual de cada equipo |
| `vista_clasificacion` | Clasificación general por puntos RLCS |
| `vista_stats_totales` | Estadísticas totales acumuladas por jugador |

### Procedimientos almacenados

```sql
-- Registrar el resultado de un partido
CALL registrar_resultado_partido(id_partido, id_ganador);

-- Transferir un jugador a otro equipo
CALL transferir_jugador(id_jugador, id_equipo_nuevo, fecha);

-- Registrar la participación de un equipo en un torneo
CALL registrar_participacion(id_equipo, id_torneo);

-- Actualizar puntos de un equipo en una temporada
CALL actualizar_puntos_torneo(id_equipo, id_temporada, puntos, tipo);
```

---

## Estructura de carpetas

```
RLCS_CRM/
├── config/
│   └── conexion.php            # Conexión a la base de datos
├── includes/
│   ├── header.php              # Navbar con búsqueda global
│   ├── footer.php              # Pie de página
│   └── sesion.php              # Control de sesión (redirige si no autenticado)
├── auth/
│   ├── login.php               # Formulario de inicio de sesión
│   ├── registro.php            # Registro de nuevo usuario
│   └── logout.php              # Cierre de sesión
├── pages/
│   ├── dashboard.php           # Panel principal con estadísticas
│   ├── busqueda.php            # Resultados de búsqueda global
│   ├── equipos/
│   │   ├── index.php           # Listado de equipos
│   │   ├── detalle.php         # Ficha de equipo (?id=X)
│   │   └── editar.php          # Crear / editar equipo (?id=X)
│   ├── jugadores/
│   │   ├── index.php           # Listado de jugadores con stats
│   │   ├── detalle.php         # Ficha de jugador (?id=X)
│   │   └── editar.php          # Crear / editar / transferir jugador
│   ├── clasificacion/
│   │   └── index.php           # Tabla de puntos RLCS
│   ├── partidos/
│   │   ├── index.php           # Listado de partidos
│   │   └── registrar.php       # Registrar resultado y juegos
│   ├── torneos/
│   │   ├── index.php           # Listado de torneos
│   │   └── detalle.php         # Ficha de torneo (?id=X)
│   └── admin/
│       └── usuarios.php        # Gestión de usuarios (solo admin)
├── css/
│   └── estilos.css             # Estilos personalizados (tema oscuro)
├── sql/
│   └── crear_usuarios.sql      # SQL para crear tabla USUARIOS
└── index.php                   # Punto de entrada (redirige a login o dashboard)
```

---

## Sistema de roles

| Permiso | admin | editor | viewer |
|---|:---:|:---:|:---:|
| Ver datos | ✅ | ✅ | ✅ |
| Crear registros | ✅ | ✅ | ❌ |
| Editar registros | ✅ | ✅ | ❌ |
| Eliminar registros | ✅ | ❌ | ❌ |
| Gestionar usuarios | ✅ | ❌ | ❌ |

Los badges de rol se muestran en la barra de navegación con colores:
- **admin** → rojo
- **editor** → amarillo
- **viewer** → azul

---

## Páginas del CRM

| URL | Acceso | Descripción |
|---|---|---|
| `/auth/login.php` | Pública | Inicio de sesión |
| `/auth/registro.php` | Pública | Registro de usuario |
| `/pages/dashboard.php` | Todos | Panel con estadísticas generales |
| `/pages/equipos/index.php` | Todos | Listado de equipos |
| `/pages/equipos/detalle.php?id=X` | Todos | Ficha de equipo |
| `/pages/equipos/editar.php` | Admin / Editor | Crear o editar equipo |
| `/pages/jugadores/index.php` | Todos | Listado de jugadores |
| `/pages/jugadores/detalle.php?id=X` | Todos | Ficha de jugador |
| `/pages/jugadores/editar.php` | Admin / Editor | Crear, editar o transferir jugador |
| `/pages/clasificacion/index.php` | Todos | Clasificación RLCS |
| `/pages/partidos/index.php` | Todos | Listado de partidos |
| `/pages/partidos/registrar.php` | Admin / Editor | Registrar resultado de partido |
| `/pages/torneos/index.php` | Todos | Listado de torneos |
| `/pages/torneos/detalle.php?id=X` | Todos | Ficha de torneo |
| `/pages/busqueda.php?q=texto` | Todos | Búsqueda global |
| `/pages/admin/usuarios.php` | Solo Admin | Gestión de usuarios |

---

## Seguridad

- Contraseñas almacenadas con `password_hash()` (bcrypt por defecto)
- Verificación con `password_verify()` en el login
- `mysqli_real_escape_string()` en todas las consultas SQL para prevenir inyección SQL
- `session_regenerate_id(true)` al iniciar sesión para prevenir fijación de sesión
- `session_destroy()` completo al cerrar sesión
- Comprobación de rol en cada página protegida
- Errores PHP redirigidos al log (`logs/errores.log`), nunca mostrados en pantalla
- Acceso denegado con redirección al dashboard si el rol no tiene permisos

---

## Capturas de pantalla

> Las capturas de pantalla se añadirán próximamente.

| Pantalla | Descripción |
|---|---|
| Login | Formulario centrado con diseño oscuro estilo esports |
| Dashboard | Tarjetas de resumen, top 5 clasificación y últimos partidos |
| Equipos | Tabla con buscador en tiempo real y filtro por región |
| Clasificación | Tabla de puntos con top 3 destacado en oro, plata y bronce |

---

## Autor

Proyecto desarrollado como trabajo de fin de curso para el módulo de **Aplicaciones Web** en **1º ASIR**.

- Temática basada en la **Rocket League Championship Series (RLCS)**
- Construido íntegramente con PHP, MySQL y Bootstrap 5
- Comentarios en español en todo el código fuente

---

*RLCS CRM no está afiliado oficialmente con Psyonix, Epic Games ni la RLCS.*
