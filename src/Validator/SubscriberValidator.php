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
}
