<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===================================
// Conexão com o banco
// ===================================
require_once("conexao.php");
require_once("logProcessamento.php");
session_start(); // necessário para pegar o id do usuário logado
$user_id = $_SESSION['user_id'] ?? 0; // id do usuário logado
$conn = CriarConexao();

// ===================================
// CAPTURA DE DADOS DO FORMULÁRIO
// ===================================
$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? "";
$email = $_POST['email'] ?? "";
$telefone = $_POST['telefone'] ?? "";
$matricula = isset($_POST['matricula']) ? (int) $_POST['matricula'] : 0;
$doc_oab = $_POST['doc_oab'] ?? "";
$aprovado_oab = $_POST['aprovado_oab'] ?? null;
$tipo = $_POST['tipo'] ?? "";  // “Salvar”, “Atualizar” ou “Deletar”

// ===================================
// VALIDAÇÃO BÁSICA
// ===================================
if ($tipo === "") {
    echo "<script>
        alert('❌ Erro: ação inválida ou dados incompletos.');
       window.top.location.href = '../paginas/main.php';
    </script>";
    $conn->close();
    exit;
}

// ===================================
// AÇÕES PRINCIPAIS
// ===================================

// ===================================
// BUSCAR ALUNOS (ajax)
// ===================================
if ($tipo === "Buscar") {
    $busca = trim($_POST['q'] ?? "");

    // Monta o SQL conforme o campo de busca
    if ($busca === "") {
        // se vazio, retorna todos os registros
        $sql = "SELECT id, matricula, nome, email, telefone, aprovado_oab, doc_oab 
                FROM alunos
                ORDER BY id DESC";
    } else {
        // se há texto, filtra por nome ou matrícula
        // força comparação minúscula para ignorar case
        $busca = strtolower($busca);
        $sql = "SELECT id, matricula, nome, email, telefone, aprovado_oab, doc_oab 
            FROM alunos
            WHERE LOWER(nome) LIKE '%$busca%' 
               OR CAST(matricula AS CHAR) LIKE '%$busca%'
            ORDER BY id DESC";
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";

            // Matrícula
            if (!empty($row['matricula'])) {
                echo "<td>{$row['matricula']}</td>";
            }

            // Nome
            if (!empty($row['nome'])) {
                echo "<td>{$row['nome']}</td>";
            }

            if (!empty($row['email']) && trim($row['email']) !== '') {
                // Se o campo 'email' NÃO estiver vazio nem for só espaços,
                // imprime o valor normalmente na célula da tabela.
                echo "<td>{$row['email']}</td>";
            } 

           
            if (!empty($row['telefone']) && trim($row['telefone']) !== '') {
                // Se o campo 'telefone' estiver preenchido corretamente,
                // mostra o número informado.
                echo "<td>{$row['telefone']}</td>";
            } 

            // Situação OAB
            if ($row['aprovado_oab'] == 's') {
                echo "<td style='color:green;font-weight:600;'>Aprovado ✅</td>";
            } else if ($row['aprovado_oab'] == 'n') {
                echo "<td style='color:red;font-weight:600;'>Reprovado ❌</td>";
            } else {
                echo "<td style='color:#555;'>Pendente</td>";
            }

            // Documento OAB
            if (!empty($row['doc_oab'])) {
                echo "<td><a href='{$row['doc_oab']}' target='_blank' rel='noopener'>Ver documento</a></td>";
            } else {
                echo "<td style='color:#555;'>Pendente</td>";
            }
            echo "<td style='display:flex;gap:8px;align-items:center;'>";

            // Botão EDITAR
            echo "<a href='cadastrarEditar.php?id={$row['id']}' target='_blank'
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

            // Botão DELETAR
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
        echo "<tr><td colspan='7' style='text-align:center;'>Nenhum aluno encontrado.</td></tr>";
    }

    $conn->close();
    exit;
}


