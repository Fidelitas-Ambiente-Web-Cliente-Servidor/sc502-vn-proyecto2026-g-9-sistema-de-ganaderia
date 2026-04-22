// dashboard.js — métricas del panel principal desde la API

document.addEventListener('DOMContentLoaded', async () => {

  const sesion = await initPage();
  if (!sesion) return;

  await cargarDashboard();
});

async function cargarDashboard() {
  const res = await API.get('api/dashboard.php');
  if (!res.success) { toast('Error cargando métricas', 'err'); return; }

  const d = res.data;

  document.getElementById('metAnimales').textContent = d.totalAnimales;
  document.getElementById('metVacunas').textContent  = d.pendientes;
  document.getElementById('metGastos').textContent   = formatColones(d.gastosMes);
  document.getElementById('metSalud').textContent    = d.totalSalud;

  // Alertas
  const alertasEl = document.getElementById('alertasLista');
  if (!d.alertas || d.alertas.length === 0) {
    alertasEl.innerHTML = '<div class="empty-state"><div class="icon">🔔</div><p>No hay alertas activas</p></div>';
  } else {
    alertasEl.innerHTML = d.alertas.map(r => `
      <div class="alert-item ${r.vencido == 1 ? 'danger' : 'warning'}">
        <strong>${r.animal}:</strong> ${r.tipo}${r.med ? ' – ' + r.med : ''}
        &nbsp;| Control: <strong>${r.prox}</strong>${r.vencido == 1 ? ' ⚠️ Vencido' : ''}
      </div>`).join('');
  }

  // Últimos animales
  const ultimosEl = document.getElementById('ultimosAnimales');
  if (!d.ultimosAnimales || d.ultimosAnimales.length === 0) {
    ultimosEl.innerHTML = '<div class="empty-state"><div class="icon">🐄</div><p>Sin animales registrados</p></div>';
  } else {
    ultimosEl.innerHTML = `
      <div class="table-wrap">
        <table>
          <thead><tr><th>ID</th><th>Nombre</th><th>Raza</th><th>Estado</th></tr></thead>
          <tbody>
            ${d.ultimosAnimales.map(a => `
              <tr>
                <td><strong>${a.id}</strong></td>
                <td>${a.nombre || '–'}</td>
                <td>${a.raza}</td>
                <td>${badgeEstado(a.estado)}</td>
              </tr>`).join('')}
          </tbody>
        </table>
      </div>`;
  }
}
