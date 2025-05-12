function checkWidthAndAddClass() {
    const element = document.getElementById('sidebar');
    const sideBarFooter = document.getElementById("dev-footer");
    if (window.innerWidth <= 768) {
        element.classList.add('hide');
        sideBarFooter.classList.add('hide-footer')
        
    } else {
        element.classList.remove('hide');
        sideBarFooter.classList.remove('hide-footer')
    }
}


checkWidthAndAddClass();

window.addEventListener('resize', checkWidthAndAddClass);