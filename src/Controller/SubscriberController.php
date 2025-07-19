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
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['error' => 'Method Not Allowed'], 405);
        }

        if ($request->isMethod('post')) {
            try {
                if (!$this->validator->isValid($request)) {
                    throw new InvalidArgumentException();
                }

                $propellerSubscribers = $this->getAllSubscribers();

                //Check to see if the subscriber with the emailAddress already exists
                if (!$this->validator->isEmailDuplicate(
                    $request->get('emailAddress'),
                    array_column($propellerSubscribers, 'emailAddress')
                )) {
                    throw new InvalidArgumentException();
                }

                $endpointParameters = [
                    'emailAddress' => $request->get('emailAddress'),
                    'firstName' => $request->get('firstName'),
                    'lastName' => $request->get('lastName'),
                    'marketingConsent' => $request->get('marketingConsent') == 'yes' ? true : false,
                    'dateOfBirth' => $request->get('dateOfBirth'),
                ];

                $createSubscriber = $this->propellerApiClient->accessEndpoint(
                    'POST',
                    'api/subscriber/',
                    $endpointParameters
                );

                if (empty($createSubscriber)) {
                    return new JsonResponse([
                        'status' => 'Action Failed',
                        'message' => 'Could not create the subscriber'
                    ]);
                }

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
     * Update Marketting list to a subscriber
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateSubscriberToLists(Request $request): JsonResponse
    {
        if ($request->getMethod() !== 'PUT') {
            return new JsonResponse(['error' => 'Method Not Allowed'], 405);
        }

        if ($request->isMethod('PUT')) {
            try {
                $emailAddress = $request->get('emailAddress');

                if (!$this->validator->emailValidation($emailAddress)) {
                    throw new InvalidArgumentException('The value is not a valid email.');
                }

                $endpointLists = $this->getSubscriberLists();

                if (empty($endpointLists)) {
                    return new JsonResponse([
                        'status' => 'failure',
                        'message' => 'No Marketting Lists found from Endpoint'
                    ]);
                }

                $submittedLists = array_map('trim', explode(',', $request->get('lists')));

                if (!$this->validator->validateLists($submittedLists, $endpointLists)) {
                    throw new InvalidArgumentException();
                }

                $subscriber = $this->getSubscriber($emailAddress);

                if (empty($subscriber)) {
                    return new JsonResponse([
                        'status' => 'failure',
                        'message' => 'Subscriber Not found'
                    ]);
                }

                if (!$subscriber['marketingConsent']) {
                    return new JsonResponse([
                        'status' => 'failure',
                        'message' => 'Subscriber has not provided consent to be added to the list'
                    ]);
                }

                $listIds = [];
                $endpointLists = array_column($endpointLists, 'name', 'id');
                $listIds = array_keys(array_intersect($endpointLists, $submittedLists));
                $updateSubscriberLists = [];

                if (!empty($listIds)) {
                    $endpointParameters = [
                        'emailAddress' => $emailAddress,
                        'lists' => $listIds
                    ];

                    $updateSubscriberLists = $this->propellerApiClient->accessEndpoint('PUT', 'api/subscriber', $endpointParameters);
                }

                if (empty($updateSubscriberLists)) {
                    return new JsonResponse([
                        'status' => 'Action Failed',
                        'message' => 'Could not update the subscriber'
                    ]);
                }

                return new JsonResponse([
                    'status' => 'success',
                    'message' => 'Subscriber Lists updated Successfully'
                ]);
            } catch (InvalidArgumentException $e) {
                error_log($e->getMessage());
                $errors = $this->validator->getErrors();

                return new JsonResponse($errors);
            }
        }
    }

    /**
     * Add a subscriber enquiry
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createSubscriberEnquiry(Request $request)
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['error' => 'Method Not Allowed'], 405);
        }

        if ($request->isMethod('post')) {
            try {

                if (!$this->validator->isValidEnquiry($request)) {
                    throw new InvalidArgumentException();
                }

                $subscriber = $this->getSubscriber($request->get('emailAddress'));

                if (empty($subscriber)) {
                    return new JsonResponse([
                        'status' => 'Action Failed',
                        'message' => 'Subscriber Not found'
                    ]);
                }

                $endpointParameters = [
                    'message' => $request->get('enquiry')
                ];

                $addSubscriberEnquiry = $this->propellerApiClient->accessEndpoint(
                    'POST',
                    'api/subscriber/' . $subscriber['id'] . '/enquiry',
                    $endpointParameters
                );

                if (empty($addSubscriberEnquiry)) {
                    return new JsonResponse([
                        'status' => 'Action Failed',
                        'message' => 'Could not submit the enquiry'
                    ]);
                }

                return new JsonResponse([
                    'status' => 'success',
                    'message' => 'Enquiry for Subscriber created Successfully'
                ]);
            } catch (InvalidArgumentException $e) {
                error_log($e->getMessage());
                $errors = $this->validator->getErrors();

                return new JsonResponse($errors);
            }
        }
    }

    /**
     * Get a subscriber from the Propeller Endpoint
     *
     * @return array
     */
    public function getSubscriber(string $email): array
    {
        $propellerSubscribers = $this->getAllSubscribers();

        if (empty($propellerSubscribers)) {
            return [];
        }

        $subscriber = current(
            array_filter($propellerSubscribers, function ($propellerSubscriber) use ($email) {
                return isset($propellerSubscriber['emailAddress']) &&
                    $propellerSubscriber['emailAddress'] === $email;
            })
        );

        if (empty($subscriber)) {
            return [];
        }

        return $subscriber;
    }

    /**
     * Get the subscriber lists from the Propeller Endpoint
     *
     * @return array
     */
    public function getSubscriberLists(): array
    {
        $propellerSubscriberLists = $this->propellerApiClient->accessEndpoint('GET', 'api/lists');

        if (empty($propellerSubscriberLists)) {
            return [];
        }

        if (isset($propellerSubscriberLists['lists'])) {
            return $propellerSubscriberLists['lists'];
        }

        return [];
    }

    /**
     * Get all Subscribers
     *
     * @return array
     */
    public function getAllSubscribers(): array
    {
        $propellerSubscribers = $this->propellerApiClient->accessEndpoint('GET', 'api/subscribers');

        if (empty($propellerSubscribers)) {
            return [];
        }

        if (isset($propellerSubscribers['subscribers'])) {
            return $propellerSubscribers['subscribers'];
        }

        return [];
    }
}
