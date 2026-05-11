

<?php
require_once "../processamento/autorizacao.php";
Autorizacao::proteger();


require_once "../processamento/conexao.php";
require_once "../processamento/logProcessamento.php";
require_once "../processamento/tabelas.php";
require_once __DIR__ . "/../vendor/autoload.php";

$conn = CriarConexao();
$user_id = $_SESSION['user_id'] ?? 0;



$totalAlunosBanco = totalAlunos($conn);
$totalAprovados = aprovados($conn);
$totalCursos = cursos($conn);

// Captura filtros e pesquisa
$pesquisa = $_GET["pesquisa"] ?? '';
$filtros = $_GET['filtro'] ?? [];
if (!is_array($filtros)) $filtros = [];

// Se houver filtros aplicados, use filtroAlunos(), senão use tabela()
if (!empty($filtros)) {
    $registros = filtroAlunos($conn);
} else {
    $registros = tabela($conn);
}

// Exportação para Excel
if (isset($_GET['exportar']) && $_GET['exportar'] === 'excel') {
    registrarLog($user_id, null, 'download'); 
    exportarAlunos($conn, $pesquisa, $filtros);
}
?>


<link rel="stylesheet" href="../css/relatorios.css">

<div id="conteudo-principal">

    <h1>Relatórios</h1>

    <!-- ================= PAINÉIS ================= -->
    <div class="painel-container">
        <div class="painel">
            <h2><?= $totalAlunosBanco ?></h2>
            <p>Total de alunos cadastrados</p>
        </div>
        <div class="painel">
            <h2><?= $totalAprovados ?></h2>
            <p>Alunos aprovados na OAB</p>
        </div>
        <div class="painel">
            <h2><!-- <?= $totalCursos ?? '—' ?> --></h2>
            <p><!-- Total de cursos distintos --></p>
        </div>
    </div>

    <!-- ================= FORMULÁRIO DE PESQUISA ================= -->
    <form action="relatorios.php" method="get" style="display:flex; gap:10px; align-items:center;">
        <input type="text" name="pesquisa" value="<?= htmlspecialchars($pesquisa) ?>" placeholder="Buscar..." style="flex:1; padding:10px 14px; border-radius:10px; border:1px solid #ccc;">

        <button type="submit" name="buscar" class="btn btn-buscar">
            <img src="../icons/buscaricon.png" alt="Buscar" class="icon-btn">
            Buscar
        </button>

        <button type="submit" name="exportar" value="excel" class="btn btn-excel">
            <img src="../icons/excelicon.png" alt="Excel" class="icon-btn">
            Excel   
        </button>
    </form>


<form action="relatorios.php" method="get" style="display:flex; gap:10px; align-items:center;">
    <button type="submit" class="btn btn-filtro">
        <img src="../icons/filtroicon.png" alt="Filtro" class="icon-btn">
        Aplicar Filtros
    </button>

    <label>
        <input type="checkbox" name="filtro[]" value="aprovado" <?= in_array('aprovado', $filtros) ? 'checked' : '' ?>>
        Aprovados
    </label>

    <label>
        <input type="checkbox" name="filtro[]" value="reprovado" <?= in_array('reprovado', $filtros) ? 'checked' : '' ?>>
        Reprovados
    </label>

    <label>
        <input type="checkbox" name="filtro[]" value="pendente" <?= in_array('pendente', $filtros) ? 'checked' : '' ?>>
        Pendentes
    </label>
</form>




    <!-- ================= TABELA PRINCIPAL ================= -->
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%; margin-top:15px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Celular</th>
                <th>Situação OAB</th>
                <th>Documento OAB</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $linha): 
                $aprovado  = $linha['aprovado_oab'];
                $documento = $linha['doc_oab'];

                $situacao = ($aprovado == 's') ? "<span style='color:green;font-weight:bold;'>Aprovado</span>" :
                           (($aprovado == 'n') ? "<span style='color:red;font-weight:bold;'>Reprovado</span>" :
                           "<span style='color:gray;'>Pendente</span>");

                $link_doc = !empty($documento) ? "<a href='../uploads/$documento' target='_blank'>Baixar</a>" :
                           "<span style='color:gray;'>—</span>";
            ?>
            <tr>
                <td><?= $linha['id'] ?></td>
                <td><?= htmlspecialchars($linha['nome']) ?></td>
                <td><?= htmlspecialchars($linha['email']) ?></td>
                <td><?= htmlspecialchars($linha['celular']) ?></td>
                <td><?= $situacao ?></td>
                <td><?= $link_doc ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
