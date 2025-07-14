<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PropellerApiClient
{
    private const ENDPOINT_URL = 'https://devtest-crm-api.standard.aws.prop.cm';
    private const AUTHORIZATION = 'Bearer 01JZWVF93C2R0X7H8QEPFZBDFE';
    private const BODY = [
        'headers' => [
            'Authorization' => self::AUTHORIZATION,
            'Content-Type' => 'application/json',
        ]
    ];

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(
        HttpClientInterface $client,
        LoggerInterface $logger
    )
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Access Propeller api.
     *
     * @param string $action
     * @param string $apiName
     * @param array $parameters
     *
     * @return array|mixed
     */
    public function accessEndpoint(string $action, $apiName = null, $parameters = []): mixed
    {
        $body = self::BODY;

        if (!empty($parameters)) {
            $body['json'] = $parameters;
        }

        try {
            $response = $this->client->request(
                $action, 
                self::ENDPOINT_URL . '/' . $apiName, 
                $body
            );

            if ($response->getStatusCode() == 200) {
                return $response->toArray();
            }

        } catch (ClientExceptionInterface $e) {
            $this->logger->error('Propeller API Client queryLog - [ClientException]' . $e->getMessage());
        } catch (ServerExceptionInterface $e) {
            $this->logger->error('Propeller API Client queryLog - [ServerException]' . $e->getMessage());
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Propeller API Client queryLog - [TransportException]' . $e->getMessage());
        }

        return [];
    }
}
