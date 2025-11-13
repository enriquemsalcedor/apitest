<?php

$mysqli = new mysqli("34.130.54.49", "admin", "Atik@2022", "test", "3306");


if ($mysqli->connect_error) {
    echo "Fallo al conectar a MySQL: (" . $mysqli->connect_error . ") " . $mysqli->connect_error;
}


?>