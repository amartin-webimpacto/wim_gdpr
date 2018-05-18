# WebImpacto GDPR

## Base de datos
<table>
	<thead>
		<tr>
			<th colspan="2">WIM_GDPR_CMS_VERSIONS</th>
		</tr>
		<tr>
			<th>Campo</th>
			<th>Descripción</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>id_gdpr_cms_version</td>
			<td>PRIMARY KEY</td>
		</tr>
		<tr>
			<td>id_cms</td>
			<td>Identificador del CMS versionado</td>
		</tr>
		<tr>
			<td>id_shop</td>
			<td>Identificador de la tienda para la que se versiona el CMS</td>
		</tr>
		<tr>
			<td>id_lang</td>
			<td>Idioma en el que se versiona el CMS</td>
		</tr>
		<tr>
			<td>id_employee</td>
			<td>Usuario que realiza la acción</td>
		</tr>
		<tr>
			<td>old_meta_title</td>
			<td>Estado del campo que tenía el CMS antes de ser modificado</td>
		</tr>
		<tr>
			<td>old_meta_description</td>
			<td>Estado del campo que tenía el CMS antes de ser modificado</td>
		</tr>
		<tr>
			<td>old_meta_keywords</td>
			<td>Estado del campo que tenía el CMS antes de ser modificado</td>
		</tr>
		<tr>
			<td>old_content</td>
			<td>Estado del campo que tenía el CMS antes de ser modificado</td>
		</tr>
		<tr>
			<td>old_link_rewrite</td>
			<td>Estado del campo que tenía el CMS antes de ser modificado</td>
		</tr>
		<tr>
			<td>new_meta_title</td>
			<td>Estado del campo después de ser modificado el CMS</td>
		</tr>
		<tr>
			<td>new_meta_description</td>
			<td>Estado del campo después de ser modificado el CMS</td>
		</tr>
		<tr>
			<td>new_meta_keywords</td>
			<td>Estado del campo después de ser modificado el CMS</td>
		</tr>
		<tr>
			<td>new_content</td>
			<td>Estado del campo después de ser modificado el CMS</td>
		</tr>
		<tr>
			<td>new_link_rewrite</td>
			<td>Estado del campo después de ser modificado el CMS</td>
		</tr>
		<tr>
			<td>modification_reason_for_a_new</td>
			<td>Motivo por el que se actualiza el CMS</td>
		</tr>
		<tr>
			<td>show_to_users</td>
			<td>Determina cómo se mostrará al usuario la actualización del CMS</td>
		</tr>
		<tr>
			<td>date_add</td>
			<td>Fecha en la que se realiza la acción</td>
		</tr>
	</tbody>
</table>

<br/>

<br/>

<table>
	<thead>
		<tr>
			<th colspan="2">WIM_GDPR_USER_ACCEPTANCE</th>
		</tr>
		<tr>
			<th>Campo</th>
			<th>Descripción</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>id_customer</td>
			<td>PRIMARY KEY</td>
		</tr>
		<tr>
			<td>id_gdpr_cms_version</td>
			<td>PRIMARY KEY</td>
		</tr>
		<tr>
			<td>date_add</td>
			<td>Fecha en la que se realiza la acción</td>
		</tr>
		<tr>
			<td>ip_address</td>
			<td>IP desde la que el usuario acepta la modificación del CMS</td>
		</tr>
		<tr>
			<td>user_agent</td>
			<td>User agent del navegador del usuario</td>
		</tr>
		<tr>
			<td>user_browser</td>
			<td>Navegador desde el que el usuario acepta la modificación del CMS</td>
		</tr>
		<tr>
			<td>user_platform</td>
			<td>Sistema operativo desde el que el usuario acepta la modificación del CMS</td>
		</tr>
		<tr>
			<td>url_on_acceptance</td>
			<td>URL desde la que el usuario acepta la modificación del CMS</td>
		</tr>
	</tbody>
</table>

<br/>

<br/>

<table>
	<thead>
		<tr>
			<th colspan="2">WIM_GDPR_ACTION_ACCEPTANCE</th>
		</tr>
		<tr>
			<th>Campo</th>
			<th>Descripción</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>id_gdpr_action_acceptance</td>
			<td>PRIMARY KEY</td>
		</tr>
		<tr>
			<td>id_guest</td>
			<td>Identificador del invitado (cuando no es un usuario registrado) que acepta las condiciones de una acción determinada</td>
		</tr>
		<tr>
			<td>id_customer</td>
			<td>Identificador del usuario que acepta las condiciones de una acción determinada</td>
		</tr>
		<tr>
			<td>id_gdpr_cms_version</td>
			<td>Identificador de la versión del CMS que acepta el usuario. <br/> Puede ser igual a 0 si el CMS en cuestión no está versionado o si el CMS no está protegido por GDPR</td>
		</tr>
		<tr>
			<td>id_cms</td>
			<td>Identificador del CMS que acepta el usuario</td>
		</tr>
		<tr>
			<td>id_shop</td>
			<td>Identificador de la tienda en la que se ubica el usuario al aceptar el CMS</td>
		</tr>
		<tr>
			<td>id_lang</td>
			<td>Identificador del lenguaje en el que el usuario acepta el CMS</td>
		</tr>
		<tr>
			<td>date_add</td>
			<td>Fecha en la que se realiza la acción</td>
		</tr>
		<tr>
			<td>ip_address</td>
			<td>IP desde la que el usuario acepta la modificación del CMS</td>
		</tr>
		<tr>
			<td>user_agent</td>
			<td>User agent del navegador del usuario</td>
		</tr>
		<tr>
			<td>user_browser</td>
			<td>Navegador desde el que el usuario acepta la modificación del CMS</td>
		</tr>
		<tr>
			<td>user_platform</td>
			<td>Sistema operativo desde el que el usuario acepta la modificación del CMS</td>
		</tr>
		<tr>
			<td>url_on_acceptance</td>
			<td>URL desde la que el usuario acepta la modificación del CMS</td>
		</tr>
	</tbody>
