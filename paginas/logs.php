<?php


require_once "../processamento/autorizacao.php";
Autorizacao::proteger();

require_once __DIR__ . "/../processamento/conexao.php";

/* === Conexão com o banco === */
$conn = CriarConexao();
$conn->set_charset('utf8mb4');

/* =========================
   FILTRO E BUSCA
   ========================= */
$busca  = $_GET['busca']  ?? '';
$filtro = $_GET['filtro'] ?? '';

$sql = "SELECT 
            l.idregistro AS id,
            u.name       AS usuario,
            l.`Ação`     AS acao,
            a.nome       AS aluno,
            l.`Data`     AS data_hora
        FROM logsmatheusvini l
        LEFT JOIN users  u ON u.id = l.user_id
        LEFT JOIN alunos a ON a.id = l.aluno_id
        WHERE 1=1";

/* filtros */
if ($busca !== '') {
    $buscaEsc = $conn->real_escape_string($busca);
    $sql .= " AND (u.name LIKE '%$buscaEsc%' OR a.nome LIKE '%$buscaEsc%')";
}
if ($filtro !== '') {
    $filtroEsc = $conn->real_escape_string($filtro);
    $sql .= " AND l.`Ação` = '$filtroEsc'";
}

$sql .= " ORDER BY l.`Data` DESC";

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Logs</title>
    <link rel="stylesheet" href="../css/logs.css">
</head>
<body>

<div id="logs-container">
    <h1>Relatório de Logs do Sistema</h1>

    <form id="busca-filtros" method="GET">
        <input type="text" id="campobusca" name="busca" placeholder="Buscar por usuário ou aluno..." value="<?= htmlspecialchars($busca) ?>">
        
        <select id="filtro" name="filtro">
            <option value="">Todas as ações</option>
            <option value="upload" <?= $filtro==='upload'?'selected':'' ?>>Upload</option>
           <option value="download" <?= $filtro==='download'?'selected':'' ?>>Download</option> 
            <option value="Exclusão" <?= $filtro==='Exclusão'?'selected':'' ?>>Exclusão</option>
            <option value="Edição" <?= $filtro==='Edição'?'selected':'' ?>>Edição</option>
            <option value="Inclusão" <?= $filtro==='Inclusão'?'selected':'' ?>>Inclusão</option>
        </select>

     
  <!-- Forçando o botao a ter estilo por erro no tamanho do icone  -->

        <button id="btn-buscar" type="submit"
        style="
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            background:linear-gradient(90deg, #7ED957, #2EB872);
            color:#fff;
            font-weight:600;
            font-size:15px;
            border:none;
            border-radius:10px;
            padding:10px 18px;
            cursor:pointer;
            box-shadow:0 2px 6px rgba(0,0,0,.15);
            transition:all .25s ease;
        "
        onmouseover="this.style.transform='scale(1.03)'"
        onmouseout="this.style.transform='scale(1)'">
        <img src="../icons/buscaricon.png"
       alt="Buscar"
       style="width:18px;height:18px;object-fit:contain;display:block;">
        Buscar
    </button>



    </form>

    <div class="tabela-wrapper">
        <table id="tabela-logs">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Aluno Modificado / Incluído</th>
                    <th>Data e Hora</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['usuario'] ?? '-') ?></td>
                        
                        <td style="display:flex; align-items:center; gap:8px;">
    <?php
        $acao = strtolower($row['acao']); // converte pra minúsculo pra facilitar
        $icone = '';

        // define ícone conforme a ação
        switch ($acao) {
            case 'upload':
                $icone = '../icons/logupload.png';
                break;
            case 'download':
                $icone = '../icons/logdownload.png';
                break;
            case 'exclusao':
            case 'exclusão':
                $icone = '../icons/logexclusao.png';
                break;
            case 'edicao':
            case 'edição':
                $icone = '../icons/logeditar.png';
                break;
            case 'inclusao':
            case 'inclusão':
                $icone = '../icons/loginclusao.png';
                break;
        }

        if ($icone && file_exists($icone)) {
            echo "<img src='{$icone}' alt='{$acao}' style='width:18px;height:18px;object-fit:contain;'>";
        }

        echo htmlspecialchars(ucfirst($acao));
    ?>
</td>



                        <td><?= htmlspecialchars($row['aluno'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['data_hora']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:20px;">Nenhum log encontrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// permite buscar com Enter
document.getElementById("campobusca").addEventListener("keypress", function(e) {
    if (e.key === "Enter") {
        e.preventDefault();
        document.getElementById("btn-buscar").click();
    }
});

// envia automaticamente ao trocar o filtro
document.getElementById("filtro").addEventListener("change", function() {
    document.getElementById("busca-filtros").submit();
});


</script>

</body>
</html>
