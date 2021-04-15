let settings_otecs = document.getElementById('id_s_block_sence_otecs')
if( settings_otecs ){
    document.getElementsByClassName('form-defaultinfo')[0].style.display = 'none'
    document.getElementsByClassName('form-label')[0].style.display = 'none'
    var otecs = JSON.parse( settings_otecs.value )
    var checkbox = document.createElement("input")
    var label = document.createElement("label")
    label.innerHTML = "Activar/Desactivar Multiotec"
    label.style.marginLeft = "5px"
    checkbox.setAttribute("type", "checkbox")
    checkbox.checked = otecs.multiotec
    checkbox.style.marginTop = '20px'
    var table = document.createElement("table")
    table.style.margin = '20px 0 0 0'
    drawTable()

    table.id = "preview-otecs"
    table.style.width = "100%"

    let note  = document.createElement('p')
    note.style.margin = '10px 0 10px 0'
    note.style.padding = '10px'
    note.style.background = '#f0ad4e'
    note.innerHTML = `Los cambios realizados en esta tabla solo se harán efectivo al pulsar el Boton "Guardar" en la parte Inferior`

    settings_otecs.parentNode.insertBefore(checkbox, settings_otecs.nextSibling)
    checkbox.parentNode.insertBefore(label, checkbox.nextSibling)
    label.parentNode.insertBefore(table, label.nextSibling)
    table.parentNode.insertBefore(note, table.nextSibling)
    document.getElementById('id_s_block_sence_otecs').style.display = 'none'

    checkbox.onchange = function (){
        otecs.multiotec = this.checked
        renderTable()
    }
}

function deleteOtec(i){
    otecs.otecs.splice(i,1)
    renderTable()
}

function renderTable(){
    drawTable()
    document.getElementById('id_s_block_sence_otecs').value = JSON.stringify(otecs)
}

function addOtec(){
    if( !formValido() ){
        return false
    }
    if( !checkbox.checked ){
        if(otecs.otecs.length > 0){
            return false
        }
    }

    otecs.otecs.push({
        'name': document.getElementById('preview-name').value,
        'rut': document.getElementById('preview-rut').value,
        'token': document.getElementById('preview-token').value
    })
    renderTable()
}

function drawTable(){
    if( !checkbox.checked && otecs.otecs.length > 1){
        otecs.otecs = [otecs.otecs[0]]
    }
    let disabled = !checkbox.checked && otecs.otecs.length > 0 ? 'disabled' : ''
    let content = `
        <tr>
            <td><input class="sence-preview" type="text" placeholder="NOMBRE OTEC" id="preview-name" /></td>
            <td><input class="sence-preview" type="text" placeholder="RUT OTEC" id="preview-rut" /></td>
            <td><input class="sence-preview" type="text" placeholder="TOKEN" id="preview-token" /></td>
            <td><button id="add-otec" class="btn-success btn" ${disabled} onclick="addOtec(); return false">Agregar</button></td>
        </tr>`

    for( i in otecs.otecs ){
        content = `
            ${content}
            <tr style="border:solid 1px #ced4da;">
                <td>${otecs.otecs[i].name}</td>
                <td>${otecs.otecs[i].rut}</td>
                <td>${otecs.otecs[i].token}</td>
                <td><button class="btn-warning btn" onclick="deleteOtec(${i}); return false">Eliminar</button></td>
            </tr>
            `
    }
    table.innerHTML = content
}

function formValido(){
    document.getElementById('preview-rut').style = 'initial'
    document.getElementById('preview-name').style = 'initial'
    document.getElementById('preview-token').style = 'initial'

    if( !document.getElementById('preview-name').value ){
        document.getElementById('preview-name').style.border = 'red solid'
        return false
    }
    if( !document.getElementById('preview-rut').value ){
        document.getElementById('preview-rut').style.border = 'red solid'
        return false
    }
    if( !document.getElementById('preview-token').value ){
        document.getElementById('preview-token').style.border = 'red solid'
        return false
    }
    document.getElementById('preview-rut').style = 'initial'
    document.getElementById('preview-name').style = 'initial'
    document.getElementById('preview-token').style = 'initial'

    return true
}
