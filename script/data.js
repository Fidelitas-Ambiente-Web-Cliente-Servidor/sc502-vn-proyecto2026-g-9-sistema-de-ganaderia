// ════════════════════════════════════════════════
//  data.js — Cliente API (reemplaza localStorage)
//  Todas las páginas usan este archivo base.
// ════════════════════════════════════════════════

const API = {
  async request(url, options = {}) {
    const cfg = {
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      ...options,
    };
    if (options.body && typeof options.body === 'object') {
      cfg.body = JSON.stringify(options.body);
    }
    const res  = await fetch(url, cfg);
    const data = await res.json();
    return data;
  },

  get:  (url)        => API.request(url),
  post: (url, body)  => API.request(url, { method: 'POST',   body }),
  put:  (url, body)  => API.request(url, { method: 'PUT',    body }),
  del:  (url)        => API.request(url, { method: 'DELETE' }),
};

// ─── Helpers de formato ───
function formatColones(n) {
  return '₡' + Number(n).toLocaleString('es-CR');
}

function badgeEstado(e) {
  const map = { 'Activo': 'badge-green', 'En tratamiento': 'badge-yellow', 'Vendido': 'badge-gray' };
  return `<span class="badge ${map[e] || 'badge-gray'}">${e}</span>`;
}

// ─── Toast global ───
function toast(msg, tipo = 'ok') {
  let el = document.getElementById('toast');
  if (!el) { el = document.createElement('div'); el.id = 'toast'; document.body.appendChild(el); }
  el.textContent = (tipo === 'ok' ? '✅ ' : '❌ ') + msg;
  el.style.background = tipo === 'ok' ? 'var(--verde-900)' : 'var(--rojo)';
  el.classList.add('show');
  clearTimeout(el._t);
  el._t = setTimeout(() => el.classList.remove('show'), 2800);
}

// ─── Modal helpers ───
function abrirModal(id)  { document.getElementById(id)?.classList.add('open'); }
function cerrarModal(id) { document.getElementById(id)?.classList.remove('open'); }
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('open');
});

// ─── initPage: verificar sesión y preparar sidebar ───
async function initPage() {
  const res = await API.get('api/auth.php?action=check');
  if (!res.success) { window.location.href = 'index.html'; return null; }

  const s = res.data;

  // Marcar enlace activo
  const page = location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-link').forEach(a => {
    a.classList.remove('active');
    if (a.getAttribute('href') === page) a.classList.add('active');
  });

  // Rellenar user-pill
  const av = document.getElementById('sidebarAvatar');
  const nm = document.getElementById('sidebarNombre');
  const em = document.getElementById('sidebarCorreo');
  if (av) av.textContent = (s.nombre || 'U')[0].toUpperCase();
  if (nm) nm.textContent = s.nombre || '';
  if (em) em.textContent = s.correo || '';

  // Botón cerrar sesión
  document.getElementById('btn-cerrar')?.addEventListener('click', async e => {
    e.preventDefault();
    await API.post('api/auth.php', { action: 'logout' });
    window.location.href = 'index.html';
  });

  return s;
}
