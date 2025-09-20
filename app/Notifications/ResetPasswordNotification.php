<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    // e-posta içeriğini özelleştirmek için
    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage())
            ->subject('Şifre Sıfırlama Talebiniz')
            ->greeting('Merhaba!')
            ->line('Hesabınız için bir şifre sıfırlama talebi aldığımız için bu e-postayı alıyorsunuz.')
            ->action('Şifreyi Sıfırla', $url)
            ->line('Bu şifre sıfırlama bağlantısı '
                . config('auth.passwords.'.config('auth.defaults.passwords').'.expire')
                . ' dakika sonra sona erecektir.')
            ->line('Eğer bu işlemi siz yapmadıysanız, bu e-postayı görmezden gelin.')
            ->salutation('Saygılarımızla, ' . config('app.name'));
    }
}
