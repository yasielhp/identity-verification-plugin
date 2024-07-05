=== Identity Verification ===
Contributors: [tu-usuario]
Tags: verification, identity, user verification, google drive
Requires at least: 5.0
Tested up to: 6.1
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Este plugin permite verificar la identidad de los usuarios y mostrar contenido según el estado de verificación.

== Descripción ==

Identity Verification es un plugin de WordPress que permite a los administradores del sitio verificar la identidad de los usuarios. Los usuarios pueden subir una foto de su DNI, que se almacenará en Google Drive. El administrador puede revisar las solicitudes de verificación y aprobar o rechazar a los usuarios.

== Instalación ==

1. **Subir el Plugin**:
   - Sube la carpeta `identity-verification` al directorio `/wp-content/plugins/` en tu servidor de producción.

2. **Instalar Dependencias**:
   - Navega al directorio del plugin en tu servidor:
     ```sh
     cd /path/to/wordpress/wp-content/plugins/identity-verification
     ```
   - Asegúrate de que Composer esté instalado en tu servidor. Si no lo está, puedes instalarlo siguiendo las instrucciones en [getcomposer.org](https://getcomposer.org/download/).
   - Instala las dependencias ejecutando:
     ```sh
     composer install
     ```

3. **Activar el Plugin**:
   - Activa el plugin a través del menú 'Plugins' en WordPress.

4. **Configurar el Plugin**:
   - Configura el plugin en la página de ajustes de 'Identity Verification' en el panel de administración de WordPress.
   - Añade el shortcode `[iv_verification_form]` a la página donde deseas mostrar el formulario de verificación.

== Configuración ==

1. **Configurar Google Drive**:
   - Crea un proyecto en Google Cloud Console.
   - Habilita la API de Google Drive.
   - Configura una cuenta de servicio y descarga el archivo de credenciales JSON.
   - Coloca el archivo de credenciales JSON en el directorio `includes` del plugin.

2. **Página de Redirección**:
   - Selecciona la página a la que los usuarios no verificados serán redirigidos en los ajustes del plugin.

== Uso ==

1. **Formulario de Verificación**:
   - Los usuarios deben completar el formulario de verificación ingresando su número de DNI y subiendo una foto de su DNI.

2. **Aprobación de Verificación**:
   - El administrador del sitio puede revisar y aprobar las solicitudes de verificación desde el panel de administración.

== Capturas de pantalla ==

1. Formulario de verificación de identidad para los usuarios.
2. Panel de administración para gestionar las solicitudes de verificación.

== Frequently Asked Questions ==

= ¿Dónde se almacenan las fotos de los DNI? =
Las fotos de los DNI se almacenan en una carpeta específica en Google Drive configurada por el administrador del sitio.

= ¿Cómo se aprueban o rechazan las solicitudes de verificación? =
El administrador del sitio puede revisar las solicitudes de verificación desde el panel de administración de WordPress y aprobar o rechazar cada solicitud.

== Changelog ==

= 1.0 =
* Versión inicial del plugin.

== Upgrade Notice ==

= 1.0 =
Versión inicial del plugin.

== License ==

Este plugin es software libre: puedes redistribuirlo y/o modificarlo bajo los términos de la Licencia Pública General GNU publicada por la Free Software Foundation, ya sea la versión 2 de la Licencia, o cualquier versión posterior.

Este plugin se distribuye con la esperanza de que sea útil, pero SIN NINGUNA GARANTÍA; sin siquiera la garantía implícita de COMERCIABILIDAD o IDONEIDAD PARA UN PROPÓSITO PARTICULAR. Consulta la Licencia Pública General GNU para obtener más detalles.

Deberías haber recibido una copia de la Licencia Pública General GNU junto con este plugin. Si no, consulta <https://www.gnu.org/licenses/gpl-2.0.html>.
