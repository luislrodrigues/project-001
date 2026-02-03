<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Construir la URL del enlace de restablecimiento para la SPA.
     */
    protected function resetUrl($notifiable): string
    {
        $url = config('app.frontend_url', config('app.url'));
        $token = $this->token;
        $email = $notifiable->getEmailForPasswordReset();

        return rtrim($url, '/') . "/reset-password/{$token}?email=" . urlencode($email);
    }

    /**
     * Obtener la representación por correo de la notificación.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Restablecer contraseña')
            ->line('Estás recibiendo este correo porque se ha solicitado un restablecimiento de contraseña para tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace caducará en ' . config('auth.passwords.users.expire', 60) . ' minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este correo.');
    }
}
