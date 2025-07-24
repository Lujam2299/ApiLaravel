<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Apn\ApnChannel;
use NotificationChannels\Apn\ApnMessage;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        // Define los canales por los que se enviará la notificación
        // En este caso, Firebase Cloud Messaging (FCM) y Apple Push Notification (APN)
        return [FcmChannel::class, ApnChannel::class];
    }

    public function toFcm($notifiable)
    {
        // Configuración de la notificación para Firebase Cloud Messaging (FCM)
        // Esta estructura es compatible con aplicaciones móviles (React Native, Flutter, etc.)

        return FcmMessage::create()
            // Establece datos personalizados que se enviarán con la notificación
            // Son accesibles en la aplicación móvil cuando se recibe la notificación
            ->setData([
                'conversation_id' => (string) $this->message->conversation_id, // ID de la conversación
                'message_id' => (string) $this->message->id,                 // ID del mensaje
                'user_id' => (string) $this->message->user_id,               // ID del usuario que envió el mensaje
                'type' => 'new_message',                                    // Tipo de notificación (personalizado)
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'              // Acción al hacer clic, para Flutter/React Native
            ])
            // Configura el contenido de la notificación (título y cuerpo visible para el usuario)
            ->setNotification(FcmNotification::create()
                ->setTitle('Nuevo mensaje') // Título de la notificación
                ->setBody($this->message->user->name.': '.$this->message->body) // Cuerpo del mensaje
            )
            // Configuración específica para dispositivos Android
            ->setAndroid(
                AndroidConfig::create()
                    // Opciones de FCM para Android (ej. etiquetas de análisis)
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('android_notification'))
                    // Configuración de la notificación Android
                    ->setNotification(AndroidNotification::create()
                        ->setColor('#0A0A0A')                             // Color del icono de la notificación
                        ->setClickAction('FLUTTER_NOTIFICATION_CLICK')   // Acción al hacer clic en Android
                        ->setChannelId('messages_channel')               // ID del canal de notificación (para Android O+ para prioridades y sonidos)
                    )
            )
            // Configuración específica para dispositivos Apple (iOS)
            ->setApns(
                ApnsConfig::create()
                    // Opciones de FCM para APNS (ej. etiquetas de análisis)
                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('ios_notification'))
                    // Carga útil de APNS (configuración avanzada para iOS)
                    ->setPayload([
                        'aps' => [
                            'sound' => 'default',         // Sonido de notificación por defecto
                            'badge' => 1,                 // Incrementa el contador de insignias en el icono de la app
                            'content-available' => 1      // Para notificaciones silenciosas o en segundo plano
                        ]
                    ])
            );
    }

    public function toApn($notifiable)
    {
        // Configuración de la notificación para Apple Push Notification (APN) directamente (sin FCM)
        // Esto se usaría si no pasas por FCM para iOS

        return ApnMessage::create()
            ->title('Nuevo mensaje')
            ->body($this->message->user->name.': '.$this->message->body)
            ->badge(1)          // Contador de insignias
            ->sound('default')  // Sonido de notificación
            // Datos personalizados para la carga útil de APN
            ->custom('conversation_id', $this->message->conversation_id)
            ->custom('message_id', $this->message->id)
            ->custom('type', 'new_message')
            // Define una acción específica que puede ser manejada por la aplicación iOS
            ->action('view_message', [
                'conversation_id' => $this->message->conversation_id,
                'message_id' => $this->message->id
            ]);
    }
}
