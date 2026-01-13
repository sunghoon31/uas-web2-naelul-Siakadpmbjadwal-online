<?php 

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        return (new MailMessage)
            ->subject('😵‍💫 Reset password dulu, baru mikir')
            ->greeting('Halo 👋😏')

            ->line('Jadi ceritanya kamu **lupa password** ya? 😌')
            ->line('Tenang. Manusiawi kok. Bahkan Admin juga suka lupa arah hidup.')
            ->line('Otak butuh istirahat 🧠💤, tapi akun kamu butuh password 😤')

            ->action('🔐 Gas Reset Password', $url)

            ->line('Link ini cuma aktif **60 menit** ⏰')
            ->line('Lebih lama dari fokus kamu, tapi jangan diuji ya 😬')

            ->line('Kalau ini **bukan kamu** 🤨')
            ->line('Yaudah sie. Berarti hidupmu cukup menarik sampai ada yang iseng 👀🔥')
            ->line('Abaikan aja. Kami juga biasa diabaikan.')

            ->salutation("Salam 🤙\nAdmin yang lupa cara bahagia 😔");
    }
}
