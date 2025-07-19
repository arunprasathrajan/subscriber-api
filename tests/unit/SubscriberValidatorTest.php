<?php

namespace App\Tests\Unit;

use App\Validator\SubscriberValidator;
use PHPUnit\Framework\TestCase;

class SubscriberValidatorTest extends TestCase
{
    private SubscriberValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new SubscriberValidator();
    }

    /**
     * @dataProvider emailValidationData
     */
    public function testEmailValidation(string $value, bool $expected): void
    {
        $result = $this->validator->emailValidation($value);
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    public function emailValidationData(): array
    {
        return [
            'valid email'   => [
                'tom@test.com',
                true
            ],
            'invalid email'   => [
                'abcd1234',
                false
            ],
        ];
    }
}
