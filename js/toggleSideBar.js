// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');
const sideBarFooter = document.getElementById("dev-footer");

menuBar.addEventListener('click', function () {
	
	sidebar.classList.toggle('hide');
	sideBarFooter.classList.toggle('hide-footer')

})