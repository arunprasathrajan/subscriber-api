<?php

namespace App\Validator;

use DateTime;

abstract class AbstractValidator
{
    protected const CONSENT_VALUES = ['yes', 'no'];

    /**
     * @var array
     */
    protected $errors;

    public function __construct() 
    {
        $this->errors = [];
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $fieldName
     * @param ?string $value
     */
    public function validateRequiredValue(string $fieldName, ?string $value): void
    {
        if (strlen($value) === 0) {
            $this->errors[$fieldName] = 'The value is required.';
        }
    }

    /**
     * @param string $fieldName
     * @param ?string $value
     */
    public function validateValueLength(string $fieldName, ?string $value): void
    {
        if (strlen($value) <= 255) {
            $this->errors[$fieldName] = 'The character limit is 255.';
        }
    }

    /**
     * @param string $fieldName
     * @param ?string $value
     */
    public function isEmailValid(string $fieldName, ?string $value): void
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$fieldName] = 'The value is not a valid email.';   
        }
    }

    /**
     * @param string $fieldName
     * @param ?string $value
     */
    public function isDateValid(string $fieldName, ?string $value): void
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);

        if (empty($date)) {
            $this->errors[$fieldName] = 'The value is not a valid date. Please use the format y-m-d ex:1990-01-15';
        } elseif (time() < strtotime('+18 years', $date->getTimestamp())) {
            $this->errors[$fieldName] = 'The age is not above 18';   
        }
    }

    /**
     * @param string $fieldName
     * @param ?string $value
     */
    public function isValidConsent(string $fieldName, ?string $value): void
    {
        if (!in_array($value, self::CONSENT_VALUES)) {
            $this->errors[$fieldName] = 'The consent is not valid. Please submit either yes or no';
        }
    }


    /**
     * @return bool
     */
    abstract public function isValid();
}
