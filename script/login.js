document.addEventListener('DOMContentLoaded', () => {
  const btnIniciar = document.getElementById('btn-iniciar');
 
  if (btnIniciar) {
    btnIniciar.addEventListener('click', () => {
      window.location.href = 'dashboard.html';
    });
  }
});
 