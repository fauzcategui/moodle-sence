let main = document.getElementById('region-main')
if(main){
    main.style.filter = 'blur(5px)'
    main.style['pointer-events'] = 'none'
    main.style.display = 'none'
    main.nextElementSibling.style.width = '100%'
}