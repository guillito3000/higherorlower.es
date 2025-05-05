<?php
session_start();

// Conexión a la base de datos (IONOS)
$conn = new mysqli("db5017618870.hosting-data.io", "dbu2487898", "JaviMendezPC02", "dbs14100912");
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $terms = isset($_POST['terms']) ? 1 : 0;

    if ($full_name && $email && $password && $terms) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_check = "SELECT * FROM usuarios WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error_message = "El correo electrónico ya está registrado.";
        } else {
            $sql = "INSERT INTO usuarios (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $full_name, $email, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['username'] = $full_name;
                header("Location: index.php"); // redirección corregida
                exit();
            } else {
                $error_message = "Error al registrar: " . $conn->error;
            }
            $stmt->close();
        }
        $stmt_check->close();
    } else {
        $error_message = "Completa todos los campos y acepta los términos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: linear-gradient(-45deg, #0f0c29, #302b63, #24243e, #0f0c29);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      color: white;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
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
      margin-top: 120px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .register-box {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      width: 400px;
      text-align: center;
    }

    .register-box h2 {
      margin-bottom: 25px;
      font-size: 26px;
      color: #00ffd5;
    }

    .register-box input {
      width: 100%;
      padding: 12px;
      margin: 12px 0;
      border-radius: 6px;
      border: none;
      font-size: 16px;
    }

    .register-box input[type="text"],
    .register-box input[type="email"],
    .register-box input[type="password"] {
      background-color: #fff;
      color: #111;
    }

    .register-box label {
      font-size: 14px;
      display: block;
      margin-top: 10px;
      color: #ccc;
    }

    .register-box label a {
      color: #00ffd5;
      text-decoration: none;
    }

    .register-box label a:hover {
      text-decoration: underline;
    }

    .register-box button {
      background: #00ffd5;
      color: #111;
      font-weight: bold;
      padding: 12px 25px;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      cursor: pointer;
      box-shadow: 0 0 15px #00ffd5;
      margin-top: 10px;
    }

    .register-box button:hover {
      box-shadow: 0 0 25px #00ffd5, 0 0 40px #00ffd5;
    }

    .error-message {
      color: #ff5252;
      margin-top: 10px;
    }
  </style>
</head>
<body>
<nav>
  <ul>
    <li><a href="index.php">Home</a></li> <!-- corregido -->
    <li><a href="juegos.php">Juegos</a></li>
    <li><a href="ranking.php">Ranking</a></li>
    <li><a href="login.php">Iniciar Sesión</a></li>
    <li><a href="register.php">Registrarse</a></li>
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
        <form action="../php/logout.php" method="POST">
          <button class="logout-button" type="submit">Cerrar Sesión</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main>
  <div class="register-box">
    <h2>Registrarse</h2>
    <form action="" method="POST">
      <input type="text" name="full_name" placeholder="Nombre completo" required>
      <input type="email" name="email" placeholder="Correo electrónico" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <label><input type="checkbox" name="terms" required> Acepto los <a href="#">términos y condiciones</a></label>
      <button type="submit">Registrarse</button>
    </form>
    <?php if (isset($error_message)) { ?>
      <p class="error-message"><?php echo $error_message; ?></p>
    <?php } ?>
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