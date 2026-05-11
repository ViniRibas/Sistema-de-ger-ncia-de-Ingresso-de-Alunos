<?php
require_once "../processamento/autorizacao.php";
Autorizacao::proteger();
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Cadastrar e Importar</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../css/cadastrarImportar.css?v=2">
</head>

<body>

  <section id="cadastro-importar" class="container" style="max-width:1400px;margin:0 auto;padding:16px;">

    <h2 style="margin:8px 0 16px;">Cadastrar Alunos e Importar via Excel</h2>

    <?php
    $editar_dados = null;
    $filtro = $_GET['filtro'] ?? $_POST['filtro'] ?? ''; 
    if (isset($_GET['editar_id'])) {
      require_once("../processamento/conexao.php");
      $conn = CriarConexao();
      $filtro = $_GET['filtro'] ?? $_POST['filtro'] ?? '';
      $id = (int) $_GET['editar_id'];
      $sql = "SELECT * FROM alunos WHERE id = $id LIMIT 1";
      $result = $conn->query($sql);
      if ($result && $result->num_rows > 0) {
        $editar_dados = $result->fetch_assoc();
      }
      $conn->close();
    }
    ?>


    <!-- topo do formulario -->
    <div id="topo" style="display:flex;gap:24px;align-items:flex-start;flex-wrap:wrap;">

      <!-- formulario para insercao dos dados com os inputs -->
      <form id="form-aluno" method="post" action="../processamento/cadastrarImportarProcessamento.php"
        autocomplete="off" style="flex:1 1 720px;min-width:640px;">
        <input type="hidden" id="id" name="id" value="">

        <div class="campo">
          <label for="nome">Nome completo:</label><br>
          <input type="text" id="nome" name="nome" placeholder="Digite o nome completo" style="width:100%;" required>
        </div>

        <div class="campo" style="margin-top:10px;">
          <label for="matricula">Matrícula:</label><br>
          <input type="text" id="matricula" name="matricula" placeholder="Digite a matrícula" style="width:100%;"
            required>
        </div>

        <div class="campo" style="margin-top:10px;">
          <label for="email">E-mail:</label><br>
          <input type="email" id="email" name="email" placeholder="ex: aluno@faculdadedombosco.edu.br"
            style="width:100%;">
        </div>

        <div class="campo" style="margin-top:10px;">
          <label for="telefone">Telefone/Celular:</label><br>
          <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" style="width:100%;">
        </div>

        <div class="campo" style="margin-top:10px;">
          <label for="doc_oab">Documento OAB (link):</label><br>
          <input type="url" id="doc_oab" name="doc_oab" placeholder="https://..." style="width:100%;">
        </div>

        <!-- situacao da oab -->
        <div class="campo" style="margin-top:10px;">
          <label>Situação OAB:</label><br>

          <div style="display:flex;gap:16px;align-items:center;margin-top:6px;">
            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
              <input type="checkbox" id="aprovado" name="aprovado_oab" value="s"
                style="transform:scale(1.2);cursor:pointer;">
              Aprovado
            </label>

            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
              <input type="checkbox" id="reprovado" name="aprovado_oab" value="n"
                style="transform:scale(1.2);cursor:pointer;">
              Reprovado
            </label>
          </div>
        </div>

        <script>
          // limita o checkbox apenas de um por vez 
          const chkAprovado = document.getElementById('aprovado');
          const chkReprovado = document.getElementById('reprovado');

          chkAprovado.addEventListener('change', () => {
            if (chkAprovado.checked) chkReprovado.checked = false;
          });
          chkReprovado.addEventListener('change', () => {
            if (chkReprovado.checked) chkAprovado.checked = false;
          });
        </script>





        <div id="acoes-form" style="margin-top:14px;display:flex;gap:12px;">
          <button type="submit" name="tipo" value="Salvar" id="btn-cadastrar">
            <img src="../icons/cadastraricon.png" alt="" class="icon">
            Cadastrar
          </button>


        </div>
      </form>

      <!-- Upload de planilha -->

      <aside id="upload-planilha" style="flex:0 0 320px;min-width:280px;">
        <div style="height: 45px; visibility: hidden;"></div>
        <form id="form-planilha" action="../processamento/importarPlanilha.php" method="post"
          enctype="multipart/form-data">
          <div id="dropzone" tabindex="0" aria-label="Área para arrastar planilha"
            style="border:2px dashed #bbb;border-radius:8px;padding:24px;text-align:center;cursor:pointer;">
            <p><strong>Importar planilha</strong></p>
            <p>Arraste e solte aqui, ou clique para selecionar.</p>
            <input type="file" id="input-planilha" name="planilha" accept=".xlsx,.xls,.csv" style="display:none;">
          </div>

          <button type="submit" id="btn-upload-planilha"
            style="margin-top:12px;width:100%;display:flex;align-items:center;justify-content:center;gap:10px;">
            <img src="../icons/excelicon.png" alt="" class="icon">
            Fazer upload da planilha
          </button>
        </form>
      </aside>

      <script>
        // ao clicar na área ou botão, abre o explorador de arquivos
        const dropzone = document.getElementById("dropzone");
        const inputFile = document.getElementById("input-planilha");

        if (dropzone && inputFile) {
          dropzone.addEventListener("click", () => inputFile.click());

          // quando arquivo for escolhido manualmente
          inputFile.addEventListener("change", () => {
            if (inputFile.files && inputFile.files.length) {
              dropzone.classList.remove("dragover");
              dropzone.classList.add("uploaded"); // aqui é ativado o verde
            } else {
              dropzone.classList.remove("uploaded");
            }
          });

          // permite arrastar arquivo
          dropzone.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropzone.style.borderColor = "#1da851";
            dropzone.classList.add("dragover");
          });

          dropzone.addEventListener("dragleave", () => {
            dropzone.style.borderColor = "#bbb";
            dropzone.classList.remove("dragover");
          });

          dropzone.addEventListener("drop", (e) => {
            e.preventDefault();
            dropzone.style.borderColor = "#bbb";
            dropzone.classList.remove("dragover");

            if (e.dataTransfer.files && e.dataTransfer.files.length) {
              inputFile.files = e.dataTransfer.files;
              dropzone.classList.add("uploaded"); // ativa o estado verde e icone
            }
          });
        }
      </script>


    </div>

    <!-- Barra de busca e filtros -->
    <div id="busca-filtros" style="margin-top:20px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <input type="text" id="campobusca" name="q" placeholder="Pesquisar por nome ou matrícula"
        style="flex:1 1 420px;min-width:300px;">

      <select id="filtro" name="filtro">
        <option value="">Sem filtro</option>
        <option value="dadospendentes" <?= $filtro === 'dadospendentes' ? 'selected' : '' ?>>Dados pendentes</option>
        <option value="docpendente" <?= $filtro === 'docpendente' ? 'selected' : '' ?>>Documento pendente</option>
        <option value="emailpendente" <?= $filtro === 'emailpendente' ? 'selected' : '' ?>>E-mail pendente</option>
        <option value="telpendente" <?= $filtro === 'telpendente' ? 'selected' : '' ?>>Telefone pendente</option>
        <option value="aprovado" <?= $filtro === 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
        <option value="reprovado" <?= $filtro === 'reprovado' ? 'selected' : '' ?>>Reprovado</option>
        <option value="oabpendente" <?= $filtro === 'oabpendente' ? 'selected' : '' ?>>Situação OAB pendente</option>

      </select>

      <script>
        document.getElementById("filtro").addEventListener("change", function () {
          const filtro = this.value;

          fetch("../processamento/cadastrarImportarProcessamento.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "tipo=Filtro&filtro=" + encodeURIComponent(filtro)
          })
            .then(resp => resp.text())
            .then(html => {
              document.getElementById("tbody-alunos").innerHTML = html;
            })
            .catch(err => console.error("Erro ao aplicar filtro:", err));
        });
      </script>


      <button type="button" id="btn-buscar">
        <img src="../icons/buscaricon.png" alt="" class="icon">
        Buscar
      </button>
    </div>

    <!-- Tabela  dos alunos  -->
    <section id="lista-alunos" style="margin-top:20px;">
      <h3 style="margin:0 0 8px;">Alunos cadastrados</h3>

      <div class="tabela-wrapper" style="width:100%;overflow:auto;">
        <table id="tabela-alunos" style="width:100%;border-collapse:collapse;min-width:1000px;">
          <thead>
            <tr>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Matrícula</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Nome</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">E-mail</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Telefone/Celular</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Situação OAB</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Documento OAB</th>
              <th style="text-align:left;border-bottom:1px solid #ccc;padding:10px;">Ações</th>
            </tr>
          </thead>

          <tbody id="tbody-alunos">
            <?php
            require_once("../processamento/conexao.php");
            $conn = CriarConexao();

            $sql = "SELECT id, matricula, nome, email, telefone, aprovado_oab, doc_oab 
                FROM alunos ORDER BY id DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['matricula']}</td>";
                echo "<td>{$row['nome']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['telefone']}</td>";

                if ($row['aprovado_oab'] == 's') {
                  echo "<td style='color:green;font-weight:600;'>Aprovado ✅</td>";
                } elseif ($row['aprovado_oab'] == 'n') {
                  echo "<td style='color:red;font-weight:600;'>Reprovado ❌</td>";
                } else {
                  echo "<td style='color:#555;'>Pendente</td>";
                }

                if (!empty($row['doc_oab'])) {
                  echo "<td><a href='{$row['doc_oab']}' target='_blank' rel='noopener'>Ver documento</a></td>";
                } else {
                  echo "<td>—</td>";
                }

                // Botões de ação (Editar + Deletar)
                echo "<td style='display:flex;gap:8px;align-items:center;'>";

                echo "<a href='cadastrarEditar.php?id={$row['id']}'
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
        <img src='../icons/editaricon.png' alt='Editar'
             style='width:16px;height:16px;object-fit:contain;'>
        Editar
      </a>";

                // Botão DELETAR (mantém form pois é POST)
                echo "<form method='post' action='../processamento/cadastrarImportarProcessamento.php' style='display:inline;'>
                    <input type='hidden' name='id' value='{$row['id']}'>
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
              echo "<tr><td colspan='7' style='text-align:center;'>Nenhum aluno cadastrado.</td></tr>";
            }

            $conn->close();
            ?>
          </tbody>
        </table>
      </div>
    </section>


