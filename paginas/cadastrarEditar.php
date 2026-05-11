<?php
// /sitev3/paginas/cadastrarEditar.php

require_once "../processamento/autorizacao.php";
Autorizacao::proteger();

require_once("../processamento/conexao.php");
$conn = CriarConexao();

$aluno = null;

if (isset($_GET['id'])) {
  $id = (int) $_GET['id'];
  $sql = "SELECT * FROM alunos WHERE id = $id LIMIT 1";
  $result = $conn->query($sql);
  if ($result && $result->num_rows > 0) {
    $aluno = $result->fetch_assoc();
  }
}
$conn->close();

if (!$aluno) {
  echo "<p style='text-align:center;margin-top:40px;'>Aluno não encontrado.</p>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Aluno</title>
  <link rel="stylesheet" href="../css/cadastrarEditar.css?v=1.0">
</head>
<body>

  <main class="editar-container">
    <h2>Editar Aluno</h2>

    <form method="post" action="../processamento/cadastrarImportarProcessamento.php" id="form-editar">
      <input type="hidden" name="id" value="<?= $aluno['id'] ?>">

      <label for="nome">Nome completo:</label>
      <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" required>

      <label for="matricula">Matrícula:</label>
      <input type="text" id="matricula" name="matricula" value="<?= htmlspecialchars($aluno['matricula']) ?>" required>

      <label for="email">E-mail:</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($aluno['email']) ?>">

      <label for="telefone">Telefone:</label>
      <input type="tel" id="telefone" name="telefone" value="<?= htmlspecialchars($aluno['telefone']) ?>">

      <label for="doc_oab">Documento OAB (link):</label>
      <input type="url" id="doc_oab" name="doc_oab" value="<?= htmlspecialchars($aluno['doc_oab']) ?>">

      <!-- Situação OAB -->
      <div class="campo" style="margin-top:12px;">
        <label>Situação OAB:</label><br>
        <div style="display:flex;gap:16px;align-items:center;margin-top:6px;">
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" id="aprovado" name="aprovado_oab" value="s"
              style="transform:scale(1.2);cursor:pointer;"
              <?= ($aluno['aprovado_oab'] === 's') ? 'checked' : '' ?>>
            Aprovado
          </label>

          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" id="reprovado" name="aprovado_oab" value="n"
              style="transform:scale(1.2);cursor:pointer;"
              <?= ($aluno['aprovado_oab'] === 'n') ? 'checked' : '' ?>>
            Reprovado
          </label>
        </div>
      </div>

      <script>
        // Garante exclusividade (não marcar ambos)
        const chkAprovado = document.getElementById('aprovado');
        const chkReprovado = document.getElementById('reprovado');

        chkAprovado.addEventListener('change', () => {
          if (chkAprovado.checked) chkReprovado.checked = false;
        });
        chkReprovado.addEventListener('change', () => {
          if (chkReprovado.checked) chkAprovado.checked = false;
        });
      </script>

      <div class="botoes" style="margin-top:18px;">
        <a href="main.php" class="btn-cancelar">Cancelar</a>
        <button type="submit" name="tipo" value="Atualizar" class="btn-salvar">Salvar Alterações</button>
      </div>
    </form>
  </main>

</body>
</html>
