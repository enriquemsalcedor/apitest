<?php

$mysqli = new mysqli("34.130.54.49", "admin", "Atik@2022", "test");


if ($mysqli->connect_error) {
    echo "Fallo al conectar a MySQL: (" . $mysqli->connect_error . ") " . $mysqli->connect_error;
}


?>