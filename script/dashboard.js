const navLinks = document.querySelectorAll('.nav-link[data-screen]');
const screens = document.querySelectorAll('.screen');
const btnCerrar = document.getElementById('btn-cerrar');
 
 
navLinks.forEach(link => {
  link.addEventListener('click', () => {
    const target = link.getAttribute('data-screen');
    activarPantalla(target);
  });
});
 
function activarPantalla(id) {
  screens.forEach(screen => screen.classList.remove('active'));
  navLinks.forEach(link => link.classList.remove('active'));
 
  document.getElementById(id).classList.add('active');
  document.querySelector(`.nav-link[data-screen="${id}"]`).classList.add('active');
}
 
// Cerrar sesión
if (btnCerrar) {
  btnCerrar.addEventListener('click', () => {
 
    window.location.href = 'index.html';
  });
}
 