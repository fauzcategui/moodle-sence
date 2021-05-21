# Plugin de Integración SENCE para Moodle
Plugin desarrollado para que Organismos Técnicos de Capacitación implementen la asistencia en los cursos bajo modalidad sence [Info SENCE](https://sence.gob.cl/organismos/control-e-learning-otec)

## Actualización 3.2 Alternativa del Plugin
Requiere Moodle 3.8 o superior
### Cambios
* Agrega la funcionalidad de Descarga de Reportes de asistencias por Curso
* Finalmente se agrego el cierre de Sesión con su respectivo contador de Minutos.
###
### Arreglos:
* Se arreglaron detalles del código que no permitían regisrtrar la asistencia de los cursos con linea de capacitación "1"
* Corrección de la doble ejecución de código. Esto hacía que se guardaran 2 veces las asistencias en la base de datos. (Esto afectaba relamente a la reportería que se agrego en esta nueva versión).
###


[Versión Estable](https://github.com/fauzcategui/moodle-sence/releases/download/v3.2-alt/sence-v3.2-alt.zip)



## Configuración General
En la Configuración General del Bloque se agregan:
* RUT de las OTECs
* Token de cada OTEC generado en [https://sistemas.sence.cl/rts](https://sistemas.sence.cl/rts)

## Instrucciones

##### 1 - Configuración Global del Plugin:
> En la configuración global ubicada en  "Site administration / Plugins / Blocks / Integración SENCE"
>
>- Activar/Desactivar Multiotec ( Default: Desactivado )
>- Agregar (Nombre | Rut | Token) de las OTECs. En el caso de estár desactivado el Multiotec, solo permitirá agregar una OTEC y esta será usada para todas las instancias existentes del Bloque, también eliminará la opción de "Seleccionar OTEC" en la configuración individual de los bloques de integración SENCE

##### 2 - Run del alumno:
> Primero debemos configurar el RUN del alumno como nombre de usuario en el formato sin puntos y  el guion separador del dígito verificador ejem. 1111111-1.
>
> Si el alumno tiene como nombre de usuario un RUN válido, el plugin usará este para el proceso de asistencia. En el caso de no ser así, entonces revisará el campo "ID NUMBER" del usuario para buscar un RUN válido.
>
> Por lo tanto existen dos opciones de implementación en cuanto al usuario Alumno.

##### 3 -Configuración del Bloque:
> En el bloque de cada curso contará con las siguientes opcione:
>
>- [Multiotec Activado] "Selecciona OTEC" >  Mostrará las OTECs Agregadas en la configuración general del Bloque.
>
>
>- "Lineas de Capacitación" > Por defecto [Impulsa Personas (3)].
>
>
>- "Código SENCE del Curso" > Este es el código asignado por SENCE a una OTEC para un curso específico. En el caso de usar la Línea de Capacitación 1 este campo se desactiva ya que SENCE no lo requiere (se manda en blanco).
>
>
>- "Nombre de grupo Becarios" > El nombre del grupo donde se agregaran los alumnos que participarán en el curso sin integración SENCE. Por defecto viene con el nombre "Becarios"
>
>
>- "Solicitar cierre de Sesión SENCE" [Default: desactivado] > Por defecto el bloque exigirá una sola sesión durante toda la duración del Curso. Pero al activar esta opción se le mostrará el botón de "Cerrar Sesión" al participante, las sesiones tendrán una duración máxima de 3 horas y el bloque volverá a solicitar inicio de sesión después de ese tiempo aún si el participante no cerró de forma manual la última sesión.
>
>
> - "Mostrar logo SENCE en en Bloque[Default: desactivado]" > muestra el logo de SENCE en la parte inferior del Bloque.
##### 4 -Asignación de alumnos:
>- Los alumnos se deben asignar a un grupo "SENCE-XXXXXX", donde XXXXXX es el ID de acción del alumno.

>- En el caso de los programas sociales que usan la linea de capacitación "1" donde el código entregado por el SIC tiene un formato como este "RLAB-19-02-08-0071-1". El grupo donde se deben asignar los alumnos quedaría "SENCE-RLAB-19-02-08-0071-1"

## Contribuciones
Este plugin debe ser mejorado para agregar;

- Documentación Amigable :sweat_smile:
## License
[GNU AGPLv3](https://choosealicense.com/licenses/agpl-3.0/)
