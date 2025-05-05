<?php
session_start();
$conn = new mysqli("db5017618870.hosting-data.io", "dbu2487898", "JaviMendezPC02", "dbs14100912");
if ($conn->connect_error) die("Error de conexión");

$query = "SELECT username, puntuacion_maxima FROM estadisticas ORDER BY puntuacion_maxima DESC";
$res = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ranking - Higher or Lower</title>
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
    }
    .user-info.open .dropdown {
      display: block;
    }
    .dropdown button, .dropdown a {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 16px;
      font-size: 14px;
      border: none;
      border-radius: 20px;
      color: #111;
      background-color: #00ffd5;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
    }
    main {
      margin-top: 100px;
      padding: 20px;
      text-align: center;
    }
    h1 {
      font-size: 2.5em;
      margin-bottom: 30px;
      color: white;
      text-shadow: 0 0 15px #00cfff;
    }
    .ranking-container {
      max-width: 800px;
      margin: 0 auto;
    }
    .ranking-entry {
      background: rgba(255,255,255,0.05);
      padding: 20px;
      margin: 15px 0;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,255,213,0.15);
      display: flex;
      justify-content: space-between;
      font-size: 18px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .ranking-entry:hover {
      transform: scale(1.02);
      box-shadow: 0 0 30px rgba(0,255,213,0.4);
    }
    .ranking-entry span:first-child {
      font-weight: bold;
      color: #00ffd5;
    }
  </style>
</head>
<body>
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
        <a href="editar_perfil.php">Editar perfil</a>
        <form action="../php/logout.php" method="POST" style="margin-top:10px;">
          <button type="submit">Cerrar Sesión</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main>
  <h1>🏆 Ranking de Jugadores</h1>
  <div class="ranking-container">
    <?php $pos = 1; while($row = $res->fetch_assoc()): ?>
      <div class="ranking-entry">
        <span>#<?php echo $pos++; ?> - <?php echo htmlspecialchars($row['username']); ?></span>
        <span><?php echo $row['puntuacion_maxima']; ?> pts</span>
      </div>
    <?php endwhile; ?>
  </div>
</main>

<script>
  const userMenu = document.getElementById('user-menu');
  userMenu.addEventListener('click', () => {
    userMenu.classList.toggle('open');
  });
</script>

</body>
</html>