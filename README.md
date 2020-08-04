# SENCE Moodle Plugin
Plugin desarrollado para que Organismos Técnicos de Capacitación implementen la asistencia en los cursos bajo modalidad sence [Info SENCE](https://sence.gob.cl/organismos/control-e-learning-otec)

## Importante
Actualmente este plugin solo registra la asistencia del usuario una sola vez para cumplir con el requisito de la resolución [2436](https://sence.gob.cl/sites/default/files/rex_n_2436_deja_sin_efecto_exigencia_de_conectividad.pdf), por lo que no tiene un contador de tiempo, ni implementa el cierre de sesión.

Actualmente se esta trabajando para agregar más funcionalidades.

## Descarga
[Versión Estable](https://github.com/fauzcategui/moodle-sence/archive/v1.1.zip)


## Configuración
El primer paso antes de instalar el plugin es tener a la mano lo siguiente:
* RUT de la Otec
* Token generado en [https://sistemas.sence.cl/rts](https://sistemas.sence.cl/rts)


## Instrucciones

##### 1 - Run del alumno:
> Primero debemos configurar el RUN del alumno como nombre de usuario en el formato sin puntos y  el guion separador del dígito verificador ejem. 1111111-1.
>
> Si el alumno tiene como nombre de usuario un RUN válido, el plugin usará este para el proceso de asistencia. En el caso de no ser así, entonces revisará el campo "ID NUMBER" del usuario para buscar un RUN válido. 
>
>Por lo tanto existen dos opciones de implementación en cuanto al usuario Alumno.

##### 2 -Configuración del Bloque:

> El bloque deberá ser agregado por cada curso que tenga un código SENCE válido y las configuraciones para cada curso serán las siguientes.
>
>- Lineas de Capacitación: por ahora solo disponible [Impulsa Personas (3)]
>
>- Código SENCE del Curso: Este es el código asignado por sence a la Otec para un curso específico.
>
>- Lista de Alumnos: En la caja de texto asiganada para la lista de alumnos debemos agregar el RUN del alumno seguido del código individual de dicho alumno para este curso, separados con un espacio y agregando un alumno por linea ejemplo:
>11111111-1 98989898
>22222222-2 89898989
>
>- Habilitar curso solo para alumnos con código SENCE: Esta opción viene desactivada por defecto y hará que el plugin solo requiera la asistencia de forma obligatoria a los alumnos con código agregados a la lista de alumnos anteriormente mencionada.
> También podemos activar la opción y solo permitir el acceso al contenido del curso a las personas señaladas en la lista de alumnos que ya tengan una asistencia registrada.







## Contribuciones
Este plugin debe ser mejorado para agregar;

- Cierre de Sesión
- Cronotmetro de Asistencia

## License
[GNU AGPLv3](https://choosealicense.com/licenses/agpl-3.0/)