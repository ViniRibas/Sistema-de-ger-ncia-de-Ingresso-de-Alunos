<?php
require_once "../processamento/autorizacao.php";
Autorizacao::proteger();



require_once("../processamento/conexao.php");


$conn = CriarConexao();

$user = null;

if (isset($_GET['editar_id'])) {
  $id = (int) $_GET['editar_id'];
  $sql = "SELECT * FROM users WHERE id = $id LIMIT 1";
  $result = $conn->query($sql);
  if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
  }
}

$conn->close();

if (!$user) {
  echo "<p class='mensagem-erro'>Usuário não encontrado.</p>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Administrador</title>
  <link rel="stylesheet" href="../css/cadastrarAdminEditar.css?v=1.0">
</head>
<body>

  <main class="editar-container">
    <h2>Editar Administrador</h2>

    <form method="post" action="../processamento/cadastroAdminProcessamento.php" id="form-editar" autocomplete="off">
      <input type="hidden" name="id_user" value="<?= $user['id'] ?>">

      <div class="campo">
        <label for="usuario">Nome completo:</label>
        <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($user['name']) ?>" required>
      </div>

      <div class="campo">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>

      <div class="campo">
        <label for="senha">Senha:</label>
        <input type="text" id="senha" name="senha" value="<?= htmlspecialchars($user['password']) ?>" required>
      </div>

      <div class="campo">
        <label class="checkbox-admin">
          <input type="checkbox" id="is_admin" name="is_admin" value="1" <?= ($user['is_admin'] == 1) ? 'checked' : '' ?>>
          Usuário é administrador
        </label>
      </div>

      <div class="botoes">
        <a href="../paginas/main.php?pagina=cadastroAdmin.php" class="btn-cancelar">Cancelar</a>
        <button type="submit" name="tipo" value="Atualizar" class="btn-salvar">Salvar Alterações</button>
      </div>
    </form>
  </main>

</body>
</html>
