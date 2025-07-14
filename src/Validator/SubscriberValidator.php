<?php

namespace App\Validator;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class SubscriberValidator extends AbstractValidator
{
    /**
     * @param Request $request
     * 
     * @return bool
     */
    public function isValid(Request $request = null): bool
    {
        if (!$request) {
            throw new InvalidArgumentException('No request passed for validation');
        }

        $this->validateRequiredValue('emailAddress', $request->get('emailAddress'));
        $this->isEmailValid('emailAddress', $request->get('emailAddress'));

        $this->validateRequiredValue('dateOfBirth', $request->get('dateOfBirth'));
        $this->isDateValid('dateOfBirth', $request->get('dateOfBirth'));

        $this->validateRequiredValue('marketingConsent', $request->get('marketingConsent'));
        $this->isValidConsent('marketingConsent', $request->get('marketingConsent'));

        return empty($this->errors);
    }

    /**
     * @param string $emailAddress
     * @param array $data
     * 
     * @return bool
     */
    public function isEmailDuplicate(string $emailAddress, $data = []): bool
    {
        $emails = array_column($data, 'email');

        if (in_array($emailAddress, $emails)) {
            $this->errors['emailAddress'] = 'The email already exists.'; 
        }

        return empty($this->errors);
    }

    /**
     * @param ?string $emailAddress
     * 
     * @return bool
     */
    public function isValidEmail(?string $emailAddress): bool
    {
        $this->validateRequiredValue('emailAddress', $emailAddress);
        $this->isEmailValid('emailAddress', $emailAddress);

        return empty($this->errors);
    }
    
    /**
     * @param array $submittedLists
     * @param array $endpointLists
     * 
     * @return bool
     */
    public function validateLists(array $submittedLists = [], $endpointLists = []): void
    {
        if(empty($submittedLists)) {
            $this->errors[$fieldName] = 'The lists value is empty.';
        }

        if(!empty($endpointLists)) {
            $endpointLists = array_column($endpointLists, 'name');

            foreach ($submittedLists as $submittedList) {
                if(!in_array($submittedList, $endpointLists)) {
                    $this->errors['lists'] = 'The submitted list ' . $submittedList . ' does not exist. Please submit as comma seperated strings from the following: ' . implode(', ', $endpointLists);
                    continue;
                }
            }
        }
    }
}
