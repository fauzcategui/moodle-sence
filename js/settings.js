let settings_otecs = document.getElementById('id_s_sence_block_otecs')
if( settings_otecs ){
    document.getElementsByClassName('form-defaultinfo')[0].style.display = 'none'
    document.getElementsByClassName('form-label')[0].style.display = 'none'
    var otecs = JSON.parse( settings_otecs.value )
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

    settings_otecs.parentNode.insertBefore(table, settings_otecs.nextSibling)
    table.parentNode.insertBefore(note, table.nextSibling)
    document.getElementById('id_s_sence_block_otecs').style.display = 'none'
}

function deleteOtec(i){
    otecs.splice(i,1)
    renderTable()
}

function renderTable(){
    drawTable()
    document.getElementById('id_s_sence_block_otecs').value = JSON.stringify(otecs)
}

function addOtec(){
    if( !formValido() ){
        return false
    }

    otecs.push({
        'name': document.getElementById('preview-name').value,
        'rut': document.getElementById('preview-rut').value,
        'token': document.getElementById('preview-token').value
    })
    renderTable()
}

function drawTable(){
    let content = `
        <tr>
            <td><input class="sence-preview" type="text" placeholder="NOMBRE OTEC" id="preview-name" /></td>
            <td><input class="sence-preview" type="text" placeholder="RUT OTEC" id="preview-rut" /></td>
            <td><input class="sence-preview" type="text" placeholder="TOKEN" id="preview-token" /></td>
            <td><button class="btn-success btn" onclick="addOtec(); return false">Agregar</button></td>
        </tr>`

    for( i in otecs ){
        content = `
            ${content}
            <tr style="border:solid 1px gray;">
                <td>${otecs[i].name}</td>
                <td>${otecs[i].rut}</td>
                <td>${otecs[i].token}</td>
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

