<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>

    <div class="background"></div>

    <div class="login-container">
        <img src="../icons/faculdade.png" alt="Logo da Faculdade" class="logo">

        <h2>Bem-vindo ao Nexus Sistema de Controle de Egressos de Aprovados na Faculdade pela OAB</h2>

        <form action="../processamento/verificarLogin.php" method="POST">
            <div class="form-group">
                <label for="email">E-mail:</label><br>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label><br>
                <input type="password" id="senha" name="senha" required>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-entrar">Entrar</button>
                <button type="button" class="btn-cadastrar" onclick="window.location.href='cadastro.php'">Cadastrar-se</button>
            </div>
        </form>
    </div>

</body>
</html>
