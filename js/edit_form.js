let form_alumnos = document.getElementById('id_config_senceAlumnos')
if( form_alumnos ){
    var alumnos = JSON.parse( form_alumnos.value )
    var table = document.createElement("table")
    table.style.margin = '20px 0 0 0'
    drawTable()

    table.id = "preview-alumnos"

    let note  = document.createElement('p')
    note.style.margin = '10px 0 10px 0'
    note.style.padding = '10px'
    note.style.background = '#f0ad4e'
    note.innerHTML = `Los cambios realizados en esta tabla solo se harán efectivo al pulsar el Boton "Guardar" en la parte Inferior`

    form_alumnos.parentNode.insertBefore(table, form_alumnos.nextSibling)
    table.parentNode.insertBefore(note, table.nextSibling)
    document.getElementById('id_config_senceAlumnos').style.display = 'none'
}

function deleteAlumno(i){
    alumnos.splice(i,1)
    renderTable()
}

function renderTable(){
    drawTable()
    document.getElementById('id_config_senceAlumnos').value = JSON.stringify(alumnos)
}

function addAlumno(){
    if( !formValido() ){
        return false
    }

    alumnos.push({
        'rut': document.getElementById('preview-rut').value,
        'cod': document.getElementById('preview-cod').value,
    })
    renderTable()
}

function drawTable(){
    let content = `
        <tr>
            <td><input class="sence-preview" type="text" placeholder="RUN Alumno" id="preview-rut" /></td>
            <td><input class="sence-preview" type="text" placeholder="Código Sence Alumno" id="preview-cod" /></td>
            <td><span class="btn-success btn" onclick="addAlumno()">Agregar</span></td>
        </tr>`

    for( i in alumnos ){
        content = `
            ${content}
            <tr style="border:solid 1px #ced4da;">
                <td>${alumnos[i].rut}</td>
                <td>${alumnos[i].cod}</td>
                <td><span class="btn-warning btn" onclick="deleteAlumno(${i})">Eliminar</span></td>
            </tr>
            `
    }
    table.innerHTML = content
}

function formValido(){
    document.getElementById('preview-rut').style = 'initial'
    document.getElementById('preview-cod').style = 'initial'

    if( !document.getElementById('preview-rut').value ){
        document.getElementById('preview-rut').style.border = 'red solid'
        return false
    }
    if( !document.getElementById('preview-cod').value ){
        document.getElementById('preview-cod').style.border = 'red solid'
        return false
    }
    document.getElementById('preview-rut').style = 'initial'
    document.getElementById('preview-cod').style = 'initial'

    return true
}

document.getElementById('id_config_lineaCap').onchange = function(){
    console.log( 'cambio' )
    if( document.getElementById('id_config_lineaCap').value == 1 ){
        document.getElementById('id_config_codigoCurso').disabled = true
        return null
    }
    document.getElementById('id_config_codigoCurso').disabled = false


}

