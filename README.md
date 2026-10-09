# Sistema de Gestión de Farmacia (FarmaPlus)

Sistema desarrollado en PHP Nativo y MySQL siguiendo los requerimientos para la entrega del proyecto de la asignatura.

---

## 📌 1. Estructura de la Aplicación y Cumplimiento de Requerimientos

- **A: Cabecera (Header):**
  - Ubicada en [`header.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/header.php).
  - Contiene el logotipo institucional estilo texto ("+ FarmaPlus") **sin imágenes de logos**, conforme a las instrucciones.
  - Visible en todas las páginas mediante `require_once 'header.php'`.

- **B: Pie de Página (Footer):**
  - Ubicado en [`footer.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/footer.php).
  - Incluye derechos reservados, año actual dinámico (`<?= date('Y') ?>`) e información de la desarrolladora **Ashley Moreta**.

- **C: Menú de Selección o Navegación:**
  - Integrado en la cabecera [`header.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/header.php).
  - Enlaces activos a: **Inicio**, **Inventario**, **Dispensación**, **Seguros Médicos** y **Usuarios** (exclusivo Admin).

- **D: Login / Control de Acceso según Roles:**
  - [`login.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/login.php) con formulario de usuario y contraseña.
  - Manejo de contraseñas seguras con `password_hash()` y `password_verify()`.
  - **Mínimo 2 Roles implementados:**
    1. **Administrador (`admin`):** Acceso total a todos los módulos y gestión de usuarios.
    2. **Farmacéutico (`farmaceutico`):** Acceso a inventario, dispensación y seguros.

- **E: Módulos del Sistema (Lógica de Negocio):**
  1. **Módulo de Inventario (`inventario.php`):** Registro de medicamentos, stock, laboratorio, control de venta bajo receta.
  2. **Módulo de Seguros Médicos (`seguros.php`):** Gestión de obras sociales / entidades de medicina prepaga y porcentaje de descuento/cobertura.
  3. **Módulo de Dispensación (`dispensacion.php`):** Entrega de medicamentos con cálculo automático de cobertura y actualización de stock en tiempo real.
  4. **Módulo de Usuarios (`usuarios.php`):** Registro de personal con asignación de roles.

---

## 🛠️ 2. Instrucciones para DBeaver y MySQL Local (XAMPP)

1. Abre **DBeaver** o **phpMyAdmin**.
2. Crea una nueva conexión MySQL local (`localhost:3306`, usuario `root`, sin contraseña por defecto en XAMPP).
3. Ejecuta el archivo de script SQL: [`schema.sql`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/schema.sql).
4. El script creará la base de datos `farmaplus` con todas sus tablas y datos semilla precargados.

### 🔑 Credenciales para Pruebas:
- **Rol Administrador:**
  - Usuario: `admin`
  - Contraseña: `123456`
- **Rol Farmacéutico:**
  - Usuario: `farmaceutico`
  - Contraseña: `123456`

---

## 🚀 3. Instrucciones de Despliegue en Railway

Para desplegar este proyecto en **Railway**:

1. Crear un proyecto en Railway y agregar un servicio **MySQL Database**.
2. En DBeaver, conectar a la base de datos de Railway utilizando los datos brindados por Railway (Host, Port, User, Password, Database).
3. Importar y ejecutar el archivo [`schema.sql`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/schema.sql) en la base de datos de Railway.
4. Conectar tu repositorio de GitHub conteniendo este código al servicio web PHP en Railway.
5. Railway inyectará automáticamente las variables de entorno (`MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`), las cuales son leídas dinámicamente en [`config.php`](file:///c:/xampp/htdocs/Ashley/FarmaPlus/config.php).
