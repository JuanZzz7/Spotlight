<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Código de verificación de seguridad — Spotlight</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background-color: #FFFCF7;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #29252A;
    -webkit-font-smoothing: antialiased;
  }
  .wrapper {
    max-width: 580px;
    margin: 40px auto;
    padding: 0 16px;
  }
  .card {
    background-color: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 12px 30px rgba(127, 29, 63, 0.08);
    border: 1px solid #EEDDDC;
  }
  .header {
    background: linear-gradient(135deg, #7F1D3F 0%, #5A122B 100%);
    padding: 38px 32px 32px;
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
    font-weight: 700;
    color: #29252A;
    margin-bottom: 14px;
  }
  .text {
    font-size: 14px;
    line-height: 1.65;
    color: #484249;
    margin-bottom: 24px;
  }
  .code-container {
    background-color: #FAF0F2;
    border: 1.5px solid #EEDDDC;
    border-radius: 16px;
    text-align: center;
    padding: 28px 20px;
    margin: 28px 0;
  }
  .code-title {
    font-size: 12px;
    font-weight: 700;
    color: #736B75;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    margin-bottom: 12px;
  }
  .code-value {
    font-size: 42px;
    font-weight: 800;
    letter-spacing: 0.22em;
    color: #7F1D3F;
    line-height: 1.1;
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
  }
  .code-info {
    font-size: 12px;
    color: #736B75;
    margin-top: 12px;
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
  .divider {
    border: 0;
    border-top: 1px solid #EEDDDC;
    margin: 24px 0;
  }
  .footer {
    background-color: #FFFCF7;
    border-top: 1px solid #EEDDDC;
    padding: 24px 32px;
    text-align: center;
  }
  .footer-text {
    font-size: 12px;
    line-height: 1.6;
    color: #736B75;
    margin-bottom: 8px;
  }
  .footer-sub {
    font-size: 11px;
    color: #9E96A0;
  }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <!-- Header Corporativo -->
    <div class="header">
      <div class="brand-title">SPOTLIGHT</div>
      <div class="brand-subtitle">Servicio de Autenticación y Seguridad</div>
    </div>

    <!-- Contenido Principal -->
    <div class="body">
      <div class="salutation">Estimado/a {{ $userName }},</div>
      <p class="text">
        Hemos recibido una solicitud de acceso o registro vinculada a tu cuenta en la plataforma. Para verificar tu identidad y continuar de manera segura, ingresa el siguiente código de autorización:
      </p>

      <!-- Bloque del Código -->
      <div class="code-container">
        <div class="code-title">Código de verificación</div>
        <div class="code-value">{{ $code }}</div>
        <div class="code-info">Código de un solo uso válido durante los próximos <strong>10 minutos</strong>.</div>
      </div>

      <!-- Aviso de Seguridad -->
      <div class="security-box">
        <div class="security-title">Aviso de seguridad</div>
        <p class="security-text">
          Por razones de seguridad, nunca compartas este código con nadie. El equipo de Spotlight jamás te solicitará este código por ningún medio. Si tú no has realizado esta solicitud, puedes desestimar este mensaje con total tranquilidad.
        </p>
      </div>

      <hr class="divider">

      <p class="text" style="margin-bottom: 0; font-size: 13px; color: #736B75;">
        Atentamente,<br>
        <strong style="color: #7F1D3F;">Equipo de Seguridad de Spotlight</strong>
      </p>
    </div>

    <!-- Pie de Correo -->
    <div class="footer">
      <p class="footer-text">
        Este mensaje ha sido generado de forma automática por el sistema de autenticación de Spotlight.<br>
        Por favor, no respondas directamente a este correo electrónico.
      </p>
      <p class="footer-sub">
        &copy; {{ date('Y') }} Spotlight. Todos los derechos reservados.
      </p>
    </div>
  </div>
</div>
</body>
</html>
