# 🏥 FarmaPlus - Sistema de Gestión Farmacéutica

**FarmaPlus** es un sistema web de gestión de farmacia desarrollado en **PHP Nativo**, **HTML5**, **CSS3** y **MySQL**. El sistema permite controlar el acceso por roles, administrar el inventario de medicamentos, gestionar convenios con seguros médicos/obras sociales, procesar dispensaciones con cálculo de descuentos en tiempo real y registrar nuevos usuarios.

---

## 👩‍💻 Información del Proyecto
- **Desarrolladora:** Ashley Moreta
- **Tecnologías:** PHP 8.x, MySQL 5.7+ / 8.0+, HTML5, CSS3, PDO.
- **Herramientas recomendadas:** XAMPP, DBeaver, VS Code.
- **Plataforma de Despliegue:** Railway / Servidores Apache - Nginx.
- **Repositorio oficial:** [https://github.com/hanonimus63-dot/FarmaPlus.git](https://github.com/hanonimus63-dot/FarmaPlus.git)

---

## 📑 Índice de Contenidos
1. [Requerimientos de la Aplicación](#1-requerimientos-de-la-aplicación)
2. [Estructura del Proyecto](#2-estructura-del-proyecto)
3. [Módulos y Funcionalidades](#3-módulos-y-funcionalidades)
4. [Configuración de Base de Datos (DBeaver / XAMPP)](#4-configuración-de-base-de-datos-dbeaver--xampp)
5. [Credenciales para Pruebas](#5-credenciales-para-pruebas)
6. [Despliegue en Railway](#6-despliegue-en-railway)

---

## 1. Requerimientos de la Aplicación

| Requerimiento | Descripción | Archivo / Componente |
| :--- | :--- | :--- |
| **A: Cabecera (Header)** | Logotipo institucional estilo texto `+ FarmaPlus` (sin imágenes de logos) y menú de navegación visible en todas las páginas. | [`header.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/header.php) |
| **B: Pie de Página (Footer)** | Información del desarrollador (Ashley Moreta), año dinámico y derechos de autor visible en todas las páginas. | [`footer.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/footer.php) |
| **C: Menú de Selección** | Navegación entre módulos (Inicio, Inventario, Dispensación, Seguros, Usuarios). | [`header.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/header.php) |
| **D: Login y Control de Acceso** | Acceso protegido con validación de credenciales encriptadas (`password_hash`) y mínimo 2 roles (`admin` y `farmaceutico`). | [`login.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/login.php) |
| **E: Módulos del Sistema** | Inventario, Seguros Médicos, Dispensación de Medicamentos y Registro de Usuarios. | `inventario.php`, `seguros.php`, `dispensacion.php`, `registro.php`, `usuarios.php` |

---

## 2. Estructura del Proyecto

```text
FarmaPlus/
├── config.php          # Conexión PDO a MySQL (Soporta Railway y XAMPP/DBeaver)
├── schema.sql          # Script SQL para creación de tablas y datos semilla
├── login.php           # Formulario de inicio de sesión
├── registro.php        # Formulario público de registro de usuarios
├── logout.php          # Cierre de sesión seguro
├── header.php          # Cabecera universal y menú de navegación
├── footer.php          # Pie de página universal
├── index.php           # Dashboard principal con estadísticas del sistema
├── inventario.php      # Módulo de administración de medicamentos y stock
├── seguros.php         # Módulo de seguros médicos y obras sociales
├── dispensacion.php    # Módulo de dispensación y venta con receta/descuentos
├── usuarios.php        # Módulo de control de usuarios (Exclusivo Administrador)
├── css/
│   └── estilos.css     # Hoja de estilos global
├── composer.json       # Configuración para despliegue en servidor Railway
├── nixpacks.toml       # Configuración del motor de construcción en Railway
└── README.md           # Documentación general del proyecto
```

---

## 3. Módulos y Funcionalidades

### 🔐 3.1. Autenticación y Registro (`login.php` y `registro.php`)
- **Login:** Formulario de acceso protegido contra inyecciones SQL mediante consultas preparadas PDO.
- **Registro:** Permite crear nuevos usuarios asignando su rol (`farmaceutico` o `admin`).
- **Seguridad:** Encriptación de contraseñas con el algoritmo estándar BCRYPT (`password_hash`).

### 📊 3.2. Panel Principal / Dashboard (`index.php`)
- Resumen visual con tarjetas informativas:
  - Total de medicamentos registrados.
  - Alertas automáticas de productos con stock bajo (<= 15 unidades).
  - Cantidad de seguros médicos activos.
  - Total de dispensaciones realizadas.

### 💊 3.3. Módulo de Inventario (`inventario.php`)
- Registro de medicamentos indicando código de barras, nombre, laboratorio, precio, stock inicial y si requiere receta médica.
- Listado interactivo con resaltado de alertas de stock en color rojo.
- Opción de eliminación de productos exclusiva para el rol **Administrador**.

### 🏥 3.4. Módulo de Seguros Médicos (`seguros.php`)
- Alta de convenios con entidades de medicina prepaga u obras sociales (OSDE, Swiss Medical, Medifé, etc.).
- Configuración del porcentaje de descuento / cobertura sobre los medicamentos.

### 🧾 3.5. Módulo de Dispensación (`dispensacion.php`)
- Registro de recetas y salida de medicamentos a pacientes.
- Selección del seguro médico del paciente con cálculo automático del monto de cobertura y total a pagar.
- Actualización automática del stock en base de datos mediante **transacciones PDO** para prevenir fallos.
- Historial de dispensaciones realizadas con detalle de folios y atendidos.

---

## 4. Configuración de Base de Datos (DBeaver / XAMPP)

1. Abre **DBeaver** o **phpMyAdmin**.
2. Conéctate a tu servidor MySQL local (`localhost:3306`, usuario `root`).
3. Ejecuta el archivo [`schema.sql`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/schema.sql) adjunto en el proyecto.
4. El script creará automáticamente la base de datos `farmaplus` con todas las tablas e insertará los datos iniciales de prueba.

---

## 5. Credenciales para Pruebas

| Rol | Usuario | Contraseña | Permisos |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin` | `123456` | Acceso total a todos los módulos y gestión de usuarios. |
| **Farmacéutico** | `farmaceutico` | `123456` | Acceso a Inventario, Dispensación y Seguros Médicos. |

---

## 6. Despliegue en Railway

El archivo [`config.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/config.php) detecta automáticamente las variables de entorno inyectadas por Railway:
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`

Para desplegar en Railway:
1. Conecta el repositorio de GitHub `https://github.com/hanonimus63-dot/FarmaPlus.git` a un nuevo servicio en Railway.
2. Agrega una base de datos MySQL en Railway y ejecuta el script [`schema.sql`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/schema.sql) mediante DBeaver conectándote a los datos provistos por Railway.
