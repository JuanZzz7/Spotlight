<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Restablecer contraseña — Spotlight</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background-color: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #1e293b;
    -webkit-font-smoothing: antialiased;
  }
  .wrapper {
    max-width: 580px;
    margin: 40px auto;
    padding: 0 16px;
  }
  .card {
    background-color: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(127, 29, 63, 0.08);
    border: 1px solid #e2e8f0;
  }
  .header {
    background: linear-gradient(135deg, #7F1D3F 0%, #5A122B 100%);
    padding: 36px 32px 30px;
    text-align: center;
  }
  .brand-title {
    color: #ffffff;
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.02em;
    margin-bottom: 4px;
    font-family: 'Outfit', sans-serif, -apple-system;
  }
  .brand-subtitle {
    color: rgba(255, 255, 255, 0.85);
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }
  .body {
    padding: 36px 36px 28px;
  }
  .salutation {
    font-size: 17px;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 14px;
  }
  .text {
    font-size: 14px;
    line-height: 1.65;
    color: #334155;
    margin-bottom: 24px;
  }
  .btn-container {
    text-align: center;
    margin: 32px 0;
  }
  .btn-reset {
    display: inline-block;
    background: #7F1D3F;
    color: #ffffff !important;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    padding: 14px 34px;
    border-radius: 9999px;
    box-shadow: 0 4px 15px rgba(127, 29, 63, 0.3);
  }
  .security-box {
    background-color: #fdf2f4;
    border-left: 3px solid #7F1D3F;
    border-radius: 6px;
    padding: 14px 16px;
    margin-top: 24px;
    margin-bottom: 20px;
  }
  .security-title {
    font-size: 12px;
    font-weight: 700;
    color: #7F1D3F;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 4px;
  }
  .security-text {
    font-size: 13px;
    line-height: 1.55;
    color: #641c2c;
    margin: 0;
  }
  .subtext {
    font-size: 12px;
    color: #64748b;
    line-height: 1.6;
    word-break: break-all;
    margin-top: 20px;
  }
  .subtext a {
    color: #7F1D3F;
    text-decoration: underline;
  }
  .divider {
    border: 0;
    border-top: 1px solid #e2e8f0;
    margin: 24px 0;
  }
  .footer {
    background-color: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 24px 32px;
    text-align: center;
  }
  .footer-text {
    font-size: 12px;
    line-height: 1.6;
    color: #64748b;
    margin-bottom: 8px;
  }
  .footer-sub {
    font-size: 11px;
    color: #94a3b8;
  }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <!-- Header Corporativo Spotlight -->
    <div class="header">
      <div class="brand-title">SPOTLIGHT</div>
      <div class="brand-subtitle">Recuperación de Contraseña</div>
    </div>

    <!-- Contenido Principal -->
    <div class="body">
      <div class="salutation">Hola, {{ $userName }}</div>
      <p class="text">
        Estás recibiendo este correo porque hemos recibido una solicitud para restablecer la contraseña de tu cuenta en Spotlight.
      </p>

      <!-- Botón de Acción -->
      <div class="btn-container">
        <a href="{{ $resetUrl }}" target="_blank" class="btn-reset">
          Restablecer mi contraseña
        </a>
      </div>

      <p class="text" style="font-size: 13px; color: #64748b; text-align: center;">
        Este enlace de restablecimiento expirará en <strong>{{ $expire }} minutos</strong>.
      </p>

      <!-- Aviso de Seguridad -->
      <div class="security-box">
        <div class="security-title">¿No solicitaste este cambio?</div>
        <p class="security-text">
          Si no has solicitado restablecer tu contraseña, no es necesario realizar ninguna acción; tu cuenta y contraseña actual permanecen completamente seguras.
        </p>
      </div>

      <hr class="divider">

      <p class="subtext">
        Si tienes problemas al hacer clic en el botón "Restablecer mi contraseña", copia y pega la siguiente URL en tu navegador web:<br>
        <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
      </p>

      <p class="text" style="margin-top: 24px; margin-bottom: 0; font-size: 13px; color: #64748b;">
        Atentamente,<br>
        <strong style="color: #7F1D3F;">El equipo de Spotlight</strong>
      </p>
    </div>

    <!-- Pie de Correo -->
    <div class="footer">
      <p class="footer-text">
        Este es un mensaje automático de seguridad enviado por la plataforma Spotlight.<br>
        Por favor, no respondas directamente a este correo.
      </p>
      <p class="footer-sub">
        &copy; {{ date('Y') }} Spotlight. Todos los derechos reservados.
      </p>
    </div>
  </div>
</div>
</body>
</html>
