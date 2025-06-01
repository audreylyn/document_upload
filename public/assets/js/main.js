/*===== SHOW NAVBAR  =====*/ 
const showNavbar = () =>{
    const toggle = document.querySelector('.menu-checkbox'),
    nav = document.querySelector('.side-menu'),
    bodypd = document.querySelector('.main-body'),
    headerpd = document.querySelector('.top-bar')

    // Validate that all variables exist
    if(toggle && nav && bodypd && headerpd){
        toggle.addEventListener('change', ()=>{
            // show navbar
            nav.classList.toggle('menu-expanded')
            // add padding to body
            bodypd.classList.toggle('content-shifted')
            // add padding to header
            headerpd.classList.toggle('content-shifted')
        })
    }
}

showNavbar()

/*===== LINK ACTIVE  =====*/ 
const linkColor = document.querySelectorAll('.menu-item')

function colorLink(){
    if(linkColor){
        linkColor.forEach(l=> l.classList.remove('menu-active'))
        this.classList.add('menu-active')
    }
}
linkColor.forEach(l=> l.addEventListener('click', colorLink))