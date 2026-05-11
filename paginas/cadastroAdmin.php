<?php
require_once "../processamento/autorizacao.php";
Autorizacao::proteger();
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Gerenciar Administradores</title>
  <link rel="stylesheet" href="../css/cadastroAdmin.css?v=1">
</head>

<body>

  <section id="cadastro-admin" class="container" style="max-width:1200px;margin:0 auto;padding:16px;">

    <h2 style="margin:8px 0 16px;">Gerenciar Usuários Administradores</h2>

    <!-- ========================== FORMULÁRIO ========================== -->
    <form id="form-admin" method="post" action="../processamento/cadastroAdminProcessamento.php" autocomplete="off"
      style="flex:1 1 720px;min-width:640px;">
      <input type="hidden" id="id_user" name="id_user" value="">

      <div class="campo">
        <label for="usuario">Nome de usuário:</label><br>
        <input type="text" id="usuario" name="usuario" placeholder="Defina o nome do usuário" required
          style="width:100%;">
      </div>

      <div class="campo" style="margin-top:10px;">
        <label for="email">E-mail:</label><br>
        <input type="email" id="email" name="email" placeholder="ex: usuario@exemplo.com" required style="width:100%;">
      </div>

      <div class="campo" style="margin-top:10px;">
        <label for="senha">Senha:</label><br>
        <input type="password" id="senha" name="senha" placeholder="Defina a senha" required style="width:100%;">
      </div>

      <div class="campo" style="margin-top:10px;">
        <label>Administrador:</label><br>
        <div style="display:flex;gap:16px;align-items:center;margin-top:6px;">
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" id="is_admin" name="is_admin" value="1" style="transform:scale(1.2);cursor:pointer;">
            Este usuário é administrador
          </label>
        </div>
      </div>

      <div id="acoes-form" style="margin-top:14px;display:flex;gap:12px;">
        <button type="submit" name="tipo" value="Salvar" id="btn-cadastrar">
          <img src="../icons/cadastraricon.png" alt="" class="icon">
          Cadastrar
        </button>
      </div>
    </form>

    <!-- ========================== BARRA DE BUSCA ========================== -->
    <div id="busca-filtros" style="margin-top:20px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <input type="text" id="campobusca" name="q" placeholder="Pesquisar por nome ou e-mail"
        style="flex:1 1 420px;min-width:300px;">
      <button type="button" id="btn-buscar">
        <img src="../icons/buscaricon.png" alt="" class="icon">
        Buscar
      </button>
    </div>

    <!-- ========================== TABELA DE USUÁRIOS ========================== -->
    <section id="lista-usuarios" style="margin-top:20px;">
      <h3 style="margin:0 0 8px;">Usuários cadastrados</h3>

      <div class="tabela-wrapper" style="width:100%;overflow:auto;">
        <table id="tabela-usuarios" style="width:100%;border-collapse:collapse;min-width:900px;">
          <thead>
            <tr>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Nome</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">E-mail</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Senha</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Administrador?</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Ações</th>
            </tr>
          </thead>

          <tbody id="tbody-usuarios">
            <?php
            require_once("../processamento/conexao.php");
            $conn = CriarConexao();

            $sql = "SELECT id, name, email, password, is_admin FROM users ORDER BY id DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['password']}</td>";

                if ($row['is_admin'] == 1) {
                  echo "<td style='color:green;font-weight:600;'>Sim ✅</td>";
                } else {
                  echo "<td style='color:red;font-weight:600; '>Não ❌</td>";
                }

                // Botões de ação (Editar + Deletar)
                echo "<td style='display:flex;gap:8px;align-items:center;'>";


                // Botão Editar (abre a tela de edição com os dados do usuário)
                echo "<a href='../paginas/cadastrarAdminEditar.php?editar_id={$row['id']}'
                target='_blank'
                style=\"
                display:flex;
                align-items:center;
                gap:6px;
                padding:8px 10px;
                border:none;
                border-radius:8px;
                cursor:pointer;
                background:linear-gradient(to right, #FF9838, #C96514);
                color:white;
                font-weight:500;
                text-decoration:none;
                \">
                <img src='../icons/editaricon.png' alt='Editar' style='width:16px;height:16px;object-fit:contain;'>
              Editar
            </a>";

                // Botão Deletar (POST)
                echo "<form method='post' action='../processamento/cadastroAdminProcessamento.php' style='display:inline;'>
                      <input type='hidden' name='id_user' value='{$row['id']}'>
                      <input type='hidden' name='tipo' value='Deletar'>
                      <button type='submit' class='btn-deletar'
                              style=\"
                                display:flex;
                                align-items:center;
                                gap:6px;
                                padding:8px 10px;
                                border:none;
                                border-radius:8px;
                                cursor:pointer;
                                background:linear-gradient(to right, #B40606, #890505);
                                color:white;
                                font-weight:500;
                              \">
                        <img src='../icons/deletaricon.png' alt='Excluir'
                             style='width:16px;height:16px;object-fit:contain;'>
                        Excluir
                      </button>
                    </form>";

                echo "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='5' style='text-align:center;'>Nenhum usuário cadastrado.</td></tr>";
            }

            $conn->close();
            ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ========================== SCRIPT DE BUSCA ========================== -->
    <script>
      document.getElementById("campobusca").addEventListener("keydown", function (e) {
        if (e.key === "Enter") document.getElementById("btn-buscar").click();
      });

      document.getElementById("btn-buscar").addEventListener("click", function () {
        const termo = document.getElementById("campobusca").value.trim();

        fetch("../processamento/cadastroAdminProcessamento.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "tipo=Buscar&q=" + encodeURIComponent(termo)
        })
          .then(resp => resp.text())
          .then(html => {
            document.getElementById("tbody-usuarios").innerHTML = html;
          })
          .catch(err => console.error("Erro na busca:", err));
      });
    </script>

  </section>

</body>

</html>