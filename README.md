# SENCE Moodle Plugin
Plugin desarrollado para que Organismos Técnicos de Capacitación implementen la asistencia en los cursos bajo modalidad sence [Info SENCE](https://sence.gob.cl/organismos/control-e-learning-otec)

## Importante
Actualmente este plugin no implemente el cierre de sesión manual por parte del alumno, es decir que tampoco muestra un contador de minutos al iniciar la sesión, sin embargo puede configurarse para solicitar una sola asistencia en toda la duración del curso para cumplir con el requisito de la resolución [2436](https://sence.gob.cl/sites/default/files/rex_n_2436_deja_sin_efecto_exigencia_de_conectividad.pdf) ó cerrar la sesión del alumno de forma automática cada 3 horas.

Eventualmente se Agregará el cronómetro y el botón de cierre de sesión.

## Actualización 2.0 del Plugin
### Cambios
* Implementación Multi OTEC que permite configurar una OTEC diferente en cada Curso.
* La caja de Texto donde se agregan los Alumnos (RUN) con su código SENCE fue cambiado por un formulario más amigable.
* Cierre de Sesión automático cada 3 Horas.
* Mejoras visuales y de usabilidad al momento de configurar el Bloque.
###

## Descarga
[Versión Estable](https://github.com/fauzcategui/moodle-sence/archive/v2.0.1.zip)


## Configuración General
En la Configuración General del Bloque se agregan:
* RUT de las OTECs
* Token de cada OTEC generado en [https://sistemas.sence.cl/rts](https://sistemas.sence.cl/rts)


## Instrucciones

##### 1 - Run del alumno:
> Primero debemos configurar el RUN del alumno como nombre de usuario en el formato sin puntos y  el guion separador del dígito verificador ejem. 1111111-1.
>
> Si el alumno tiene como nombre de usuario un RUN válido, el plugin usará este para el proceso de asistencia. En el caso de no ser así, entonces revisará el campo "ID NUMBER" del usuario para buscar un RUN válido. 
>
>Por lo tanto existen dos opciones de implementación en cuanto al usuario Alumno.

##### 2 -Configuración del Bloque:
> En el bloque de cada curso contará con las siguientes opcione:
>
>- "Selecciona OTEC" >  Mostrará las OTECs Agregadas en la configuración general del Bloque.
>
>
>- "Lineas de Capacitación" > por ahora solo disponible [Impulsa Personas (3)].
>
>
>- "Código SENCE del Curso" > Este es el código asignado por SENCE a una OTEC para un curso específico.
>
>
>- "Habilitar curso solo para alumnos con código SENCE" > Al desactivar esta opción permite que usuarios nos listados en el bloque de Inrtegración SENCE puedan ver el contenido del curso y solo exigirá asistencia a los especificados en dicha lista.
>
>
>- "Cerrar sesión del Alumno después de 3 Horas" > Por defecto el bloque exigirá una sola sesión durante toda la duración del Curso. Al activar esta opción la asistencia de SENCE será requerida si el alumno intenta acceder al curso en un período mayor a 3 horas desde el último registro de asistencia.
>
>
>- "Lista de Alumnos" > Precarga un RUN (sin puntos y con dígito verificador separado por guión) y Código SENCE por alumno. Una vez agregados todos los alumnos se deberá guardar los cambios del Bloque para que estos se almacenen en la base de datos.
>
>
> También se puede activar la opción de "Activar carga Masiva de Alumnos" para habilitar una caja de texto que permita precargar un listado de alumnos con las siguientes condiciones:
> - 1 Alumno por cada Linea
> - Cada linea debe contener El RUN del alumno y luego el código SENCE del alumno separados por un espacio en blanco. Por ejemplo: "111111-1 12345678"

## Contribuciones
Este plugin debe ser mejorado para agregar;

- Cierre de Sesión
- Cronotmetro de Asistencia

## License
[GNU AGPLv3](https://choosealicense.com/licenses/agpl-3.0/)
