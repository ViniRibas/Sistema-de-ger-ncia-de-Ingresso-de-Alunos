<?php
include("conexao.php");

// Se o formulário foi enviado
if (isset($_POST['enviar'])) {
    $diretorio = "upload/";
    $arquivo = $_FILES['arquivo'];

    // Nome final (único) do arquivo
    $nomeFinal = uniqid() . "-" . basename($arquivo["name"]);
    $caminhoCompleto = $diretorio . $nomeFinal;

    // Verifica se é um upload válido
    if (move_uploaded_file($arquivo["tmp_name"], $caminhoCompleto)) {
        // Grava no banco o caminho do arquivo
        $sql = "INSERT INTO arquivos (caminho) VALUES ('$caminhoCompleto')";
        if ($conn->query($sql) === TRUE) {
            echo "Arquivo enviado e caminho gravado com sucesso!<br>";
            echo "Caminho: " . $caminhoCompleto;
        } else {
            echo "Erro ao gravar no banco: " . $conn->error;
        }
    } else {
        echo "Erro ao fazer upload do arquivo.";
    }
}
?>

<!-- Formulário HTML -->
<form method="post" enctype="multipart/form-data">
    <label>Selecione um arquivo:</label><br>
    <input type="file" name="arquivo" required><br><br>
    <button type="submit" name="enviar">Enviar</button>
</form>
