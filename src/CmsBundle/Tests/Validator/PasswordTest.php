<?php
namespace Opifer\CmsBundle\Tests\Validator;

use Opifer\CmsBundle\Validator\Constraints\Password;
use Opifer\CmsBundle\Validator\Constraints\PasswordValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PasswordTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new PasswordValidator();
    }

    public function testPasswordLength()
    {
        $password = 'Aa1';
        $constraint = new Password();

        $this->validator->validate($password, $constraint);

        $this->assertSame(1, $violationsCount = \count($this->context->getViolations()), sprintf('1 violation expected. Got %u.', $violationsCount));
    }

    public function testPasswordMissingCapital()
    {
        $password = 'abcdef123';
        $constraint = new Password();

        $this->validator->validate($password, $constraint);

        $this->assertSame(1, $violationsCount = \count($this->context->getViolations()), sprintf('1 violation expected. Got %u.', $violationsCount));
    }

    public function testPasswordMissingNummeric()
    {
        $password = 'aBcDeFgH';
        $constraint = new Password();

        $this->validator->validate($password, $constraint);

        $this->assertSame(1, $violationsCount = \count($this->context->getViolations()), sprintf('1 violation expected. Got %u.', $violationsCount));
    }

    public function testCorrectPassword()
    {
        $password = 'aBcDeF123';
        $constraint = new Password();

        $this->validator->validate($password, $constraint);

        $this->assertNoViolation();
    }

    public function testPasswordWithSpecialChars()
    {
        $password = 'aBcDeF123!@#';
        $constraint = new Password();

        $this->validator->validate($password, $constraint);

        $this->assertNoViolation();
    }
}
