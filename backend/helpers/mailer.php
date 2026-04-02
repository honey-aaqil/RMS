<?php
/**
 * RecruiterMS — Brevo SMTP Mailer Helper
 * 
 * Sends verification emails via Brevo SMTP using PHPMailer.
 * Dark-themed HTML email matching the RecruiterMS design system.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail(string $toEmail, string $toName, string $token): bool {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration — Brevo
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['BREVO_SMTP_USER'] ?? '';
        $mail->Password   = $_ENV['BREVO_SMTP_KEY'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender / Recipient
        $fromEmail = $_ENV['BREVO_EMAIL_FROM'] ?? $_ENV['BREVO_SMTP_USER'] ?? 'noreply@recruiterms.com';
        $mail->setFrom($fromEmail, 'RecruiterMS');
        $mail->addAddress($toEmail, $toName);

        // Build verification URL
        $appUrl = $_ENV['APP_URL'] ?? 'http://localhost:5173';
        $verifyUrl = "{$appUrl}/verify-email?token={$token}";

        // HTML Email — Dark themed matching RecruiterMS design system
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your RecruiterMS Account';
        $mail->Body    = getVerificationEmailHTML($toName, $verifyUrl, $token);
        $mail->AltBody = getVerificationEmailPlainText($toName, $verifyUrl);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

function getVerificationEmailHTML(string $name, string $verifyUrl, string $token): string {
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
</head>
<body style="margin: 0; padding: 0; background-color: #020B18; font-family: 'Inter', Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #020B18; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="520" cellspacing="0" cellpadding="0" border="0" style="background-color: #0A1628; border-radius: 16px; border: 1px solid #1A2E50; overflow: hidden; max-width: 520px;">
                    
                    <!-- Header with gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #020B18 0%, #0A1628 50%, #001A4D 100%); padding: 40px 40px 30px; text-align: center;">
                            <div style="font-size: 28px; font-weight: 700; color: #F0F6FF; margin-bottom: 4px;">
                                ⬡ RecruiterMS
                            </div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px 40px 40px;">
                            <h1 style="color: #F0F6FF; font-size: 24px; font-weight: 700; margin: 0 0 16px; text-align: center;">
                                Verify Your Email
                            </h1>
                            <p style="color: #7A9CC4; font-size: 15px; line-height: 1.6; margin: 0 0 8px;">
                                Hi {$name},
                            </p>
                            <p style="color: #7A9CC4; font-size: 15px; line-height: 1.6; margin: 0 0 32px;">
                                Thanks for signing up for RecruiterMS! Please verify your email address by clicking the button below.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <a href="{$verifyUrl}" target="_blank" style="display: inline-block; background-color: #004BBD; color: #FFFFFF; font-size: 16px; font-weight: 600; text-decoration: none; padding: 14px 48px; border-radius: 8px; letter-spacing: 0.02em;">
                                            Verify My Account
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #3D5A80; font-size: 13px; line-height: 1.5; margin: 32px 0 0; text-align: center;">
                                This verification link expires in <strong style="color: #FFB830;">24 hours</strong>.
                            </p>

                            <p style="color: #3D5A80; font-size: 12px; line-height: 1.5; margin: 16px 0 0; text-align: center;">
                                If the button doesn't work, copy and paste this link:<br>
                                <a href="{$verifyUrl}" style="color: #004BBD; word-break: break-all; font-size: 11px;">{$verifyUrl}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 40px; border-top: 1px solid #1A2E50;">
                            <p style="color: #3D5A80; font-size: 12px; margin: 0; text-align: center;">
                                If you didn't create an account, you can safely ignore this email.
                            </p>
                            <p style="color: #3D5A80; font-size: 11px; margin: 8px 0 0; text-align: center;">
                                © 2026 RecruiterMS. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}

function getVerificationEmailPlainText(string $name, string $verifyUrl): string {
    return <<<TEXT
RecruiterMS — Verify Your Email

Hi {$name},

Thanks for signing up for RecruiterMS! Please verify your email address by visiting the link below:

{$verifyUrl}

This verification link expires in 24 hours.

If you didn't create an account, you can safely ignore this email.

© 2026 RecruiterMS. All rights reserved.
TEXT;
}
