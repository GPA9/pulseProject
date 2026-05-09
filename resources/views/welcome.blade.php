<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PULSE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fuente Lacquer -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lacquer&display=swap" rel="stylesheet">

    <style>
        html, body {
            margin: 0;
            width: 100%;
            height: 100%;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #888888; /* gris elegante */
            font-family: 'Lacquer', cursive;
            overflow: hidden;
        }

        .logo {
            position: relative;
            cursor: pointer; /* indica interacción */
        }

        .pulse {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            user-select: none;
            white-space: nowrap;
            transition: transform 0.3s ease; /* transición suave al hover */
        }

        /* Texto trasero → glow */
        .pulse.glow {
            font-size: 180px;
            color: #ffffff;
            filter: blur(8px);
            opacity: 0.95;
        }

        /* Texto delantero → principal */
        .pulse.main {
            font-size: 155px;
            color: #111111;
        }

        /* Hover: aumenta ligeramente tamaño */
        .logo:hover .pulse.main {
            transform: translate(-50%, -50%) scale(1.05);
        }

        .logo:hover .pulse.glow {
            transform: translate(-50%, -50%) scale(1.07);
        }
    </style>
</head>
<body>

    <div class="logo" onclick="window.location.href='/login'">
        <span class="pulse glow">PULSE</span>
        <span class="pulse main">PULSE</span>
    </div>

</body>
</html>
