# Google Presentation PHP Library

Google Presentation PHP Library es una librería que permite gestionar, crear y modificar presentaciones en Google Slides, utilizando un template base y reemplazando texto dinámico según los valores proporcionados. Está diseñada para integrarse con la API de Google Slides y Google Drive, siguiendo las prácticas recomendadas de PSR-4, con el fin de facilitar la escalabilidad y mantenibilidad del proyecto.

## Características

- **Crear Presentaciones:** Genera nuevas presentaciones en Google Slides.
- **Modificación de Templates:** Reemplaza variables en las diapositivas (por ejemplo, `{{title}}`) para personalizar el contenido dinámicamente.
- **Gestión de Permisos:** Comparte presentaciones en Google Drive y permite gestionar permisos de acceso.
- **Actualización de Templates:** Si un archivo de presentación existe, se puede actualizar con un nuevo archivo PPTX.
- **Eliminación de Archivos:** Permite la eliminación de archivos desde la cuenta de servicio.

## Requisitos Previos

- PHP >= 7.4
- [Composer](https://getcomposer.org/) para manejar dependencias.
- Habilitar la API de Google Slides y Google Drive en el proyecto de Google Cloud.
- Un archivo de credenciales JSON para la cuenta de servicio de Google Cloud.

## Instalación

1. **Clonar el repositorio** y navegar al directorio del proyecto.
   ```bash
   git clone https://github.com/tuusuario/google-presentation-php-library.git
   cd google-presentation-php-library
