let main = document.getElementById('region-main')
let block = document.getElementsByClassName('block_sence')[0]
if(block){
    main.innerHTML = block.innerHTML
    block.style = 'display:none'
}