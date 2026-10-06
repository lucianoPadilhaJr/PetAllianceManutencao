<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = getenv('SMTP_USER');
        $this->mail->Password = getenv('SMTP_PASS');
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = getenv('SMTP_PORT') ?: 587;
        $this->mail->setFrom(
            getenv('SMTP_FROM_EMAIL') ?: 'noreply@petalliance.com',
            getenv('SMTP_FROM_NAME') ?: 'PetAlliance'
        );
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }

    public function enviarVerificacao($email, $nome, $token, $codigo) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $nome);

            $link = "http://{$_SERVER['HTTP_HOST']}/backEnd/verificarEmail.php?token=" . urlencode($token);

            $this->mail->Subject = 'PetAlliance - Verifique seu email';
            $this->mail->Body = "
                <h2>Olá, $nome!</h2>
                <p>Bem-vindo ao PetAlliance! Verifique seu email clicando no link abaixo:</p>
                <p><a href='$link' style='display:inline-block;padding:12px 24px;background:#4CAF50;color:white;text-decoration:none;border-radius:4px;'>Verificar Email</a></p>
                <p>Ou insira o código de verificação na plataforma:</p>
                <p style='font-size:24px;font-weight:bold;letter-spacing:4px;color:#333;'>$codigo</p>
                <p>Este código expira em 30 minutos.</p>
                <p>Se você não criou uma conta, ignore este email.</p>
            ";
            $this->mail->AltBody = "Olá, $nome! Verifique seu email clicando no link: $link Ou insira o código: $codigo";

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar email: " . $e->getMessage());
            return false;
        }
    }

    public function enviarRecuperacao($email, $nome, $token, $codigo) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $nome);

            $link = "http://{$_SERVER['HTTP_HOST']}/backEnd/redefinirSenha.php?token=" . urlencode($token);

            $this->mail->Subject = 'PetAlliance - Recuperação de senha';
            $this->mail->Body = "
                <h2>Olá, $nome!</h2>
                <p>Recebemos uma solicitação para redefinir sua senha.</p>
                <p>Clique no link abaixo para criar uma nova senha:</p>
                <p><a href='$link' style='display:inline-block;padding:12px 24px;background:#2196F3;color:white;text-decoration:none;border-radius:4px;'>Redefinir Senha</a></p>
                <p>Ou insira o código de recuperação na plataforma:</p>
                <p style='font-size:24px;font-weight:bold;letter-spacing:4px;color:#333;'>$codigo</p>
                <p>Este código expira em 30 minutos.</p>
                <p>Se você não solicitou esta recuperação, ignore este email.</p>
            ";
            $this->mail->AltBody = "Olá, $nome! Redefina sua senha clicando no link: $link Ou insira o código: $codigo";

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar email: " . $e->getMessage());
            return false;
        }
    }

    public function enviarAlteracaoEmail($email, $nome, $token) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email, $nome);

            $link = "http://{$_SERVER['HTTP_HOST']}/backEnd/verificarEmail.php?token=" . urlencode($token) . "&tipo=alteracao_email";

            $this->mail->Subject = 'PetAlliance - Confirme seu novo email';
            $this->mail->Body = "
                <h2>Olá, $nome!</h2>
                <p>Você solicitou a alteração do seu email.</p>
                <p>Clique no link abaixo para confirmar o novo email:</p>
                <p><a href='$link' style='display:inline-block;padding:12px 24px;background:#2196F3;color:white;text-decoration:none;border-radius:4px;'>Confirmar novo email</a></p>
                <p>Se você não solicitou esta alteração, ignore este email.</p>
            ";
            $this->mail->AltBody = "Confirme seu novo email clicando no link: $link";

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar email: " . $e->getMessage());
            return false;
        }
    }
}
