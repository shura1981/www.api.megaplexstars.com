<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login xx</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #dcdcdc8c;
            min-height: 100vh;
            font-family: system-ui;
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 1rem;
            padding: 1rem;
            width: 100%;
            justify-content: center;
            margin: auto;
        }

        @media(min-width:896px) {
            body {
                max-width: 900px;
            }
        }

        form {
            display: grid;
            display: grid;
            grid-auto-rows: max-content;
            row-gap: 1rem;
        }

        .label {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 0.5rem;
        }
    </style>
</head>

<body>
    <h1>Inicia Sessión</h1>
    <form action="<?= htmlspecialchars($url) ?>login" method="GET">
        <p> ruta del servidor
            <?php if (isset($error)): ?>
                <?= htmlspecialchars($error) ?>
            <?php endif; ?>

        </p>
        <label class="label">
            Correo
            <input type="email" name="email" placeholder="example@gmail.com">
        </label>

        <label class="label">
            Contraseña
            <input type="password" name="pass" placeholder="*******">
        </label>

        <button type="submit" name="ingresar">Ingresar</button>
        <a href="<?= htmlspecialchars($url) ?>registre">Registrarse</a>

    </form>



</body>

</html>