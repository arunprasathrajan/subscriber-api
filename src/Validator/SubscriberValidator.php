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
        
        if (empty($this->errors['emailAddress'])) {
            $this->isEmailValid('emailAddress', $request->get('emailAddress'));
        }

        $this->validateRequiredValue('dateOfBirth', $request->get('dateOfBirth'));

        if (empty($this->errors['dateOfBirth'])) {
            $this->isDateValid('dateOfBirth', $request->get('dateOfBirth'));
        }

        $this->validateRequiredValue('marketingConsent', $request->get('marketingConsent'));
        
        if (empty($this->errors['marketingConsent'])) {
            $this->isValidConsent('marketingConsent', $request->get('marketingConsent'));
        }

        if(!empty($request->get('firstName'))) {
            $this->validateValueLength('firstName', $request->get('firstName'));
        }

        if(!empty($request->get('lastName'))) {
            $this->validateValueLength('lastName', $request->get('lastName'));
        }

        return empty($this->errors);
    }

    /**
     * @param string $emailAddress
     * @param array $emails
     * 
     * @return bool
     */
    public function isEmailDuplicate(string $emailAddress, $emails = []): bool
    {
        if (in_array($emailAddress, $emails)) {
            $this->errors['emailAddress'] = 'The email already exists.'; 
        }

        return empty($this->errors);
    }

    /**
     * @param Request $request
     * 
     * @return bool
     */
    public function isValidEnquiry(Request $request = null): bool
    {
        $this->emailValidation($request->get('emailAddress'));

        $this->validateRequiredValue('enquiry', $request->get('enquiry'));

        if (empty($this->errors['enquiry']) && strlen($request->get('enquiry')) > 1000) {
            $this->errors['enquiry'] = 'Limit exceeded. The max characters allowed is 1000.'; 
        }

        return empty($this->errors);
    }

    /**
     * @param array $submittedLists
     * @param array $endpointLists
     * 
     * @return bool
     */
    public function validateLists(array $submittedLists = [], $endpointLists = []): bool
    {
        if(empty($submittedLists)) {
            $this->errors['lists'] = 'The lists value is empty.';
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

        return empty($this->errors);
    }

    /**
     * @param ?string $emailAddress
     * 
     * @return bool
     */
    public function emailValidation(?string $emailAddress): bool
    {
        $this->validateRequiredValue('emailAddress', $emailAddress);

        if (empty($this->errors['emailAddress'])) {
            $this->isEmailValid('emailAddress', $emailAddress);
        }

        return empty($this->errors);
    }
}
