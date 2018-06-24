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
        $this->endpoint = Arr::get($config, 'host', 'http://sms.telera.co/app/');

        $this->client = new HttpClient([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
    }

    public function send($params)
    {
        $base = [
            'charset' => 'utf-8',
            'tp'     => $this->tp,
            'tk'   => $this->tk,
        ];

        $params = \array_merge($base, \array_filter($params));

        try {
            $response = $this->client->request('POST', $this->endpoint, ['form_params' => $params]);

            $response = \json_decode((string) $response->getBody(), true);

            if (isset($response['error'])) {
                throw new \DomainException($response['error'], $response['error_code']);
            }

            return $response;
        } catch (\DomainException $exception) {
            throw CouldNotSendNotification::smscRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithSmsc($exception);
        }
    }
}
