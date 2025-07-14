<?php

namespace App\Controller;

use App\Service\PropellerApiClient;
use App\Validator\SubscriberValidator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubscriberController extends AbstractController
{
    /**
     * @var PropellerApiClient
     */
    protected $propellerApiClient;

    /**
     * @var SubscriberValidator
     */
    protected $validator;

    /**
     * @param PropellerApiClient $propellerApiClient,
     * @param SubscriberValidator $validator,
     */
    public function __construct(
        PropellerApiClient $propellerApiClient,
        SubscriberValidator $validator
    ) {
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
    /**
     * Add a new subscriber
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createSubscriber(Request $request): JsonResponse
    {
        if ($request->isMethod('post')) {
            try {
                if (!$this->validator->isValid($request)) {
                    throw new InvalidArgumentException();
                }

                $subscriberData = $this->getSubscriberData();

                //Check to see if the subscriber with the emailAddress already exists
                if (!$this->validator->isEmailDuplicate(
                    $request->get('emailAddress'), 
                    $subscriberData)
                ) {
                    throw new InvalidArgumentException();
                }
                
                $parameters = [
                    'emailAddress' => $request->get('emailAddress'),
                    'firstName' => $request->get('firstName'),
                    'lastName' => $request->get('lastName'),
                    'marketingConsent' => $request->get('marketingConsent') == 'yes' ? true : false,
                    'dateOfBirth' => $request->get('dateOfBirth'),
                ];

                $createSubscriber = $this->propellerApiClient->accessEndpoint(
                    'POST', 
                    'api/subscriber/', 
                    $parameters
                );

                if (empty($createSubscriber)) {
                    return new JsonResponse([
                        'status' => 'Action Failed',
                        'message' => 'Could not create the subscriber'
                    ]);
                }

                $subscriberData[] = [
                    'id' => $createSubscriber['subscriber']['id'],
                    'email'=> $createSubscriber['subscriber']['emailAddress']
                ];

                file_put_contents($this->getSubscriberFile(), json_encode($subscriberData, JSON_PRETTY_PRINT));

                return new JsonResponse([
                    'status' => 'success',
                    'message' => 'Subscriber created Successfully'
                ]);
            } catch (InvalidArgumentException $e) {
                error_log($e->getMessage());
                $errors = $this->validator->getErrors();

                return new JsonResponse($errors);
            }
        }
    }

    /**
     * Get the subscriber data file in the project repo
     *
     * @return string|null
     */
    public function getSubscriberFile(): ?string
    {
        $file = $this->getParameter('kernel.project_dir') . '/var/subscriberData/subscriber.json';

        if (file_exists($file)) {
            return $file;
        } else {
            return null;
        }
    }

    /**
     * Get the subscriber data saved internally
     *
     * @return array
     */
    public function getSubscriberData(): array
    {
        $file = $this->getSubscriberFile();

        if (empty($file)) {
            return [];
        }

        $data = json_decode(file_get_contents($file), true);

        if(isset($data)) {
            return $data;
        } 

        return [];
    }
}

