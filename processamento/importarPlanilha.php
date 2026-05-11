<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("conexao.php");
require '../vendor/autoload.php';


require_once("logProcessamento.php");
session_start();
$user_id = $_SESSION['user_id'] ?? 0;

use PhpOffice\PhpSpreadsheet\IOFactory;

// =============================
// Verifica se o arquivo foi enviado
// =============================
if (!isset($_FILES['planilha']) || $_FILES['planilha']['error'] != 0) {
    echo "<script>alert('❌ Erro ao enviar o arquivo.'); window.history.back();</script>";
    exit;
}

// Caminho temporário do upload
$arquivoTmp = $_FILES['planilha']['tmp_name'];
$nomeArquivo = $_FILES['planilha']['name'];
$extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

if (!in_array($extensao, ['xls', 'xlsx'])) {
    echo "<script>alert('❌ Tipo de arquivo inválido. Envie um arquivo .xls ou .xlsx'); window.history.back();</script>";
    exit;
}

$conn = CriarConexao();

if (!$conn) {
    echo "<script>
        alert('Servidor do banco de dados temporariamente indisponível.');
        window.top.location.href = '../paginas/main.php';
    </script>";
    exit;
}


try {
    // Carrega a planilha com PhpSpreadsheet
    $spreadsheet = IOFactory::load($arquivoTmp);
    $sheet = $spreadsheet->getActiveSheet();

    $linha = 0;
    $importados = 0;

    // Começa da linha 2 (pula cabeçalho)
    foreach ($sheet->getRowIterator(2) as $row) {
        $linhaIndex = $row->getRowIndex();

        // Colunas da planilha:
        $matricula     = trim($sheet->getCell('A' . $linhaIndex)->getValue());
        $nome          = trim($sheet->getCell('B' . $linhaIndex)->getValue());
        $email         = trim($sheet->getCell('C' . $linhaIndex)->getValue());
        $telefone      = trim($sheet->getCell('D' . $linhaIndex)->getValue());
        $aprovado_oab  = trim($sheet->getCell('E' . $linhaIndex)->getValue());
        $doc_oab       = trim($sheet->getCell('F' . $linhaIndex)->getValue());

        // Ignora linhas vazias
        if ($nome == "" || $matricula == "") continue;

        $matricula = (int)$matricula;
        $aprovado_sql = ($aprovado_oab != "") ? "'$aprovado_oab'" : "NULL";

        $sql = "INSERT INTO alunos (matricula, nome, email, telefone, aprovado_oab, doc_oab)
                VALUES ($matricula, '$nome', '$email', '$telefone', $aprovado_sql, '$doc_oab')";

        if ($conn->query($sql) === TRUE) {
            $importados++;
        }
    }

    echo "<script>
        alert('✅ Importação concluída! Foram importados {$importados} alunos.');
        window.top.location.href = '../paginas/main.php';
    </script>";
registrarLog($user_id, null, 'upload');


} catch (Exception $e) {
    echo "<script>
        alert('❌ Erro ao ler o arquivo: " . addslashes($e->getMessage()) . "');
        window.history.back();
    </script>";
}

$conn->close();
exit;
?>
