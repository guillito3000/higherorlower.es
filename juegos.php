<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['current_score'])) {
    $_SESSION['current_score'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['p1'], $_POST['p2'])) {
    $action = $_POST['action'];
    $p1 = json_decode($_POST['p1'], true);
    $p2 = json_decode($_POST['p2'], true);

    $correct = false;
    if ($action === 'higher' && $p2['followers'] >= $p1['followers']) $correct = true;
    if ($action === 'lower' && $p2['followers'] < $p1['followers']) $correct = true;

    $username = $_SESSION['username'];
    $score = $_SESSION['current_score'];

    $conn = new mysqli("db5017618870.hosting-data.io", "dbu2487898", "JaviMendezPC02", "dbs14100912");
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $check = $conn->prepare("SELECT 1 FROM estadisticas WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO estadisticas (username) VALUES (?)");
        $insert->bind_param("s", $username);
        $insert->execute();
        $insert->close();
    }
    $check->close();

    if ($correct) {
        $_SESSION['current_score']++;
        $stmt = $conn->prepare("UPDATE estadisticas SET aciertos = aciertos + 1 WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['score' => $_SESSION['current_score'], 'result' => 'correct', 'followers' => $p2['followers']]);
    } else {
        $_SESSION['last_score'] = $_SESSION['current_score'];
        $stmt = $conn->prepare("UPDATE estadisticas SET partidas_jugadas = partidas_jugadas + 1, fallos = fallos + 1, puntuacion_maxima = IF(? > puntuacion_maxima, ?, puntuacion_maxima) WHERE username = ?");
        $stmt->bind_param("iis", $score, $score, $username);
        $stmt->execute();
        $stmt->close();
        $_SESSION['current_score'] = 0;
        echo json_encode(['result' => 'fail', 'redirect' => 'fallo.php', 'followers' => $p2['followers']]);
    }

    $conn->close();
    exit();
}

$personajes_json = file_get_contents("json/FAMOSOS/famosos_formato_foto_actualizado.json");
$personajes_array = json_decode($personajes_json, true);
$personajes_validos = array_filter($personajes_array, function($p) {
    return !empty($p['img']) && !empty($p['name']) && !empty($p['followers']) && !empty($p['platform']);
});
$personajes_unicos = array_values($personajes_validos);
if (count($personajes_unicos) < 2) die("No hay suficientes personajes válidos.");
$seleccionados = array_rand($personajes_unicos, 2);
$p1 = $personajes_unicos[$seleccionados[0]];
$p2 = $personajes_unicos[$seleccionados[1]];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Juego - Higher or Lower</title>
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
      overflow: hidden;
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
    .game-container {
      display: flex;
      width: 100%;
      height: calc(100vh - 70px);
      padding-top: 80px;
      gap: 10px;
    }
    .half {
      width: 50%;
      height: 100%;
      position: relative;
    }
    .half img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.5);
    }
    .overlay {
      position: absolute;
      bottom: 15px;
      width: 100%;
      text-align: center;
      color: white;
      text-shadow: 1px 1px 6px rgba(0,0,0,0.8);
    }
    .overlay .name {
      font-size: 32px;
      font-weight: bold;
    }
    .overlay .platform {
      font-size: 26px;
      color: #00ffd5;
    }
    .overlay .followers {
      font-size: 30px;
      margin-top: 8px;
      font-weight: bold;
      transition: all 0.4s ease;
    }
    .center-buttons {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      z-index: 10;
    }
    .center-buttons button {
      background: linear-gradient(to right, #00ffd5, #0099ff);
      color: #111;
      font-size: 22px;
      font-weight: bold;
      padding: 12px 28px;
      margin: 12px;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      box-shadow: 0 0 15px #00ffd588;
      transition: all 0.3s ease;
    }
    .center-buttons button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 30px #00ffd5;
    }
    .score {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 24px;
      color: white;
      font-weight: bold;
      text-shadow: 0 0 10px #00bfff;
    }
    #animatedScore {
      position: absolute;
      top: 50%;
      left: 50%;
      font-size: 50px;
      color: lime;
      font-weight: bold;
      transform: translate(-50%, -50%);
      display: none;
      z-index: 1000;
      text-shadow: 2px 2px 10px black;
      transition: all 0.6s ease;
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
</nav>

<div class="game-container">
  <div class="half">
    <img src="json/FAMOSOS/<?php echo htmlspecialchars($p1['img']); ?>" alt="<?php echo htmlspecialchars($p1['name']); ?>">
    <div class="overlay">
      <div class="name"><?php echo htmlspecialchars($p1['name']); ?></div>
      <div class="platform"><?php echo htmlspecialchars($p1['platform']); ?></div>
      <div class="followers"><?php echo number_format($p1['followers']); ?> seguidores</div>
    </div>
  </div>

  <div class="half">
    <img src="json/FAMOSOS/<?php echo htmlspecialchars($p2['img']); ?>" alt="<?php echo htmlspecialchars($p2['name']); ?>">
    <div class="overlay">
      <div class="name"><?php echo htmlspecialchars($p2['name']); ?></div>
      <div class="platform"><?php echo htmlspecialchars($p2['platform']); ?></div>
      <div class="followers" id="followersP2">???</div>
    </div>
  </div>

  <div class="center-buttons">
    <button onclick='checkAnswer("higher")'>Higher</button><br>
    <button onclick='checkAnswer("lower")'>Lower</button>
  </div>

  <div class="score">Score: <?php echo $_SESSION['current_score']; ?></div>
</div>

<audio id="winSound" src="sounds/win.mp3" preload="auto"></audio>
<audio id="loseSound" src="sounds/lose.mp3" preload="auto"></audio>
<div id="animatedScore">+1</div>

<script>
const p1 = <?php echo json_encode($p1); ?>;
const p2 = <?php echo json_encode($p2); ?>;

function animateFollowers(finalValue) {
  let count = 0;
  const increment = Math.ceil(finalValue / 100);
  const interval = setInterval(() => {
    count += increment;
    if (count >= finalValue) {
      count = finalValue;
      clearInterval(interval);
    }
    document.getElementById('followersP2').textContent = count.toLocaleString() + " seguidores";
  }, 20);
}

function playSound(type) {
  const win = document.getElementById("winSound");
  const lose = document.getElementById("loseSound");
  if (type === "win") win.play();
  else lose.play();
}

function animateScore() {
  const elem = document.getElementById("animatedScore");
  elem.style.display = "block";
  elem.style.opacity = "1";
  elem.style.transform = "translate(-50%, -50%) scale(1.5)";
  setTimeout(() => {
    elem.style.opacity = "0";
    elem.style.transform = "translate(-50%, -70%) scale(0.5)";
  }, 100);
  setTimeout(() => {
    elem.style.display = "none";
  }, 1000);
}

function updateScore(newScore) {
  document.querySelector(".score").textContent = "Score: " + newScore;
}

function checkAnswer(choice) {
  fetch("juegos.php", {
    method: "POST",
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=${choice}&p1=${encodeURIComponent(JSON.stringify(p1))}&p2=${encodeURIComponent(JSON.stringify(p2))}`
  })
  .then(res => res.json())
  .then(data => {
    animateFollowers(data.followers);
    if (data.result === "correct") {
      playSound("win");
      animateScore();
      updateScore(data.score);
    } else {
      playSound("lose");
    }
    setTimeout(() => {
      if (data.redirect) {
        window.location.href = data.redirect;
      } else {
        location.reload();
      }
    }, 3000);
  });
}
</script>

</body>
</html>