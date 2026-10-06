<html>
    <head>
        <title>Examen de Desarrollo web en entorno servidor</title>
        <link rel="icon" type="image/png" sizes="32x32" href="../imagenes/favicon.jpeg">
        <link rel="stylesheet" type="text/css" href="../estilos/estilos.css">
    </head>

    <body>

        <h1>Centro de Ayuda al Empleo</h1>

        <h2>
            <?php
            // Creo una variable que por defecto el tipo de profesión sera "Soldadura"
                $tipo = "Soldadura";

            //Comprobamos que profesion me ha llegado en la URL
            // Si en la URL pone informatica, cambiamos el tipo a Informática.
                if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] == 'informatica') {
                    $tipo = 'Informatica';
            // Si pone socio, cambiamos el tipo a Asistencia Sociosanitaria.        
                } else if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] == 'socio') {
                    $tipo = "Asistencia Sociosanitaria";
                }
            //De esta manera, mostramos un titulo correspondiente
                echo "Solicitudes de $tipo";
            ?>
        </h2>

        
        <?php 

        // CONEXIÓN CON LA BASE DE DATOS
        $conexion = mysqli_connect("localhost", "root", "", "CAE");

        if (!$conexion) {
            die("Error de conexión");
        }

        mysqli_set_charset($conexion, "utf8");


        // COMPRUEBO SI SE HA ENVIADO EL FORMULARIO
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (
                isset($_POST["nombre"]) &&
                isset($_POST["apellidos"]) &&
                isset($_POST["dni"]) &&
                isset($_POST["f_nac"]) &&
                isset($_POST["tlf"]) &&
                isset($_POST["email"]) &&
                isset($_POST["profesion"]) &&
                isset($_POST["jornadaParcial"])
            ) {

                $nombre = $_POST["nombre"];
                $apellidos = $_POST["apellidos"];
                $dni = $_POST["dni"];
                $f_nac = $_POST["f_nac"];
                $tlf = $_POST["tlf"];
                $email = $_POST["email"];
                $profesion = $_POST["profesion"];
                $jornadaParcial = $_POST["jornadaParcial"];


                // COMPROBAMOS LOS IDIOMAS
                //Creo la variable $idiomas vacia suponiendo que no se ha seleccionado
                // ningun idioma.
                $idiomas = "";

                //Si el usuario ha seleccionado la opcion "euskera", se guarda euskera en $idiomas.
                if (isset($_POST["euskera"])) {
                    $idiomas = "euskera";
                }

                //Si el usuario ha seleccionado la opcion "ingles", 
                //entramos dentro del if y comprobamos si el usuario ha marcado algo mas
                //o no,
                if (isset($_POST["ingles"])) {

                    //Si $idiomas no esta vacio, es decir, si ya tenia "euskera"...
                    if ($idiomas != "") {
                        //...entonces hacemos la siguiente linea que significa: añade ", ingles" a lo que ya habia
                        $idiomas = $idiomas . ", ingles";
                        //se convertiria en euskera, ingles
                    } else {
                        $idiomas = "ingles";
                    }
                }


                // Insertamos los datos dentro de la tabla solicitud de phpmyadmin
                $sql = "INSERT INTO SOLICITUD
                        (nombre, apellidos, dni, f_nac, tlf, email, profesion, jornadaParcial, idiomas)
                        VALUES
                        ('$nombre', '$apellidos', '$dni', '$f_nac', '$tlf', '$email', '$profesion', '$jornadaParcial', '$idiomas')";

                if (mysqli_query($conexion, $sql)) {
                    echo "Solicitud guardada correctamente.";
                } else {
                    echo "Error al guardar la solicitud: " . mysqli_error($conexion);
                }
            }
        }


        //Proceso de crear la tabla segun la profesion que se haya seleccionado
        if (isset($_REQUEST['tipo'])) {

            $tipo = $_REQUEST['tipo'];

            if ($tipo == "soldadura") {
                $profesion = "soldadura";

            } elseif ($tipo == "informatica") {
                $profesion = "informatica";

            } elseif ($tipo == "socio") {
                $profesion = "asistencia sociosanitaria";
            }


            // Busco las solicitudes de "esa" profesion
            $sql = "SELECT * FROM SOLICITUD WHERE profesion = '$profesion'";

            $resultado = mysqli_query($conexion, $sql);


            // Creamos la tabla
            echo "<table border='1'>";

            echo "<tr>";
            echo "<th>Nombre</th>";
            echo "<th>Apellidos</th>";
            echo "<th>DNI</th>";
            echo "<th>Fecha de nacimiento</th>";
            echo "<th>Telefono</th>";
            echo "<th>Email</th>";
            echo "<th>Profesion</th>";
            echo "<th>Jornada parcial</th>";
            echo "<th>Idiomas</th>";
            echo "</tr>";

            //La siguiente linea obtiene una fila de los resultados de la consulta
            // y la guarda en la variable creada: $fila
            $fila = mysqli_fetch_assoc($resultado);

            //cuando ya no quedan filas, mysqli_fetch_assoc($resultado) devuelve false
            //y sale del while
            while ($fila != false) {

                echo "<tr>";

                echo "<td>" . $fila["nombre"] . "</td>";
                echo "<td>" . $fila["apellidos"] . "</td>";
                echo "<td>" . $fila["dni"] . "</td>";
                echo "<td>" . $fila["f_nac"] . "</td>";
                echo "<td>" . $fila["tlf"] . "</td>";
                echo "<td>" . $fila["email"] . "</td>";
                echo "<td>" . $fila["profesion"] . "</td>";

                // Si la jornada es parcial, 1 significa "Sí"
                if ($fila["jornadaParcial"] == 1) {
                    echo "<td>Si</td>";
                } else {
                    // Si es completa, 0 significa "No"
                    echo "<td>No</td>";
                }

                echo "<td>" . $fila["idiomas"] . "</td>";

                echo "</tr>";

                // Cogemos la siguiente fila
                $fila = mysqli_fetch_assoc($resultado);
            }

            echo "</table>";
        }

        ?>
        <button onclick="location.href='../html/index.html'">
            Volver al formulario
        </button>

    </body>
</html>