let form_alumnos = document.getElementById('id_config_senceAlumnos')
if( form_alumnos ){
    var alumnos = JSON.parse( form_alumnos.value )
    var div = document.createElement("div")

    div.style.margin = '20px 0 0 0'
    div.id = "preview-alumnos"

    drawTable()


    let note  = document.createElement('p')
    note.style.margin = '10px 0 10px 0'
    note.style.padding = '10px'
    note.style.background = '#f0ad4e'
    note.innerHTML = `Los cambios realizados en esta tabla solo se harán efectivo al pulsar el Boton "Guardar" en la parte Inferior`

    form_alumnos.parentNode.insertBefore(div, form_alumnos.nextSibling)
    div.parentNode.insertBefore(note, div.nextSibling)
    document.getElementById('id_config_senceAlumnos').style.display = 'none'
}

function deleteAlumno(i){
    alumnos.splice(i,1)
    renderTable()
}

function deleteAlumnos(){
    if(alumnos.length > 0){
        alumnos = []
        renderTable()
    }
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

function addAlumnos(){
    let textBox = document.getElementById('masive-data')
    let data = textBox.value.trim()
    let lines = data.split('\n')
    for( i in lines ) {
        let alumno = lines[i].split(' ')
        if( alumno.length < 2 ){
            textBox.style.border = 'red solid'
            return false
        }
    }
    textBox.style = 'initial'
    for( i in lines ){
        alumno = lines[i].split(' ')
        alumnos.push({
            rut: alumno[0].trim(),
            cod: alumno[1].trim()
        })
    }
    renderTable()
}

function drawTable(){
    let content = `
        <table>
            <tr id="masive-container" style="display:none;">
                <td colspan="3">
                    <textarea placeholder="1 Alumno Por Linea \n[RUN] [ESPACIO] [CODIGO] \nEjemplo: \n111111-1 1234567 \n222222-2 87654321" style="width:100%;" id="masive-data" wrap="virtual" rows="8" cols="50"></textarea>
                    <span class="btn-success btn" onclick="addAlumnos()">Agregar</span>
                    <span class="btn-warning btn" onclick="deleteAlumnos()">Eliminar Lista Completa Actual</span>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="3">
                    <input onclick="openMasive(this)" type="checkbox" id="is-masive" />
                    Activar carga Masiva de Alumnos
                </td>
            </tr>
            <tr id="individual-container">
                <td><input type="text" placeholder="RUN Alumno" id="preview-rut" /></td>
                <td><input type="text" placeholder="Código Sence Alumno" id="preview-cod" /></td>
                <td><span class="btn-success btn" onclick="addAlumno()" id="preview-btn">Agregar</span></td>
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
    div.innerHTML = `${content}</table>`
}

function openMasive(e){
    document.getElementById('masive-data').value = ''
    document.getElementById('masive-container').style.display = !e.checked ? 'none' : 'initial'
    document.getElementById('preview-rut').disabled = e.checked
    document.getElementById('preview-cod').disabled = e.checked
    document.getElementById('preview-btn').onclick = e.checked ? null : () => { addAlumno() }
    
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

