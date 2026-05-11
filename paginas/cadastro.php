<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Cadastro</title>
    <link rel="stylesheet" href="../css/cadastro.css">
</head>
<body>

    <div class="background"></div>

    <div class="cadastro-container">
        <img src="../icons/faculdade.png" alt="Logo da Faculdade" class="logo">

        <h2>Cadastro de Usuário na Nexus</h2>

        <form action="../processamento/cadastrarUsuarioProcessamento.php" method="POST">
            <div class="form-group">
                <label for="usuario">Nome de Usuário:</label><br>
                <input type="text" id="usuario" name="usuario" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label><br>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label><br>
                <input type="password" id="senha" name="senha" required>
            </div>

            <div class="button-group">
              <button type="submit" class="btn-cadastrar">Cadastrar</button>
                  
                </button>
              <button type="button" class="btn-voltar" onclick="window.location.href='login.php'">Voltar ao Login</button>
                 
                </button>
            </div>
        </form>
    </div>

</body>
</html>
