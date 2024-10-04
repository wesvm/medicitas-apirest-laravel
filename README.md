# Gestión de Citas Médicas - APIRest

Este proyecto es una aplicación web para la gestión de citas médicas, la aplicación permite a los pacientes crear citas, los especialistas pueden gestionar sus consultas y los administradores supervisar el sistema.

## Características

-   **Autenticación JWT**: Inicio de sesión seguro utilizando tokens JWT.
-   **Roles**: Tres roles principales (Administrador, Especialista, Paciente) con permisos específicos.
-   **Gestión de Citas**: Los pacientes pueden crear citas basadas en la disponibilidad de los especialistas.
-   **Consultas Médicas**: Los especialistas pueden ver y gestionar sus consultas.
-   **Recuperación de Contraseña**: Los usuarios pueden restablecer sus contraseñas mediante correo electrónico.
-   **Base de Datos PostgreSQL**: Gestión de la persistencia de los datos con PostgreSQL.
-   **Rutas Protegidas**: Rutas específicas protegidas mediante middleware basado en roles y autenticación.

## Tecnologías

-   Laravel
-   PostgreSQL
-   JWT (JSON Web Tokens)
-   Composer
