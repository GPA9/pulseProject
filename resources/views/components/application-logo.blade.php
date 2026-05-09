<div class="logo" onclick="window.location.href='{{ url('/') }}'">
    <span class="pulse glow">PULSE</span>
    <span class="pulse main">PULSE</span>
</div>

<!-- Estilos del logo -->
<style>
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
        font-family: 'Lacquer', cursive; /* fuente aplicada */
    }

    /* Texto trasero → glow */
    .pulse.glow {
        font-size: 100px;
        color: #ffffff;
        filter: blur(8px);
        opacity: 0.95;
    }

    /* Texto delantero → principal */
    .pulse.main {
        font-size: 85px;
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
