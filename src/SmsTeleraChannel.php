<?php

namespace OnixSolutions\SmsTelera;

use Illuminate\Notifications\Notification;
use OnixSolutions\SmsTelera\Exceptions\CouldNotSendNotification;

class SmsTeleraChannel
{
    /** @var \OnixSolutions\SmsTelera\SmsTeleraApi */
    protected $smsc;

    public function __construct(SmsTeleraApi $smsc)
    {
        $this->smsc = $smsc;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! ($to = $this->getRecipients($notifiable, $notification))) {
            return;
        }

        $message = $notification->{'toSmsTelera'}($notifiable);

        if (\is_string($message)) {
            $message = new SmsTeleraMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    /**
     * Gets a list of phones from the given notifiable.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return string[]
     */
    protected function getRecipients($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('smsctelera', $notification);

        if ($to === null || $to === false || $to === '') {
            return [];
        }

        return is_array($to) ? $to : [$to];
    }

    protected function sendMessage($recipients, SmsTeleraMessage $message)
    {
        if (\mb_strlen($message->content) > 800) {
            throw CouldNotSendNotification::contentLengthLimitExceeded();
        }

        $params = [
            'numero'  => \implode(',', $recipients), //Número da mensagem (Obrigatório, padrão E164 ex: 5511988663344).
            'mensagem'     => $message->content,
            'servico' => (isset($message->servico) ? $message->servico : $this->smsc->tp), //Serviço da mensagem (Obrigatório, long ou short).
            'codificacao' => "1",  // Codificação da mensagem (Não obrigatório, 1 para 7-bits e 8 para 16-bits).
        ];

        $params = json_encode(array((object)$params)); // Body format to send to API

//        if ($message->sendAt instanceof \DateTimeInterface) {
//            $params['time'] = '0'.$message->sendAt->getTimestamp();
//        }

        $this->smsc->send($params);
    }
}
