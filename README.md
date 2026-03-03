# Sistema de Exámenes

Sistema web para gestión y realización de exámenes en línea.

## Características

- Sistema de autenticación de usuarios (estudiantes y administradores)
- Gestión de preguntas por categorías
- Simulador de exámenes
- Reportes y estadísticas
- Panel de administración

## Instalación

### 1. Requisitos
- PHP 7.4 o superior
- MySQL/MariaDB
- Servidor web (Apache/Nginx)

### 2. Configuración de Base de Datos

1. Importa el archivo `examen.sql` en tu base de datos:
   ```bash
   mysql -u usuario -p < examen.sql
   ```

2. Copia el archivo de configuración:
   ```bash
   cp config/database.example.php config/database.php
   ```

3. Edita `config/database.php` y configura tus credenciales:
   ```php
   $host = 'localhost';
   $db   = 'examen';
   $user = 'tu_usuario';
   $pass = 'tu_contraseña';
   ```

### 3. Configuración del Servidor

Configura tu servidor web para que apunte a la carpeta del proyecto.

## Estructura del Proyecto

```
/admin          - Panel de administración
/estudiante     - Área de estudiantes
/config         - Archivos de configuración
/includes       - Funciones y utilidades
/assets         - CSS y recursos estáticos
```

## Seguridad

- El archivo `config/database.php` está excluido del repositorio por seguridad
- Usa las credenciales apropiadas en producción
- Mantén PHP actualizado

## Licencia

Proyecto educativo