</table>


## Hooks

### DisplayWimGdprChecks
En ciertos formularios se requerirá la aceptación por parte del usuario de ciertos términos/CMS/etcétera, para esta tarea, disponemos del hook **"DisplayWimGdprChecks"** que mostrará uno o varios checkbox para aceptarlos obligatoriamente.
El hook aceptará el parámetro opcional "id", que será el/los id de los CMS que se mostrarán (se pueden incluir identificadores de CMS que no estén protegidos).
Si el hook no recibe dicho parámetro, se mostrará un checkbox por cada CMS protegido y no desactivado.
La sintaxis para ejecutar el hook mostrando los CMS con id 1 y 2 sería la siguiente:
> {hook h='displayWimGdprChecks' id='1,2'}

Para que este hook funcione correctamente, debe incluirse su llamada siempre dentro de un formulario.

### DisplayCMSHistory
En el frontOffice, en la página de cada CMS protegido, se mostrará una lista con el historial de cambios que ha recibido a lo largo del tiempo.
Para mostrarlo, se llamará al hook desde el template. La configuración del template variará según la versión de PrestaShop utilizada:
 - **1.5 y 1.6**:  El template en cuestión se encuentra en la ruta *themes/[nombre-del-tema]/cms.tpl*.<br/> Justo encima del siguiente bloque de texto:
`<div class="rte{if $content_only} content_only{/if}">`
`{$cms->content}`
`</div>`
Se debe añadir la siguiente línea:
`{hook h='displayCMSHistory'}`
 - **1.7**:  El template en cuestión se encuentra en la ruta *themes/[nombre-del-tema]/templates/cms/page.tpl*.<br/> Justo encima del siguiente bloque de texto:
`<section id="content" class="page-content page-cms page-cms-{$cms.id}">`
`{block name='cms_content'}`
`{$cms.content nofilter}`
`{/block}`
Se debe añadir la siguiente línea:
`{hook h='displayCMSHistory'}`

## Instalación
Al instalar el módulo, se crearán en la base de datos las 3 tablas descritas anteriormente, además se generará en la tabla de configuración un registro con el nombre *"WIM_GDPR_CMS_LIST"* en el que se guardará en formato JSON el listado de los CMS protegidos.

Al desinstalar el módulo no se borrará ninguna tabla de la base de datos para conservar la información.

## BackOffice
### Configuración del módulo
En la configuración del módulo se mostrará un listado con todos los CMS existentes por cada tienda. Cada uno tendrá un checkbox para marcarlo como "protegido".
### Edición de CMS
Cuando editamos un CMS protegido, se mostrarán dos campos nuevos:
 - **Motivo de modificación**: Al editar un CMS protegido será obligatorio indicar una razón para modificarlo. Si en la edición del CMS sólo se modifica el campo "mostrar a usuarios", el motivo de modificación será opcional y si el usuario deja el campo vacío, se insertará el comentario por defecto *"Apartado 'Mostrar a usuarios' modificado"*.
 - **Mostrar a usuarios**: Será un desplegable para seleccionar la forma en la que se le mostrará al usuario los cambios que reciba el CMS. Tendrá tres posibles opciones:
	 - Ni se muestra en el CMS en el front la razón de cambio, ni se pedirá aceptación por parte de los usuarios si es la última versión.
	 - Se muestra en el CMS en el front la razón de cambio y se pedirá aceptación por parte de los usuarios si es la última versión.
	 - Se muestra en el CMS en el front la razón de cambio pero NO pedirá aceptación por parte de los usuarios si es la última versión.

## FrontOffice

### Aceptación de cambios en un CMS
Esto afecta solamente a los usuarios registrados cada vez que un CMS es modificado.

Al entrar a la web (da igual la URL), se le mostrará al usuario un popup bloqueante con el listado de CMS que todavía no haya aceptado, siempre que el campo "mostrar a usuarios" del CMS sea *"Se muestra en el CMS en el front la razón de cambio y se pedirá aceptación por parte de los usuarios si es la última versión"* o *"Se muestra en el CMS en el front la razón de cambio pero NO pedirá aceptación por parte de los usuarios si es la última versión"*.

Por cada CMS se mostrará el título, el motivo de la modificación y un botón para ver el contenido de dicho CMS.

El popup tendrá un botón para aceptar todos los CMS.

Cada modificación de un CMS se aceptará una sola  vez y no se volverá a pedir aceptación hasta que el CMS vuelva a ser actualizado.

### Aceptación de un CMS al enviar un formulario
Mediante el hookDisplayWimGdprChecks se podrán mostrar ciertos CMS al usuario para que los acepte antes de realizar una acción (por ejemplo, aceptar ciertas condiciones al enviar un formulario de contacto).

El CMS se aceptará cada vez que se vaya a realizar la acción en cuestión.

### Página de CMS
Mediante el hookDisplayCMSHistory, en la parte superior de la página de un CMS protegido se mostrará su histórico de cambios, indicando el motivo de la modificación.
