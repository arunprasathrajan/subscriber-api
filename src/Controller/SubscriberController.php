<?php

namespace App\Controller;

use App\Service\PropellerApiClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubscriberController extends AbstractController
{
    /**
     * @var PropellerApiClient
     */
    protected $propellerApiClient;

    /**
     * @param PropellerApiClient $propellerApiClient,
     */
    public function __construct(PropellerApiClient $propellerApiClient)
    {
        $this->propellerApiClient = $propellerApiClient;
        $this->validator = $validator;
    }

    /**
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $propellerResponse = $this->propellerApiClient->accessEndpoint('GET');

        if (empty($propellerResponse)) {
            return new JsonResponse([
                'error' => 'Connection Failed',
                'message' => 'Could not connect'
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
            'message' => 'Welcome'
        ]);
    }
}
