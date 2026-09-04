# Crediweb

**Crediweb** es un sistema integral de gestión financiera y administrativa diseñado para empresas microfinancieras, cooperativas y entidades dedicadas al otorgamiento, administración y cobranza de préstamos a corto plazo (créditos diarios, comerciales y de ruta).

El sistema fue construido con un enfoque de alto rendimiento y robustez transaccional utilizando **Zend Framework 1 (PHP)**, motor de base de datos relacional **Firebird SQL**, y una interfaz de usuario rica y reactiva basada en **Mozilla XUL (XML User Interface Language)**.

---

## 🚀 Características Principales

### 💳 Gestión de Créditos y Cartera
- **Evaluación y Solicitudes**: Registro de expedientes socioeconómicos, actividades comerciales (códigos CIIU), capacidad de pago y líneas de crédito.
- **Simulación y Cronogramas**: Generación y recálculo automático de cronogramas de amortización diaria, semanal o mensual considerando feriados y días no laborables.
- **Cálculo de Impuestos y Cargos**: Desglose automático de capital, intereses compensatorios, moratorios, gastos administrativos, seguro, IGV e ITF.
- **Flujo de Aprobación y Desembolso**: Autorización jerárquica de préstamos, redefinición de cuotas y desembolso en efectivo.
- **Gestión de Clientes y Avales**: Registro de datos personales, historial crediticio interno, calificación de clientes y asignación de tarifas preferenciales.

### 📋 Cobranzas y Gestión de Ruta
- **Planillas Diarias de Cobranza**: Generación de hojas de ruta y listas de cobro para supervisores y gestores de campo.
- **Liquidación de Cobranza**: Registro y validación rápida de pagos recaudados en ruta con conciliación automática.
- **Control de Morosidad**: Seguimiento en tiempo real de cuotas vencidas, condonación o ajuste de moras y reclasificación de cartera en mora.

### 💵 Caja y Tesorería
- **Operaciones de Ventanilla**: Cobro de cuotas, emisión e impresión de recibos de pago y cancelación de créditos.
- **Arqueo y Cierre Diario**: Control de apertura, cierre de caja, registro de billetaje desglosado por denominación y control de efectivo sencillo.
- **Gastos y Egresos**: Comprobantes de pago a proveedores, egresos operativos y anulación controlada de recibos con registro en auditoría.

### ⚖️ Gestión Legal y Documentación Contractual
- Emisión automatizada de **Pagarés a la Orden**, contratos de crédito con garantías y formatos de solicitud de crédito listos para firma e impresión térmica/láser.
- Registro y seguimiento de expedientes derivados a cobranza judicial o extrajudicial.

### 📊 Reportes Gerenciales y Fiscales
- Reportes consolidados de **cartera activa**, capital colocado vs. recuperado y saldos pendientes.
- Reportes fiscales y tributarios detallados (liquidación de **ITF** e **IGV**).
- Tableros para analistas, supervisores, gerencia general y accionistas.

### 🏢 Integración con Centrales de Riesgo (Equifax / Infocorp)
- Generación automática de archivos planos mensuales en formato estándar para envío y reporte periódico a **Equifax / Infocorp**.

### 👥 Recursos Humanos y Control de Asistencia
- Registro de asistencia de personal, asignación de turnos, horarios laborales, control de permisos diarios y programación de vacaciones.

### 🔐 Seguridad y Auditoría
- **Control de Acceso Basado en Roles (RBAC)**: Gestión granular de permisos mediante listas de control de acceso (`Quipu_Acl`).
- **Bitácora Transaccional**: Registro en tiempo real de operaciones críticas de usuarios (`LG_AUDITOR` y `LG_BITACORA`).
- **Respaldo Automatizado**: Scripts integrados para backup en caliente de la base de datos Firebird (`gbak`) y del código fuente.

---

## 🛠️ Stack Tecnológico y Arquitectura

| Componente | Tecnología / Versión | Descripción |
| :--- | :--- | :--- |
| **Backend** | PHP 5.3+ | Arquitectura MVC modular sobre Zend Framework 1 |
| **Framework** | Zend Framework 1 (1.x) | Enrutamiento modular, Zend_Db, Zend_Auth, Zend_Session |
| **Librería Núcleo** | `Quipu` (`lib/Quipu/`) | Clases base de ACL, Controladores, Vistas, Bitácora y Db |
| **Base de Datos** | Firebird SQL 2.x (Dialecto 3) | Stored Procedures, Generadores, Triggers y UDFs (`fbudf`, `ib_udf`) |
| **Frontend** | Mozilla XUL + JavaScript | Interfaz de usuario tipo escritorio rica (XUL), Prototype y jQuery |
| **Reportes & Impresión** | CSS Print / DOMPDF | Formatos optimizados para tickets, planillas de cobranza y reportes |

---

## 📁 Estructura del Proyecto