if ($tipo === "Salvar") {

    // Define valor padrão se nada for marcado
    $valorAprovado = $aprovado_oab ? "'$aprovado_oab'" : "NULL";

    $sql = "INSERT INTO alunos (matricula, nome, email, telefone, doc_oab, aprovado_oab)
            VALUES ($matricula, '$nome', '$email', '$telefone', '$doc_oab', $valorAprovado)";

    if ($conn->query($sql) === TRUE) {
        registrarLog($user_id, $conn->insert_id, 'Inclusão');
        echo "<script>
            alert('✅ Aluno cadastrado com sucesso!');
            window.top.location.href = '../paginas/main.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Erro ao cadastrar: " . addslashes($conn->error) . "');
            window.top.location.href = '../paginas/main.php';
        </script>";
    }

} elseif ($tipo === "Atualizar") {

    if (!empty($id)) {
        $valorAprovado = $aprovado_oab ? "'$aprovado_oab'" : "NULL";

        $sql = "UPDATE alunos 
                SET matricula = $matricula,
                    nome      = '$nome',
                    email     = '$email',
                    telefone  = '$telefone',
                    doc_oab   = '$doc_oab',
                    aprovado_oab = $valorAprovado
                WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            registrarLog($user_id, $id, 'Edição');

            echo "<script>
                alert('✏️ Dados atualizados com sucesso!');
                window.top.location.href = '../paginas/main.php';
            </script>";
        } else {
            echo "<script>
                alert('❌ Erro ao atualizar: " . addslashes($conn->error) . "');
                window.top.location.href = '../paginas/main.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('❌ Erro: ID não informado para atualização.');
            window.top.location.href = '../paginas/main.php';
        </script>";
    }

} elseif ($tipo === "Deletar") {
    if (!empty($id)) {
        // registra o log antes de apagar o aluno
        registrarLog($user_id, $id, 'Exclusão');

        $sql = "DELETE FROM alunos WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
            alert('🗑️ Aluno deletado com sucesso!');
            window.top.location.href = '../paginas/main.php';
        </script>";
        } else {
            echo "<script>
            alert('❌ Erro ao deletar: " . addslashes($conn->error) . "');
            window.top.location.href = '../paginas/main.php';
        </script>";
        }
    }
}




// ===================================
// FILTRAR ALUNOS
// ===================================
if ($tipo === "Filtro") {
    $filtro = trim($_POST['filtro'] ?? "");

    $sql = "SELECT id, matricula, nome, email, telefone, aprovado_oab, doc_oab 
            FROM alunos WHERE 1=1";

    if ($filtro === "dadospendentes") {
        $sql .= " AND (email = '' OR telefone = '' OR doc_oab = '' OR aprovado_oab IS NULL)";
    } elseif ($filtro === "docpendente") {
        $sql .= " AND (doc_oab = '' OR doc_oab IS NULL)";
    } elseif ($filtro === "emailpendente") {
        $sql .= " AND (email = '' OR email IS NULL)";
    } elseif ($filtro === "telpendente") {
        $sql .= " AND (telefone = '' OR telefone IS NULL)";
    } elseif ($filtro === "aprovado") {
        $sql .= " AND aprovado_oab = 's'";
    } elseif ($filtro === "reprovado") {
        $sql .= " AND aprovado_oab = 'n'";
    } elseif ($filtro === "oabpendente") {
        $sql .= " AND aprovado_oab IS NULL";
    }

    $sql .= " ORDER BY id DESC";

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
                echo "<td><a href='{$row['doc_oab']}' target='_blank'>Ver documento</a></td>";
            } else {
                echo "<td>—</td>";
            }

            // ==========================
            // BOTÕES DE AÇÃO (Editar + Deletar)
            // ==========================
            echo "<td style='display:flex;gap:8px;align-items:center;'>";

            // Botão EDITAR
            echo "<a href='cadastrarEditar.php?id={$row['id']}' target='_blank'
                    style=\"display:flex;align-items:center;gap:6px;
                    padding:8px 10px;border:none;border-radius:8px;
                    cursor:pointer;background:linear-gradient(to right,#FF9838,#C96514);
                    color:white;font-weight:500;text-decoration:none;\">
                    <img src='../icons/editaricon.png' alt='Editar' style='width:16px;height:16px;'>
                    Editar
                  </a>";

            // Botão DELETAR
            echo "<form method='post' action='../processamento/cadastrarImportarProcessamento.php' style='display:inline;'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <input type='hidden' name='tipo' value='Deletar'>
                    <button type='submit' class='btn-deletar'
                            style=\"display:flex;align-items:center;gap:6px;
                            padding:8px 10px;border:none;border-radius:8px;
                            cursor:pointer;background:linear-gradient(to right,#B40606,#890505);
                            color:white;font-weight:500;\">
                        <img src='../icons/deletaricon.png' alt='Excluir' style='width:16px;height:16px;'>
                        Excluir
                    </button>
                  </form>";

            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7' style='text-align:center;'>Nenhum aluno encontrado com esse filtro.</td></tr>";
    }

    $conn->close();
    exit;
}





// ===================================
// Fecha conexão e encerra execução
// ===================================
$conn->close();
exit;
?>