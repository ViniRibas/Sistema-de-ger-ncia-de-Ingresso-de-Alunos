<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// ==================================================================
// SOLUÇÃO DO ERRO "CLASS NOT FOUND"
// Em vez de confiar no autoload, carregamos os arquivos manualmente.
// Isso garante que o PHP ache a biblioteca na sua pasta vendor.
// ==================================================================
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

// ATENÇÃO: Adicionei $conn como primeiro parâmetro
function enviarEmailSimples($conn, $destinatario, $assunto, $mensagem) {
    
    // Configurações SMTP
    $smtpConfig = [
        'host'       => 'smart.iagentesmtp.com.br',
        'port'       => 587,
        'username'   => 'brum@faculdadedombosco.edu.br',
        'password'   => 'UXTSQ@Z&',
        'from_email' => 'alguem@faculdadedombosco.com.br', //email de quem enviou
        'from_name'  => 'FDB Integrador'
    ];

    $mail = new PHPMailer(true);

    try {
        // --- 1. Configuração e Envio ---
        $mail->isSMTP();
        $mail->Host       = $smtpConfig['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpConfig['username'];
        $mail->Password   = $smtpConfig['password'];
        $mail->Port       = $smtpConfig['port'];
        $mail->CharSet    = 'UTF-8';
        
        $mail->setFrom($smtpConfig['from_email'], $smtpConfig['from_name']);
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = nl2br($mensagem);
        $mail->AltBody = strip_tags($mensagem);

        $mail->send();

        // --- 2. Registro no Banco de Dados (Log) ---
        $data_agora = date('Y-m-d H:i:s');
        $status     = 'e'; // 'e' de enviado

        // OBSERVAÇÃO: Seu código estava 'INSERT INTO email' (singular).
        // Se a tabela no banco for 'emails', adicione um 's' abaixo.
        $sql = "INSERT INTO email (remetente, destinatario, data_hora, data_envio, mensagem, status, assunto) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssssss", 
                $smtpConfig['from_email'], // remetente
                $destinatario,             // destinatario
                $data_agora,               // data_hora
                $data_agora,               // data_envio
                $mensagem,                 // mensagem
                $status,                   // status
                $assunto                   // assunto
            );
            $stmt->execute();
            $stmt->close();
        }

        return ['status' => true, 'msg' => 'E-mail enviado e registrado com sucesso!'];

    } catch (Exception $e) {
        return ['status' => false, 'msg' => "Erro no envio: {$mail->ErrorInfo}"];
    }
}
?>