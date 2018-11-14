<?php

namespace OnixSolutions\SmsTelera;

use Illuminate\Support\Arr;
use GuzzleHttp\Client as HttpClient;
use OnixSolutions\SmsTelera\Exceptions\CouldNotSendNotification;

class SmsTeleraApi
{
    const FORMAT_JSON = 3;

    /** @var HttpClient */
    protected $client;

    /** @var string */
    protected $endpoint;

    /** @var string */
    protected $login;

    /** @var string */
    protected $secret;

    /** @var string */
    protected $sender;

    public function __construct(array $config)
    {
        $this->tk = Arr::get($config, 'tk');
        $this->tp = Arr::get($config, 'tp');
        $this->endpoint = Arr::get($config, 'host', 'http://apisms.multibr.com/mt');

        $this->client = new HttpClient([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
    }

    public function send($params)
    {
        $header = [
            'Authorization' => 'Bearer '.$this->tk,
            'Content-Type'  => 'application/json',
        ];

        //$params = \array_filter($params);

        try {
            $response = $this->client->request('POST', $this->endpoint, ['body'=>$params, 'headers' => $header]);   //'form_params' => $params

            $response = \json_decode((string) $response->getBody(), true);

            if (!isset($response['status']) or $response['status'] != 200
                or !isset($response['detail'][0]['status']) or $response['detail'][0]['status'] != 'ACCEPTED') {

                if($response['status'] != 200)
                    throw new \DomainException($response['detail'], $response['status']);

                $errors_msg = [
                    "ACCEPTED" => "Aguardando",
                    "PAYREQUIRED" => "Sem Saldo",
                    "UNKNOWN"  => "Falhada"
                ];
                throw new \DomainException($response['detail'][0]['status'].'['.$errors_msg[$response['detail'][0]['status']].']', $response['status']);
            }

            return $response;
        } catch (\DomainException $exception) {
            throw CouldNotSendNotification::smscRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithSmsc($exception);
        }
    }
}
