// login.js — autenticación contra PHP API

document.addEventListener('DOMContentLoaded', async () => {

  // Si ya hay sesión activa, redirigir al dashboard
  const check = await API.get('api/auth.php?action=check');
  if (check.success) { window.location.href = 'dashboard.html'; return; }

  // ── Cambio de pestaña ──
  window.switchTab = function(tab) {
    document.querySelectorAll('.tab-btn').forEach((b, i) =>
      b.classList.toggle('active', (i === 0 ? 'login' : 'register') === tab));
    document.getElementById('tab-login').classList.toggle('active', tab === 'login');
    document.getElementById('tab-register').classList.toggle('active', tab === 'register');
  };

  // ── LOGIN ──
  window.loginUser = async function() {
    const correo = document.getElementById('lCorreo').value.trim().toLowerCase();
    const clave  = document.getElementById('lClave').value;
    const errEl  = document.getElementById('login-error');
    errEl.style.display = 'none';

    if (!correo || !clave) {
      errEl.textContent = 'Ingrese correo y contraseña.';
      errEl.style.display = 'block'; return;
    }

    const res = await API.post('api/auth.php', { action: 'login', correo, clave });
    if (!res.success) {
      errEl.textContent = res.message || 'Credenciales incorrectas.';
      errEl.style.display = 'block'; return;
    }

    window.location.href = 'dashboard.html';
  };

  // ── REGISTRO ──
  window.registerUser = async function() {
    const nombre = document.getElementById('rNombre').value.trim();
    const finca  = document.getElementById('rFinca').value.trim();
    const correo = document.getElementById('rCorreo').value.trim().toLowerCase();
    const clave  = document.getElementById('rClave').value;
    const errEl  = document.getElementById('reg-error');
    const sucEl  = document.getElementById('reg-success');
    errEl.style.display = 'none';
    sucEl.style.display = 'none';

    if (!nombre || !correo || !clave) {
      errEl.textContent = 'Complete todos los campos obligatorios.';
      errEl.style.display = 'block'; return;
    }

    const res = await API.post('api/auth.php', { action: 'register', nombre, finca, correo, clave });
    if (!res.success) {
      errEl.textContent = res.message;
      errEl.style.display = 'block'; return;
    }

    sucEl.textContent = '¡Cuenta creada! Ahora puede iniciar sesión.';
    sucEl.style.display = 'block';
    ['rNombre','rFinca','rCorreo','rClave'].forEach(id => document.getElementById(id).value = '');
    setTimeout(() => switchTab('login'), 1600);
  };

  // Enter en campo contraseña
  document.getElementById('lClave')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') loginUser();
  });
});
