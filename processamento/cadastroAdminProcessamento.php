<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===================================
// Conexão com o banco
// ===================================
require_once("conexao.php");
$conn = CriarConexao();

// ===================================
// CAPTURA DE DADOS DO FORMULÁRIO
// (aceita nomes vindos do teu front e também do rascunho antigo)
// ===================================
$id = $_POST['id_user'] ?? ($_POST['id'] ?? null);
$nome = $_POST['usuario'] ?? ($_POST['name'] ?? "");
$email = $_POST['email'] ?? "";
$senha = $_POST['senha'] ?? "";
$is_admin = isset($_POST['is_admin']) ? 1 : 0; // checkbox marcado = 1; senão 0
$tipo = $_POST['tipo'] ?? "";  // “Salvar”, “Atualizar”, “Deletar”, “Buscar”

// ===================================
// VALIDAÇÃO BÁSICA DA AÇÃO
// ===================================
if ($tipo === "") {
    echo "<script>
        alert('❌ Erro: ação inválida.');
         window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';
    </script>";
    $conn->close();
    exit;
}

// ===================================
// BUSCAR (AJAX) - monta as linhas <tr> para a tabela
// ===================================
if ($tipo === "Buscar") {
    $busca = trim($_POST['q'] ?? "");
    if ($busca === "") {
        $sql = "SELECT id, name, email, password, is_admin
                  FROM users
              ORDER BY id DESC";
    } else {
        $b = $conn->real_escape_string($busca);
        $sql = "SELECT id, name, email, password, is_admin
                  FROM users
                 WHERE name  LIKE '%$b%'
                    OR email LIKE '%$b%'
                    OR CAST(id AS CHAR) LIKE '%$b%'
              ORDER BY id DESC";
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";

            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['password']) . "</td>";

            if ((int) $row['is_admin'] === 1) {
                echo "<td style='color:green;font-weight:600;'>Sim ✅</td>";
            } else {
                echo "<td style='color:#555;'>Não</td>";
            }

            echo "<td style='display:flex;gap:8px;align-items:center;'>";

            // EDITAR: abre a tela com o id na querystring
// EDITAR (igual ao comportamento do cadastrarImportar)
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
      <img src='../icons/editaricon.png' alt='Editar'
           style='width:16px;height:16px;object-fit:contain;'>
      Editar
    </a>";



            // DELETAR: envia POST
            echo "<form method='post' action='../processamento/cadastroAdminProcessamento.php' style='display:inline;'>
                    <input type='hidden' name='id_user' value='" . $row['id'] . "'>
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
        echo "<tr><td colspan='5' style='text-align:center;'>Nenhum usuário encontrado.</td></tr>";
    }

    $conn->close();
    exit;
}

// ===================================
// SANITIZAÇÃO SIMPLES (para Salvar/Atualizar)
// ===================================
$nome_esc = $conn->real_escape_string($nome);
$email_esc = $conn->real_escape_string($email);
$senha_esc = $conn->real_escape_string($senha);

// ===================================
// SALVAR
// ===================================
if ($tipo === "Salvar") {
    if ($nome_esc === "" || $email_esc === "" || $senha_esc === "") {
        echo "<script>
            alert('❌ Preencha nome, e-mail e senha.');
             window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';

        </script>";
        $conn->close();
        exit;
    }

    $sql = "INSERT INTO users (name, email, password, is_admin, created_at, updated_at)
            VALUES ('$nome_esc', '$email_esc', '$senha_esc', $is_admin, NOW(), NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            alert('✅ Usuário cadastrado com sucesso!');
            window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';


        </script>";
    } else {
        echo "<script>
            alert('❌ Erro ao cadastrar: " . addslashes($conn->error) . "');
             window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';
    
            </script>";
    }

    $conn->close();
    exit;
}

// ===================================
// ATUALIZAR
// ===================================
if ($tipo === "Atualizar") {
    if (empty($id)) {
        echo "<script>
            alert('❌ Erro: ID não informado para atualização.');
             window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';

            </script>";
        $conn->close();
        exit;
    }

    $id_int = (int) $id;

    $sql = "UPDATE users SET
                name = '$nome_esc',
                email = '$email_esc',
                password = '$senha_esc',
                is_admin = $is_admin,
                updated_at = NOW()
            WHERE id = $id_int";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            alert('✏️ Dados atualizados com sucesso!');
             window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';
            </script>";
    } else {
        echo "<script>
            alert('❌ Erro ao atualizar: " . addslashes($conn->error) . "');
             window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';

            </script>";
    }

    $conn->close();
    exit;
}

// ===================================
// DELETAR
// ===================================
if ($tipo === "Deletar") {
    if (empty($id)) {
        echo "<script>
            alert('❌ Erro: ID não informado para exclusão.');
             window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';

            </script>";
        $conn->close();
        exit;
    }

    $id_int = (int) $id;
    $sql = "DELETE FROM users WHERE id = $id_int";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            alert('🗑️ Usuário deletado com sucesso!');
             window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';
            </script>";
    } else {
        echo "<script>
            alert('❌ Erro ao deletar: " . addslashes($conn->error) . "');
             window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';

            </script>";
    }

    $conn->close();
    exit;
}

// ===================================
// Ação não reconhecida
// ===================================
echo "<script>
    alert('❌ Erro: ação não reconhecida.');
     window.top.location.href = '../paginas/main.php?pagina=cadastroAdmin.php';
    </script>";
$conn->close();
exit;
