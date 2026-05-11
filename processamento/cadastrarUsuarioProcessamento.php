<?php
// Inicia a sessão (caso queira salvar login depois)
session_start();

// Inclui a conexão
require_once "conexao.php";

// Cria a conexão
$conn = CriarConexao();

// Recebe os dados do formulário
$nome  = $_POST['usuario'] ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Verifica se os campos não estão vazios
if (empty($nome) || empty($email) || empty($senha)) {
    echo "<script>alert('Preencha todos os campos!'); history.back();</script>";
    exit;
}

//  Insere de forma simples no banco (sem confirmação de e-mail)
$sql = "INSERT INTO users (name, email, password, is_admin, created_at)
        VALUES ('$nome', '$email', '$senha', 0, NOW())";

if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('Usuário cadastrado com sucesso!');
            window.location.href = '../paginas/login.php';
          </script>";
} else {
    echo "<script>
            alert('Erro ao cadastrar usuário: " . $conn->error . "');
            history.back();
          </script>";
}

// Fecha a conexão
$conn->close();
?>
