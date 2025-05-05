<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Higher or Lower - Futurista</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, sans-serif;
      color: white;
      background: linear-gradient(-45deg, #0f0c29, #302b63, #24243e, #0f0c29);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      overflow-x: hidden;
    }
    @keyframes gradientBG {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }
    nav {
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(8px);
      padding: 15px 30px;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    nav ul {
      list-style: none;
      display: flex;
      gap: 25px;
      padding: 0;
      margin: 0;
    }
    nav ul li a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      font-size: 18px;
      transition: 0.3s;
    }
    nav ul li a:hover {
      color: #00ffd5;
      text-shadow: 0 0 10px #00ffd5;
    }
    .user-info {
      display: flex;
      align-items: center;
      position: relative;
      cursor: pointer;
    }
    .user-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
      border: 2px solid white;
    }
    .dropdown {
      display: none;
      position: absolute;
      background-color: rgba(0, 0, 0, 0.95);
      color: white;
      top: 60px;
      right: 0;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px #00ffd5;
      text-align: center;
      min-width: 180px;
    }
    .user-info.open .dropdown {
      display: block;
    }
    .edit-profile-link {
      display: inline-block;
      margin: 10px 0;
      background-color: #00ffd5;
      color: #111;
      padding: 8px 15px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }
    .edit-profile-link:hover {
      background-color: #00e6c1;
    }
    .start-btn {
      margin-top: 30px;
      padding: 15px 40px;
      font-size: 18px;
      font-weight: bold;
      border: none;
      border-radius: 30px;
      background: #00ffd5;
      color: #111;
      box-shadow: 0 0 20px #00ffd5;
      cursor: pointer;
      transition: 0.3s ease;
    }
    .start-btn:hover {
      box-shadow: 0 0 40px #00ffd5, 0 0 80px #00ffd5;
      transform: scale(1.05);
    }
    header.hero {
      padding: 160px 20px 100px;
      text-align: center;
      position: relative;
      z-index: 2;
    }
    header.hero h1 {
      font-size: 3.5em;
      margin-bottom: 20px;
      background: linear-gradient(to right, #00ffd5, #0099ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    header.hero p {
      font-size: 20px;
      color: #ccc;
      max-width: 700px;
      margin: auto;
    }
    .section-title {
      text-align: center;
      font-size: 2.2em;
      margin: 60px 0 20px;
      background: linear-gradient(to right, #00ffd5, #00bfff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .columns {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      padding: 0 40px 60px;
    }
    .card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(8px);
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      padding: 30px 20px;
      width: 280px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 0 30px #00ffd588;
    }
    .card h3 {
      margin-bottom: 10px;
      font-size: 1.4em;
      color: #00ffd5;
    }
    .card p {
      font-size: 16px;
      color: #eee;
    }
    .card small {
      color: #bbb;
      font-size: 13px;
    }
  </style>
</head>
<body>

<!-- MENÚ -->
<nav>
<ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="juegos.php">Juegos</a></li>
    <li><a href="ranking.php">Ranking</a></li>
    <?php if (isset($_SESSION['username'])): ?>
      <li><a href="estadisticas.php">Estadísticas</a></li>
    <?php else: ?>
      <li><a href="login.php">Iniciar Sesión</a></li>
      <li><a href="register.php">Registrarse</a></li>
    <?php endif; ?>
  <li><a href="HOME-MOVIL.php" title="Versión móvil">📲</a></li>
  </ul>
  
  <div class="user-info" id="user-menu">
    <?php
    $userIcon = $_SESSION['username'] ? "https://picsum.photos/40?random=" . rand(1, 1000) : "https://via.placeholder.com/40";
    $userName = $_SESSION['username'] ? $_SESSION['username'] : "Invitado";
    ?>
    <img src="<?php echo $userIcon; ?>" alt="User Icon">
    <div class="dropdown">
      <p><?php echo $userName; ?></p>
      <?php if ($_SESSION['username']) : ?>
        <a href="editar_perfil.php" class="edit-profile-link">Editar perfil</a>
        <form action="/php/logout.php" method="POST">
          <button class="start-btn" style="font-size:14px;padding:8px 20px;">Cerrar Sesión</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</nav>

<script>
  const userMenu = document.getElementById('user-menu');
  userMenu.addEventListener('click', () => {
    userMenu.classList.toggle('open');
  });
</script>

<!-- HERO -->
<header class="hero">
  <h1>Desafía tu intuición social</h1>
  <p>¿Quién tiene más seguidores? Acierta, suma puntos y escala al top.</p>
  <a href="juegos.php"><button class="start-btn">¡Jugar ahora!</button></a>
</header>

<!-- SECCIONES -->
<section>
  <h2 class="section-title">🎯 ¿Por qué jugar a Higher or Lower?</h2>
  <div class="columns">
    <div class="card"><h3>📱 Datos reales</h3><p>Seguidores actualizados de redes sociales.</p></div>
    <div class="card"><h3>🔥 Reto viral</h3><p>Compite con amigos en el ranking global.</p></div>
    <div class="card"><h3>⚡ Instantáneo</h3><p>Juega sin esperar. Rápido y divertido.</p></div>
  </div>
</section>

<section>
  <h2 class="section-title">🎮 Mini demo visual</h2>
  <div class="columns">
    <div class="card"><h3>Famoso A</h3><p>Charli D’Amelio<br><strong>100M seguidores</strong></p></div>
    <div class="card"><h3>VS</h3><p><strong>¿Más o menos?</strong></p></div>
    <div class="card"><h3>Famoso B</h3><p>Cristiano Ronaldo<br><strong>??? seguidores</strong></p></div>
  </div>
</section>

<section>
  <h2 class="section-title">🧪 Tecnología utilizada</h2>
  <div class="columns">
    <div class="card"><h3>HTML & CSS</h3><p>Diseño responsive y moderno</p></div>
    <div class="card"><h3>PHP & MySQL</h3><p>Usuarios, estadísticas, rankings</p></div>
    <div class="card"><h3>JavaScript</h3><p>Efectos, lógica y dinámicas</p></div>
  </div>
</section>

<section>
  <h2 class="section-title">🗣️ Opiniones</h2>
  <div class="columns">
    <div class="card"><h3>⭐⭐⭐⭐⭐</h3><p>"¡Muy adictivo y divertido!"</p><small>- Laura23</small></div>
    <div class="card"><h3>⭐⭐⭐⭐</h3><p>"Buen reto para pasar el rato."</p><small>- DaniYT</small></div>
    <div class="card"><h3>⭐⭐⭐⭐⭐</h3><p>"Me encanta el diseño."</p><small>- CodeMaster</small></div>
  </div>
</section>

<section>
  <h2 class="section-title">🏆 Top 3 del Ranking</h2>
  <div class="columns">
    <?php
    $conn = new mysqli("db5017618870.hosting-data.io", "dbu2487898", "JaviMendezPC02", "dbs14100912");
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $query = "SELECT username, puntuacion_maxima FROM estadisticas ORDER BY puntuacion_maxima DESC LIMIT 3";
    $res = $conn->query($query);
    while ($row = $res->fetch_assoc()):
    ?>
      <div class="card">
        <h3><?php echo htmlspecialchars($row['username']); ?></h3>
        <p>Puntuación: <?php echo $row['puntuacion_maxima']; ?></p>
      </div>
    <?php endwhile; $conn->close(); ?>
  </div>
</section>

<section>
  <h2 class="section-title">👨‍💻 Equipo de desarrollo</h2>
  <div class="columns">
    <div class="card"><h3>Oscar Méndez</h3><p>Frontend & HTML</p></div>
    <div class="card"><h3>Javier Garrido</h3><p>Base de datos & PHP</p></div>
    <div class="card"><h3>Guillermo Nieto</h3><p>Lógica del juego & JS</p></div>
  </div>
</section>

<footer style="background:#111;text-align:center;padding:20px;color:#aaa;font-size:14px;">
  <p>&copy; <?php echo date("Y"); ?> Higher or Lower — Proyecto realizado con esfuerzo y dedicación.</p>
  <p><a href="aviso_legal.php" style="color: #00ffd5; text-decoration: underline;">Aviso Legal y Cookies</a></p>
</footer>

<div id="cookie-popup" style="display:none; position: fixed; bottom: 20px; left: 20px; right: 20px; background: rgba(0,0,0,0.85); color: white; padding: 20px; border-radius: 10px; z-index: 9999; box-shadow: 0 0 10px #00ffd5; text-align: center;">
  <p>🍪 Usamos cookies para mejorar tu experiencia en <strong>Higher or Lower</strong>. Puedes <a href="aviso_legal.php" style="color: #00ffd5; text-decoration: underline;">leer más aquí</a>.</p>
  <button onclick="aceptarCookies()" style="margin: 10px; padding: 10px 20px; background-color: #00ffd5; color: #000; border: none; border-radius: 8px; cursor: pointer;">Aceptar</button>
  <button onclick="denegarCookies()" style="margin: 10px; padding: 10px 20px; background-color: #ff5252; color: #fff; border: none; border-radius: 8px; cursor: pointer;">Denegar</button>
</div>

<script>
  window.addEventListener('DOMContentLoaded', () => {
    const aceptadas = localStorage.getItem('cookies_aceptadas');
    if (!aceptadas) {
      document.getElementById('cookie-popup').style.display = 'block';
    }
  });

  function aceptarCookies() {
    localStorage.setItem('cookies_aceptadas', 'true');
    document.getElementById('cookie-popup').style.display = 'none';
  }

  function denegarCookies() {
    localStorage.setItem('cookies_aceptadas', 'false');
    document.getElementById('cookie-popup').style.display = 'none';
    alert("Has denegado las cookies. Algunas funciones pueden no estar disponibles.");
  }
</script>

</body>
</html>