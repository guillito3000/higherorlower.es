<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$host = "db5017618870.hosting-data.io";
$db = "dbs14100912"; // Nombre de tu base de datos real
$user = "dbu2487898";
$pass = "JaviMendezPC02"; // Tu contraseña recién actualizada
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM usuarios WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
$userData = $res->fetch_assoc();
$stmt->close();

$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nuevoNombre = trim($_POST['nuevo_nombre']);
    $nuevoEmail = trim($_POST['nuevo_email']);
    $nuevaContrasena = trim($_POST['nueva_contrasena']);

    if ($nuevoNombre || $nuevoEmail || $nuevaContrasena) {
        if ($nuevoNombre) {
            $stmt = $conn->prepare("UPDATE usuarios SET username = ? WHERE username = ?");
            $stmt->bind_param("ss", $nuevoNombre, $username);
            $stmt->execute();
            $_SESSION['username'] = $nuevoNombre;
            $username = $nuevoNombre;
            $stmt->close();
        }

        if ($nuevoEmail) {
            $stmt = $conn->prepare("UPDATE usuarios SET email = ? WHERE username = ?");
            $stmt->bind_param("ss", $nuevoEmail, $username);
            $stmt->execute();
            $stmt->close();
        }

        if ($nuevaContrasena) {
            $hashed = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $hashed, $username);
            $stmt->execute();
            $stmt->close();
        }

        $mensaje = "Datos actualizados correctamente.";
    } else {
        $mensaje = "No has cambiado ningún dato.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
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

        main {
            padding-top: 120px;
            max-width: 500px;
            margin: auto;
            text-align: center;
        }

        form {
            background-color: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px #00ffd5;
        }

        form input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: none;
            font-size: 16px;
        }

        button {
            background-color: #00ffd5;
            color: black;
            font-weight: bold;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #00bfa5;
        }

        .mensaje {
            margin-top: 15px;
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
      <a href="editar_perfil.php" style="display:block; margin-bottom:10px; color: #00ffd5;">Editar perfil</a>
      <?php if ($_SESSION['username']) : ?>
        <form action="../php/logout.php" method="POST" style="display:inline;">
          <button class="logout-button" type="submit">Cerrar Sesión</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main>
    <h1>Editar Perfil</h1>
    <form method="POST">
        <input type="text" name="nuevo_nombre" placeholder="Nuevo nombre de usuario (opcional)">
        <input type="email" name="nuevo_email" placeholder="Nuevo correo (opcional)">
        <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña (opcional)">
        <button type="submit">Guardar Cambios</button>
        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo $mensaje; ?></div>
        <?php endif; ?>
    </form>
</main>

<script>
  const userMenu = document.getElementById('user-menu');
  userMenu.addEventListener('click', () => {
    userMenu.classList.toggle('open');
  });
</script>

</body>
</html>