```plaintext
crediweb/
├── app/                      # Lógica principal de la aplicación
│   ├── boot.php              # Inicializador / Bootstrap del sistema (Zend MVC)
│   ├── config/               # Archivos de configuración
│   │   └── config.ini        # Conexión DB, credenciales, sesiones y rutas
│   ├── layouts/              # Layout principal en Mozilla XUL
│   ├── models/               # Modelos de dominio y acceso a datos
│   └── modules/              # Módulos del sistema (MVC)
│       ├── asistencia/       # Control de personal, horarios y vacaciones
│       ├── auth/             # Autenticación, usuarios y roles
│       ├── credito/          # Núcleo: créditos, caja, cobranzas, reportes
│       ├── default/          # Módulo base: escritorio, backups, bitácora
│       ├── infocorp/         # Exportación periódica para Equifax/Infocorp
│       └── legal/            # Documentación y seguimiento legal
├── db/                       # Definición de esquema y scripts SQL
│   └── ddldump.sql           # DDL de tablas, dominios, generadores y UDFs
├── files/                    # Archivos auxiliares, respaldos y exportaciones
│   ├── backup/               # Scripts de respaldo del sistema (backup.sh)
│   ├── infocorp/             # Archivos generados para Equifax / Infocorp
│   └── sessions/             # Almacenamiento de sesiones PHP
├── lib/                      # Librerías y extensiones
│   └── Quipu/                # Framework interno para XUL, ACL, DB y Logs
├── www/                      # Recursos web públicos
│   ├── css/                  # Hojas de estilo para vistas e impresión
│   ├── img/                  # Íconos y logotipos
│   └── js/                   # Scripts JavaScript (XUL helpers, jQuery, Prototype)
├── .htaccess                 # Reglas de reescritura de Apache y configuración PHP
└── index.php                 # Punto de entrada HTTP único
```

---

## ⚙️ Requisitos del Sistema

- **Servidor Web**: Apache 2.2 / 2.4 con módulo `mod_rewrite` habilitado.
- **PHP**: PHP 5.3 o superior (compatible con extensiones `php_interbase` o `pdo_firebird`).
- **Librerías PHP**: `short_open_tag = On`, `session`, `json`, `mbstring`.
- **Motor de Base de Datos**: Firebird SQL Server 2.1 o 2.5 (SuperServer / Classic) con librerías UDF (`fbudf`, `ib_udf`, `FreeAdhocUDF`).
- **Cliente**: Navegador Mozilla Firefox compatible con ejecución nativa de aplicaciones XUL (Firefox ESR 3.x - 52 ESR o entorno basado en XULRunner).

---

## 📦 Instalación y Configuración

### 1. Clonar o descargar el repositorio
```bash
git clone https://github.com/juancarbajal/crediweb.git
cd crediweb
```

### 2. Configurar la Base de Datos Firebird
1. Asegurarse de tener el servicio de Firebird en ejecución.
2. Crear una base de datos `db_quipu.fdb` con dialecto 3 y charset `ISO8859_1`.
3. Importar la estructura del esquema desde [db/ddldump.sql](file:///home/jcarbajal/Projects/crediweb/db/ddldump.sql):
   ```bash
   isql-fb -u SYSDBA -p masterkey
   CREATE DATABASE 'localhost:/ruta/a/db_quipu.fdb' PAGE_SIZE 4096 DEFAULT CHARACTER SET ISO8859_1;
   IN db/ddldump.sql;
   COMMIT;
   ```

### 3. Configurar Parámetros del Sistema
Editar el archivo de configuración [app/config/config.ini](file:///home/jcarbajal/Projects/crediweb/app/config/config.ini):
```ini
[db]
adapter = Firebird
config.host = 127.0.0.1
config.username = SYSDBA
config.password = tu_clave
config.dbname = /ruta/a/db_quipu.fdb
config.dialect = 3
config.charset = ISO8859_1

[session]
save_path = /ruta/a/crediweb/files/sessions
remember_me_seconds = 600

[files]
app = /ruta/a/crediweb
```

### 4. Permisos de Directorios
Conceder permisos de escritura al usuario del servidor web en las carpetas de sesiones, respaldos y reportes:
```bash
chmod -R 775 files/
chown -R www-data:www-data files/
```

### 5. Configuración de Apache
Asegurarse de que `AllowOverride All` esté activado en el VirtualHost de Apache para que se aplique [.htaccess](file:///home/jcarbajal/Projects/crediweb/.htaccess).

---

## 💾 Respaldos Automatizados

El sistema incluye una utilidad de respaldo completo en [files/backup/backup.sh](file:///home/jcarbajal/Projects/crediweb/files/backup/backup.sh) que realiza:
1. Respaldo en caliente de la base de datos Firebird mediante `gbak`.
2. Compresión bzip2 de la base de datos respaldada.
3. Empaquetado comprimido del código fuente y configuraciones.
4. Generación de un paquete final consolidado `.tar.gz`.

---

## 👨‍💻 Autor

- **Juan Carbajal Paxi**
- Correo: [juancarbajal@gmail.com](mailto:juancarbajal@gmail.com)