<script>
  // quando o usuário pressionar Enter dentro do campo de busca
  document.getElementById("campobusca").addEventListener("keydown", function (e) {
    if (e.key === "Enter") document.getElementById("btn-buscar").click();
  });

  // clique no botão Buscar
  document.getElementById("btn-buscar").addEventListener("click", function () {
    const termo = document.getElementById("campobusca").value.trim();

    // limpa o filtro salvo, pra não interferir
    localStorage.removeItem("filtroSelecionado");

    // faz a requisição AJAX pro PHP
    fetch("../processamento/cadastrarImportarProcessamento.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "tipo=Buscar&q=" + encodeURIComponent(termo)
    })
      .then(resp => resp.text())
      .then(html => {
        // substitui o conteúdo da tabela pelos resultados
        document.getElementById("tbody-alunos").innerHTML = html;
      })
      .catch(err => console.error("Erro na busca:", err));
  });
</script>


    <script>
      document.getElementById("filtro").addEventListener("change", function () {
        const filtro = this.value;
        localStorage.setItem("filtroSelecionado", filtro);

        fetch("../processamento/cadastrarImportarProcessamento.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "tipo=Filtro&filtro=" + encodeURIComponent(filtro)
        })
          .then(resp => resp.text())
          .then(html => {
            document.getElementById("tbody-alunos").innerHTML = html;
          });
      });

      // Ao recarregar a página, restaura a seleção
      window.addEventListener("load", () => {
        const filtroSalvo = localStorage.getItem("filtroSelecionado");
        if (filtroSalvo) {
          document.getElementById("filtro").value = filtroSalvo;
        }
      });
    </script>










</body>

</html>