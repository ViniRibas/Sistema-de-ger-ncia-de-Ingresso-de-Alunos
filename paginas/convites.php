<?php

// Inclusões

require_once "../processamento/autorizacao.php";
Autorizacao::proteger();

include('../processamento/conexao.php');
require_once('../processamento/Sender.php');

$conn = CriarConexao();


//  Processamento

$mensagem_sucesso = "";
$mensagem_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinatario = $_POST['destinatario'] ?? '';
    $assunto      = $_POST['assunto'] ?? '';
    $mensagem     = $_POST['mensagem'] ?? '';

    if (empty($destinatario) || empty($assunto) || empty($mensagem)) {
        $mensagem_erro = "Preencha todos os campos antes de enviar.";
    } else {
        // MUDANÇA AQUI: Passamos o $conn como primeiro parâmetro
        $resultado = enviarEmailSimples($conn, $destinatario, $assunto, $mensagem);

        if ($resultado['status'] === true) {
            $mensagem_sucesso = $resultado['msg'];
        } else {
            $mensagem_erro = $resultado['msg'];
        }
    }
}


if (isset($_GET['exportar']) && $_GET['exportar'] === 'csv') {
    //. Define os cabeçalhos para download
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=emails_egressos.csv");
    
    //  Abre a saída de dados
    $output = fopen("php://output", "w");

    //  ADICIONA O BOM (Byte Order Mark) para o Excel reconhecer acentos (UTF-8)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    //  Cabeçalho das colunas
    // o ";" no final -> É ele que separa as colunas no Excel em português
    fputcsv($output, ["Nome", "Email"], ";");

    $sql = "SELECT nome, email FROM alunos";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            //  Escreve os dados usando ";" como separador
            fputcsv($output, [$row['nome'], $row['email']], ";");
        }
    }
    
    fclose($output);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Convites e Comunicados</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/convites.css">
</head>
<body>

    <h1>Convites e Comunicados</h1>

    <?php if ($mensagem_sucesso): ?>
        <div class="mensagem sucesso" style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 10px;">
            <?= htmlspecialchars($mensagem_sucesso) ?>
        </div>
    <?php elseif ($mensagem_erro): ?>
        <div class="mensagem erro" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 10px;">
            <?= htmlspecialchars($mensagem_erro) ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">
        <label for="destinatario">Destinatário (E-mail):</label>
        

        <input 
    type="email" 
    name="destinatario" 
    id="destinatario" 
    required 
    placeholder="exemplo@email.com"
    style="
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 6px;
        border: 1px solid #ccc;
        box-sizing: border-box;
    "
>




        <label for="assunto">Assunto:</label>
        <input type="text" name="assunto" id="assunto" required>

        <label for="mensagem">Mensagem:</label>
        <textarea name="mensagem" id="mensagem" required rows="5"></textarea>

        <button type="submit" style="margin-top: 10px;">Enviar comunicado</button>
        
        <a href="?exportar=csv">
            <button type="button" class="exportar">Exportar e-mails (CSV)</button>
        </a>
    </form>

</body>
</html>