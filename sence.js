
let settings_otecs = document.getElementById('id_s_sence_block_otecs')
if( settings_otecs ){
    var otecs = JSON.parse( settings_otecs.value )
    var table = document.createElement("table")
    drawTable()

    table.id = "preview-otecs"
    table.style.width = "100%"

    settings_otecs.parentNode.insertBefore(table, settings_otecs.nextSibling)
    document.getElementById('id_s_sence_block_otecs').style.display = 'none'
}

function deleteOtec(i){
    // console.log(`la Otec con indice ${i} SERÁ ELIMINADA`)
    otecs.splice(i,1)
    renderTable()
}

function renderTable(){
    drawTable()
    document.getElementById('id_s_sence_block_otecs').value = JSON.stringify(otecs)
}

function addOtec(){
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
            <td><input type="text" placeholder="Nombre" id="preview-name" /></td>
            <td><input type="text" placeholder="Rut" id="preview-rut" /></td>
            <td><input type="text" placeholder="Token" id="preview-token" /></td>
            <td><td><span onclick="addOtec()">Agregar</span></td</td>
        </tr>`

    for( i in otecs ){
        content = `
            ${content}
            <tr>
                <td>${otecs[i].name}</td>
                <td>${otecs[i].rut}</td>
                <td>${otecs[i].token}</td>
                <td><span onclick="deleteOtec(${i})">Eliminar</span></td>
            </tr>
            `
    }
    table.innerHTML = content
}

