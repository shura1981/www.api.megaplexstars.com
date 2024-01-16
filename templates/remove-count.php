<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App taller flutter</title>
    <meta name="description" content="App taller flutter">

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .container {
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        input,
        button {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 80%;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>App taller flutter</h1>
        <p>En esta página puedes eliminar tu cuenta</p>
        <form>
            <input type="email" id="email" required placeholder="Correo electrónico">
            <br>
            <button>Eliminar Cuenta</button>
        </form>
    </div>

    <script>
        const form = document.querySelector("form");
        const button = form.querySelector("button");
        const desactivarBoton = () => {
            button.setAttribute("disabled", true);
            button.innerHTML = "Eliminando cuenta...";
        }
        const activarBoton = () => {
            button.removeAttribute("disabled")
            button.innerHTML = "Eliminar Cuenta";
        };

        async function eliminarCuenta() {
            // Aquí deberías agregar la lógica para eliminar la cuenta
            // obtener el campo correo del formulario
            const email = form.querySelector("#email").value;
            try {
                desactivarBoton();
                const response = await fetch("https://www.api.megaplexstars.com/api/remove-count", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Y2tfZGJjMDI5ZTA2ZWJmZTdmNjg5YjJmZTRiOGJkNzhjNWEyNzlhN2IxYjpjc180ODhjOTNjOTlhOTE3OTc4NzU4N2Y0NmIzYmIyNWZkYzNmYzdlZDBj"
                    },
                    body: JSON.stringify({
                        email
                    })
                });
                const data = await response.json();
                console.log(data);
                alert("Ahora tu cuenta ha sido removida de nuestra base de datos.");
            } catch (error) {
                alert("Ocurrió un error al eliminar tu cuenta.");
            } finally {
                activarBoton();
            }


        }


        form.addEventListener("submit", async function (event) {
            event.preventDefault();
            eliminarCuenta();

        });




    </script>
</body>

</html>