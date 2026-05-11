<?php
require_once __DIR__ . "/conexao.php";

/**
 * Registra um log de ação do usuário.
 *
 * @param int $user_id   ID do usuário que fez a ação.
 * @param int|null $aluno_id  ID do aluno envolvido (ou null).
 * @param string $acao    Tipo de ação (upload, inclusao, edicao, exclusao, download).
 */
function registrarLog($user_id, $aluno_id, $acao) {
    $conn = CriarConexao();
    $conn->set_charset('utf8mb4');

    $stmt = $conn->prepare("INSERT INTO logsmatheusvini (user_id, aluno_id, `Ação`, `Data`) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $user_id, $aluno_id, $acao);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
