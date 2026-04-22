<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Happy Farmer – Iniciar sesión</title>
  <link rel="stylesheet" href="styles/styles.css" />
</head>
<body>

<div class="login-page">
  <div class="login-box">

    <div class="login-logo">
      <div class="ico">🐄</div>
      <h2>Happy Farmer</h2>
      <p>Gestión ganadera digital para Costa Rica</p>
    </div>

    <div class="tabs-login">
      <button class="tab-btn active" onclick="switchTab('login')">Iniciar sesión</button>
      <button class="tab-btn" onclick="switchTab('register')">Crear cuenta</button>
    </div>

    <!-- ── PANEL LOGIN ── -->
    <div id="tab-login" class="tab-panel active">
      <div id="login-error" class="error-msg"></div>
      <div class="form-grid" style="grid-template-columns:1fr;">
        <div class="form-group">
          <label for="lCorreo">Correo electrónico</label>
          <input id="lCorreo" type="email" placeholder="ganadero@correo.com" />
        </div>
        <div class="form-group">
          <label for="lClave">Contraseña</label>
          <input id="lClave" type="password" placeholder="••••••••" />
        </div>
        <div class="form-group">
          <button class="btn btn-primary" style="width:100%;justify-content:center;" onclick="loginUser()">
            Entrar al sistema
          </button>
        </div>
      </div>
      <p class="helper" style="text-align:center;margin-top:14px;">
        ¿No tiene cuenta? Haga clic en <strong>Crear cuenta</strong>.
      </p>
    </div>

    <!-- ── PANEL REGISTRO ── -->
    <div id="tab-register" class="tab-panel">
  <div id="reg-error" class="error-msg"></div>
  <div id="reg-success" class="success-msg"></div>
  <div class="form-grid" style="grid-template-columns:1fr;">
    <div class="form-group">
      <label>Nombre completo *</label>
      <input id="rNombre" type="text" placeholder="Juan Pérez" />
    </div>
    <div class="form-group">
      <label>Nombre de la finca</label>
      <input id="rFinca" type="text" placeholder="Finca La Esperanza" />
    </div>
    <div class="form-group">
      <label>Correo electrónico *</label>
      <input id="rCorreo" type="email" placeholder="ganadero@correo.com" />
    </div>
    <div class="form-group">
      <label>Contraseña * (mínimo 6 caracteres)</label>
      <input id="rClave" type="password" placeholder="••••••••" />
    </div>
    <div class="form-group">
      <button class="btn btn-primary" style="width:100%;justify-content:center;" onclick="registerUser()">
        Crear cuenta
      </button>
    </div>
  </div>
</div>

<div id="toast"></div>
<script src="script/data.js"></script>
<script src="script/login.js"></script>
</body>
</html>
