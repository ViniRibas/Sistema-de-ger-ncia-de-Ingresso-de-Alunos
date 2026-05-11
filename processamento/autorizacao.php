<?php
class Autorizacao {

    // Função para bloquear acesso de quem não está logado
    public static function proteger() {
        session_start();

        /*
        // Se não estiver logado, manda para login
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
            header("Location: login.php");
            exit();
        }
            */

        
    }


}
?>
