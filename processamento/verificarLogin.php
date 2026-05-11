<?php
session_start();
require_once "conexao.php";

$conn = CriarConexao();

// Coleta dados do formulário
$email = $_POST['email'];
$senha = $_POST['senha'];

// Consulta simples no banco
$sql = "SELECT * FROM users WHERE email = '$email' AND password = '$senha' LIMIT 1";
$resultado = mysqli_query($conn, $sql);

// Verifica se encontrou usuário
if (mysqli_num_rows($resultado) > 0) {
    $linha = mysqli_fetch_array($resultado);

    // Salva dados na sessão
    $_SESSION['user_id'] = $linha['id'];
    $_SESSION['user_nome'] = $linha['name'];
    $_SESSION['logado'] = true;

    // Cria cookie simples
    setcookie("usuario_logado", $linha['email'], time() + 7000, "/");

    // Redireciona para a tela principal
    header("Location: ../paginas/main.php");
    exit();
} else {
    echo "<script>
        alert('E-mail ou senha incorretos!');
        window.location.href='../paginas/login.php';
    </script>";
}

// Fecha conexão
mysqli_close($conn);
?>
