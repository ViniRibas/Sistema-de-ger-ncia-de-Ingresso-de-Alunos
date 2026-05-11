<?php

$host = "193.123.113.234";
$db = "iea";
$user = "iea";
$pass = "iea";

$pesquisa= $_GET["pesquisa"];
$buscar  = $_GET["buscar"];
$id      = $_GET["id"];
$acao    = $_GET["acao"];


/// Continuar na proxima aula
// Inclusão
    



try {
    
    // Conexão com o banco
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if ($acao=='deletar')
{
    $sql = "delete   FROM alunos  WHERE  id = $id";    
    $stmt = $pdo->query($sql);

}





    if(!empty($id) && empty($pesquisa))
{
    $sql = "SELECT matricula,nome,email,celular,id  FROM alunos  WHERE  id = $id";    
    $stmt = $pdo->query($sql);
    echo $sql;
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
     foreach ($registros as $linha) {
            $nome    = $linha['nome'];
            $email   = $linha['email'];
            $id      = $linha['id'];
     }       
}        

    // SELECT simples
if(!empty($pesquisa))
{
    $stmt = $pdo->query("SELECT matricula,nome,email,celular,id  FROM alunos  WHERE  nome LIKE '%$pesquisa%'");
}

else{
    $stmt = $pdo->query("SELECT matricula,nome,email,celular,id FROM alunos  ");

}
 

    // Busca todos os registros em formato associativo
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Exibir os resultados
    

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela Simples</title>
    <style>        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%; /* Largura da tabela */
            border-collapse: collapse; /* Remove o espaçamento entre bordas */
        }

        th, td {
            border: 1px solid #ddd; /* Borda simples */
            padding: 8px; /* Espaçamento interno das células */
            text-align: left;
        }

        th {
            background-color: #f2f2f2; /* Cor de fundo para o cabeçalho */
            color: #333;
        }
        text {
            background-color: #f2f2f2ff; /* Cor de fundo para o cabeçalho */
            color: #333;
        }
        button {
            background-color: #f2f2f2ff; /* Cor de fundo para o cabeçalho */
            color: #333;
        }
        

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Cor de fundo diferente para linhas pares (listras) */
        }
    </style>
</head>
<body>
<form action="lista.php" method="get">     
    <h1>Exemplo</h1>
    <table>
    <input type="text" name="pesquisa">    
    <br>
    <br>
    <br>
    id <input type="text" name="id" value='<?php echo $id?>'>    
    <br>
    nome:<input type="text" name="nome" value='<?php echo $nome?>'>    
    <br>
    email:<input type="text" name="email" value='<?php echo $email?>'>    
    
    <input type="submit" name="buscar">    
        <thead>
            <tr>
                <th>id</th>
                <th>Nome</th>
                <th>email</th>
                <th>celular</th>
                <th>deletar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
          <?PHP
           foreach ($registros as $linha) {
            $nome   = $linha['nome'];
            $email   = $linha['email'];
            $celular = $linha['celular'];
            $id = $linha['id'];
           echo "<td><a href='lista.php?id=$id'>$id</a></td>";
           echo "<td>$nome</td>";
           echo "<td>$email</td>";
           echo "<td>$celular</td>";
           echo "<td><a href='lista.php?acao=deletar&id=$id'>deletar</a></td>";
           echo "</tr>";
        }
            ?>            
        </tbody>
    </table>
    </form>
</body>
</html>