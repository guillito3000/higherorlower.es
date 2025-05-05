<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Aviso Legal - Higher or Lower</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: linear-gradient(-45deg, #0f0c29, #302b63, #24243e, #0f0c29);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      color: white;
      padding: 30px;
    }

    @keyframes gradientBG {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }

    h1 {
      color: #00ffd5;
      text-shadow: 0 0 10px #00ffd5;
    }

    p, li {
      line-height: 1.6;
      font-size: 16px;
    }

    a {
      color: #00ffd5;
      text-decoration: underline;
    }

    .container {
      max-width: 800px;
      margin: auto;
    }

    ul {
      padding-left: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>📄 Aviso Legal</h1>
    
    <p><strong>1. Información del proyecto:</strong></p>
    <p>Este sitio web es un proyecto académico titulado <em>Higher or Lower</em>, creado con fines educativos y de aprendizaje por estudiantes de desarrollo web. No tiene fines comerciales ni lucrativos.</p>

    <p><strong>2. Propiedad intelectual:</strong></p>
    <p>Todo el contenido, imágenes, código y diseño están protegidos por derechos de autor de sus respectivos autores. Las imágenes de famosos se utilizan con fines ilustrativos sin intención de vulnerar derechos de imagen.</p>

    <p><strong>3. Política de Cookies:</strong></p>
    <p>Utilizamos cookies técnicas para mejorar la experiencia de usuario. Al continuar navegando o al aceptar el uso de cookies, estás dando tu consentimiento. Puedes desactivarlas desde la configuración de tu navegador.</p>

    <p><strong>4. Limitación de responsabilidad:</strong></p>
    <p>Este sitio no garantiza la exactitud de los datos de seguidores de los famosos y se ofrece "tal cual". No nos responsabilizamos por daños derivados del uso de la web.</p>

    <p><strong>5. Contacto:</strong></p>
    <p>Para cualquier duda o comentario, puedes escribirnos a <a href="mailto:higherorlower@gmail.com">higherorlower@gmail.com</a>.</p>

    <br>
    <a href="index.php">← Volver al inicio</a>
  </div>
</body>
</html>
