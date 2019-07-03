<?php
namespace Opifer\CmsBundle\Tests\Validator;

use Opifer\CmsBundle\Validator\Constraints\PasswordStrength;
use Opifer\CmsBundle\Validator\Constraints\PasswordStrengthValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PasswordStrengthTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new PasswordStrengthValidator();
    }

    public function testPasswordLength() {
        $password = "Aa1";
        $constraint = new PasswordStrength();

        $result = $this->validator->validate($password, $constraint);

        $this->assertSame(1, $violationsCount = \count($this->context->getViolations()), sprintf('1 violation expected. Got %u.', $violationsCount));
    }

    public function testPasswordMissingCapital() {
        $password = "abcdef123";
        $constraint = new PasswordStrength();

        $result = $this->validator->validate($password, $constraint);

        $this->assertSame(1, $violationsCount = \count($this->context->getViolations()), sprintf('1 violation expected. Got %u.', $violationsCount));
    }

    public function testPasswordMissingNummeric() {
        $password = "aBcDeFgH";
        $constraint = new PasswordStrength();

        $result = $this->validator->validate($password, $constraint);

        $this->assertSame(1, $violationsCount = \count($this->context->getViolations()), sprintf('1 violation expected. Got %u.', $violationsCount));
    }

    public function testCorrectPassword() {
        $password = "aBcDeF123";
        $constraint = new PasswordStrength();

        $result = $this->validator->validate($password, $constraint);

        $this->assertNoViolation();
    }

    public function testPasswordWithSpecialChars() {
        $password = "aBcDeF123!@#";
        $constraint = new PasswordStrength();

        $result = $this->validator->validate($password, $constraint);

        $this->assertNoViolation();
    }

}