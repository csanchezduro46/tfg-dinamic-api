<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailCustom extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $temporarySignedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Extraer los parámetros y construir la URL del frontend Angular
        $parsedUrl = parse_url($temporarySignedUrl);
        parse_str($parsedUrl['query'], $queryParams);

        $frontendUrl = env('FRONTEND_URL') . '/verificar-email?' . http_build_query($queryParams + [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ]);

        return (new MailMessage)
            ->subject('Verifica tu dirección de correo electrónico')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Gracias por registrarte. Para completar el proceso, por favor verifica tu correo electrónico haciendo clic en el botón.')
            ->action('Verificar correo', $frontendUrl)
            ->line('Este enlace caducará en 1 hora.')
            ->line('Si no solicitaste este registro, puedes ignorar este mensaje.')
            ->salutation('¡Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
