<?php
function CriarConexao(){

    $host = "204.216.145.129";
    $user = "iea";
    $pass = "iea";
    $db   = "iea";

    // Oculta warnings do mysqli
    mysqli_report(MYSQLI_REPORT_OFF);

    $conn = new mysqli($host, $user, $pass, $db);

    // Se falhar retorna null
    if ($conn->connect_error) {
        return null;
    }

    return $conn;
}
?>