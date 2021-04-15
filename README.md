# SENCE Moodle Plugin
Plugin desarrollado para que Organismos Técnicos de Capacitación implementen la asistencia en los cursos bajo modalidad sence [Info SENCE](https://sence.gob.cl/organismos/control-e-learning-otec)

## Importante
Actualmente este plugin no implemente el cierre de sesión manual por parte del alumno, es decir que tampoco muestra un contador de minutos al iniciar la sesión, sin embargo puede configurarse para solicitar una sola asistencia en toda la duración del curso para cumplir con el requisito de la resolución [2436](https://sence.gob.cl/sites/default/files/rex_n_2436_deja_sin_efecto_exigencia_de_conectividad.pdf) ó cerrar la sesión del alumno de forma automática cada 3 horas.

## Actualización 3.1 del Plugin
### Cambios
* Funcionalidad Multiotec/Monototec
###

## Descarga
[Versión Estable](https://github.com/fauzcategui/moodle-sence/archive/v3.1.zip)


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
>- "Selecciona OTEC" >  Mostrará las OTECs Agregadas en la configuración general del Bloque.
>
>
>- "Lineas de Capacitación" > Por defecto [Impulsa Personas (3)].
>
>
>- "Código SENCE del Curso" > Este es el código asignado por SENCE a una OTEC para un curso específico.
>
>
>- "Nombre de grupo Becarios" > El nombre del grupo donde se agregaran los alumnos que participarán en el curso sin integración SENCE. Por defecto viene con el nombre "Becarios"
>
>
>- "Cerrar sesión del Alumno después de 3 Horas" > Por defecto el bloque exigirá una sola sesión durante toda la duración del Curso. Al activar esta opción la asistencia de SENCE será requerida si el alumno intenta acceder al curso en un período mayor a 3 horas desde el último registro de asistencia.
>
>
##### 4 -Asignación de alumnos:
>- Los alumnos se deben asignar a un grupo "SENCE-XXXXXX", donde XXXXXX es el ID de acción del alumno.

## Contribuciones
Este plugin debe ser mejorado para agregar;

- Cierre de Sesión
- Cronotmetro de Asistencia

## License
[GNU AGPLv3](https://choosealicense.com/licenses/agpl-3.0/)
