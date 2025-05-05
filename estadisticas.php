<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = null;
}

$host = "db5017618870.hosting-data.io";
$dbname = "dbs14100912";
$user = "dbu2487898";
$pass = "JaviMendezPC02";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$username = $_SESSION['username'];
// Estadísticas personales
$sql = "SELECT partidas_jugadas, aciertos, fallos, puntuacion_maxima FROM estadisticas WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();
$stmt->close();

$jugadas = $stats['partidas_jugadas'] ?? 0;
$aciertos = $stats['aciertos'] ?? 0;
$fallos = $stats['fallos'] ?? 0;
$mejor = $stats['puntuacion_maxima'] ?? 0;
$total = $aciertos + $fallos;
$porcentaje = ($total > 0) ? round(($aciertos / $total) * 100) : 0;

// Color del gráfico
if ($porcentaje >= 70) {
    $colorStroke = "url(#greenGradient)";
} elseif ($porcentaje >= 40) {
    $colorStroke = "url(#yellowGradient)";
} else {
    $colorStroke = "url(#redGradient)";
}

// Posición en el ranking
$ranking_sql = "SELECT username FROM estadisticas ORDER BY puntuacion_maxima DESC";
$ranking_res = $conn->query($ranking_sql);
$posicion = 0;
$actual = 1;
while($fila = $ranking_res->fetch_assoc()) {
    if ($fila["username"] === $_SESSION["username"]) {
        $posicion = $actual;
        break;
    }
    $actual++;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Estadísticas - Higher or Lower</title>
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
    .dashboard {
      margin-top: 40px;
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      justify-content: center;
    }
    .card {
      background: rgba(255,255,255,0.05);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 0 25px rgba(0,255,213,0.25);
      width: 280px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 0 35px rgba(0,255,213,0.4);
    }
    .card h3 {
      color: #00ffd5;
      margin-bottom: 10px;
    }
    .card p {
      font-size: 20px;
      font-weight: bold;
      color: #fff;
    }
    .circle-container {
      position: relative;
      width: 200px;
      height: 200px;
      margin: auto;
    }
    .circle-bg {
      stroke: #e6e6e6;
      fill: none;
      stroke-width: 15;
    }
    .circle-fill {
      fill: none;
      stroke-width: 15;
      stroke-linecap: round;
      filter: drop-shadow(0 0 6px rgba(0,0,0,0.3));
    }
    .circle-text {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 28px;
      font-weight: bold;
      color: #00ffd5;
    }
    .tooltip {
      position: absolute;
      background: #333;
      color: white;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 14px;
      transform: translate(-50%, -120%);
      display: none;
    }
    .circle-container:hover .tooltip {
      display: block;
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
    <?php endif; ?>
  </ul>
  <ul>
    <?php if (!isset($_SESSION['username'])): ?>
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
        <a href="editar_perfil.php">Editar perfil</a><br>
        <form action="../php/logout.php" method="POST" style="margin-top:10px;">
          <button type="submit">Cerrar Sesión</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main>
  <h1 style="font-size: 2.5em; margin-bottom: 30px; color: white; text-shadow: 0 0 15px #00cfff;">📊 Tus Estadísticas</h1>
  <p style="margin-bottom:30px;font-size:22px;color:#00ffd5;text-shadow:0 0 10px #00ffd5;">
    🥇 Tu posición en el ranking: <strong>#<?php echo $posicion; ?></strong>
  </p>

  <div class="dashboard">
    <div class="card"><h3>Partidas Jugadas</h3><p><?php echo $jugadas; ?></p></div>
    <div class="card"><h3>Mejor Puntuación</h3><p><?php echo $mejor; ?></p></div>
    <div class="card"><h3>Nº Aciertos</h3><p><?php echo $aciertos; ?></p></div>
    <div class="card"><h3>Nº Fallos</h3><p><?php echo $fallos; ?></p></div>

    <div class="card">
      <h3>Porcentaje de Aciertos</h3>
      <div class="circle-container">
        <svg width="200" height="200">
          <defs>
            <linearGradient id="greenGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#00e676"/>
              <stop offset="100%" stop-color="#00c853"/>
            </linearGradient>
            <linearGradient id="yellowGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#ffeb3b"/>
              <stop offset="100%" stop-color="#fbc02d"/>
            </linearGradient>
            <linearGradient id="redGradient" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#ff5252"/>
              <stop offset="100%" stop-color="#e53935"/>
            </linearGradient>
          </defs>
          <circle class="circle-bg" cx="100" cy="100" r="80"></circle>
          <circle id="circle" class="circle-fill" cx="100" cy="100" r="80"
                  stroke-dasharray="502" stroke-dashoffset="502"
                  stroke="<?php echo $colorStroke; ?>">
          </circle>
        </svg>
        <div id="percentText" class="circle-text">0%</div>
        <div class="tooltip">Porcentaje de aciertos sobre aciertos + fallos</div>
      </div>
    </div>
  </div>
</main>

<script>
  const userMenu = document.getElementById('user-menu');
  userMenu.addEventListener('click', () => {
    userMenu.classList.toggle('open');
  });

  window.addEventListener('DOMContentLoaded', () => {
    const circle = document.getElementById('circle');
    const text = document.getElementById('percentText');
    const target = <?php echo $porcentaje; ?>;
    const finalOffset = 502 - (502 * target / 100);

    let offset = 502;
    let displayed = 0;
    const steps = 60;
    const stepOffset = (502 - finalOffset) / steps;
    const stepText = target / steps;

    function animate() {
      offset -= stepOffset;
      displayed += stepText;

      if (offset <= finalOffset) {
        circle.setAttribute('stroke-dashoffset', finalOffset);
        text.textContent = target + "%";
      } else {
        circle.setAttribute('stroke-dashoffset', offset);
        text.textContent = Math.round(displayed) + "%";
        requestAnimationFrame(animate);
      }
    }

    animate();
  });
</script>
</body>
</html>