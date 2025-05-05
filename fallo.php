<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$ultimo_score = $_SESSION['last_score'] ?? 0;
unset($_SESSION['last_score']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Has fallado!</title>
    <style>
        body {
            background-color: #111;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }

        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: #ff4d4d;
        }

        p {
            font-size: 24px;
            margin-bottom: 40px;
        }

        a button {
            background-color: #6a11cb;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            margin: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        a button:hover {
            background-color: #5015a3;
        }
    </style>
</head>
<body>
    <h1>¡Has fallado! 😢</h1>
    <p>Tu puntuación fue: <strong><?php echo $ultimo_score; ?></strong></p>
    <a href="juegos.php"><button>Volver a jugar</button></a>
    <a href="index.php"><button>Volver al inicio</button></a>
</body>
</html>
