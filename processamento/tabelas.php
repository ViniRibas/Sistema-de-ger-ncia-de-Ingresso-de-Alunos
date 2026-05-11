<?php
require_once __DIR__ . "/conexao.php";
require_once __DIR__ . "/../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$conn = CriarConexao();

/* ============================
   MÉTRICAS
============================ */
function totalAlunos($conn){

    if (!$conn) {
        return 0;
    }

    $q = $conn->query("SELECT COUNT(*) AS t FROM alunos");
    return $q->fetch_assoc()['t'] ?? 0;
}

function aprovados($conn){

    if (!$conn) {
        return 0;
    }

    $q = $conn->query("SELECT COUNT(*) AS t FROM alunos WHERE aprovado_oab = 's'");
    return $q->fetch_assoc()['t'] ?? 0;
}

function cursos($conn){

    if (!$conn) {
        return null;
    }

    return null; // sem coluna curso
}

/* ============================
   EXPORTAÇÃO EXCEL
============================ */
function exportarAlunos($conn, $pesquisa = '', $filtros = []) {

    if (!$conn) {
        return [];
    }



    if (ob_get_length()) ob_clean();

    // --- SQL ---
    $sql = "SELECT * FROM alunos WHERE 1=1";

    if ($pesquisa !== "") {
        $sql .= " AND (nome LIKE '%$pesquisa%' 
                   OR email LIKE '%$pesquisa%' 
                   OR id LIKE '%$pesquisa%')";
    }

    $cond = [];
    if (in_array('aprovado', $filtros))  $cond[] = "aprovado_oab = 's'";
    if (in_array('reprovado', $filtros)) $cond[] = "aprovado_oab = 'n'";
    if (in_array('pendente', $filtros)) $cond[] = "(doc_oab IS NULL OR doc_oab = '')";

    if (!empty($cond)) {
        $sql .= " AND (" . implode(" OR ", $cond) . ")";
    }

    $res = $conn->query($sql);
    if (!$res) {
    return [];
}

    // --- PLANILHA ---
    $spreadsheet = new Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Alunos");

    // Cabeçalho
    $cab = ["ID","Nome","Email","Celular","Situação OAB","Documento OAB"];
    $col = "A";

    foreach ($cab as $h) {
        $sheet->setCellValue($col . "1", $h);
        $sheet->getStyle($col . "1")->getFont()->setBold(true);
        $col++;
    }

    // Dados
    $linhaExcel = 2;

    while($row = $res->fetch_assoc()){
        $situacao = ($row['aprovado_oab'] == 's') ? "Aprovado" :
                    (($row['aprovado_oab'] == 'n') ? "Reprovado" : "Pendente");

        $sheet->setCellValue("A$linhaExcel", $row['id']);
        $sheet->setCellValue("B$linhaExcel", $row['nome']);
        $sheet->setCellValue("C$linhaExcel", $row['email']);
        $sheet->setCellValue("D$linhaExcel", $row['celular']);
        $sheet->setCellValue("E$linhaExcel", $situacao);
        $sheet->setCellValue("F$linhaExcel", $row['doc_oab']);

        $linhaExcel++;
    }

    // Ajustar largura
    foreach(range('A','F') as $c){
        $sheet->getColumnDimension($c)->setAutoSize(true);
    }

    // --- DOWNLOAD ---
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=alunos_exportados.xlsx");
    header("Cache-Control: max-age=0");

    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}


/* ============================
   FILTRO DA TABELA
============================ */
function filtroAlunos($conn): mixed{
    
    if (!$conn) {
        return [];
    }

    $pesquisa = $_GET["pesquisa"] ?? '';
    $filtros  = $_GET['filtro'] ?? [];

    if (!is_array($filtros)) $filtros = [];

    $sql = "SELECT * FROM alunos WHERE 1=1";

    if ($pesquisa !== "") {
        $sql .= " AND (nome LIKE '%$pesquisa%' 
                   OR email LIKE '%$pesquisa%' 
                   OR id LIKE '%$pesquisa%')";
    }

    $cond = [];
    if (in_array('aprovado', $filtros))  $cond[] = "aprovado_oab = 's'";
    if (in_array('reprovado', $filtros)) $cond[] = "aprovado_oab = 'n'";
    if (in_array('pendente', $filtros)) $cond[] = "(doc_oab IS NULL OR doc_oab = '')";

    if (!empty($cond)) {
        $sql .= " AND (" . implode(" OR ", $cond) . ")";
    }

    return $conn->query($sql);
}

/* ============================
   LISTA PADRÃO
============================ */
function tabela($conn){
    
    if (!$conn) {
        return [];
    }


    $pesq = $_GET["pesquisa"] ?? '';

    $sql = "SELECT * FROM alunos WHERE 1=1";

    if ($pesq !== ""){
        $sql .= " AND (nome LIKE '%$pesq%' 
                   OR email LIKE '%$pesq%' 
                   OR id LIKE '%$pesq%')";
    }

    $res = $conn->query($sql);
    if (!$res) {
    return [];
}

    $array = [];
    while($r = $res->fetch_assoc()){
        $array[] = $r;
    }

    return $array;
}

?>
