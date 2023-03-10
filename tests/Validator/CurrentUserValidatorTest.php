<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Tests\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use Sofyco\Bundle\SecurityValidationBundle\Validator\CurrentUser;
use Sofyco\Bundle\SecurityValidationBundle\Validator\CurrentUserValidator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class CurrentUserValidatorTest extends ConstraintValidatorTestCase
{
    private MockObject & Security $security;

    public function setUp(): void
    {
        $this->security = $this->createMock(Security::class);

        parent::setUp();
    }

    public function testInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('0000-1111-2222-3333', new NotBlank());
    }

    public function testValidationSkippedByNonLoggedUser(): void
    {
        $this->security->method('getUser')->willReturn(null);

        $this->validator->validate('0000-1111-2222-3333', new CurrentUser());

        $this->assertNoViolation();
    }

    public function testValidationFailedByOtherUser(): void
    {
        $user = new InMemoryUser(username: '0000-1111-2222-3334', password: null);
        $constraint = new CurrentUser();

        $this->security->method('getUser')->willReturn($user);

        $this->validator->validate('0000-1111-2222-3333', $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }

    public function testValidationPassedByCorrectUser(): void
    {
        $user = new InMemoryUser(username: '0000-1111-2222-3333', password: null);

        $this->security->method('getUser')->willReturn($user);

        $this->validator->validate('0000-1111-2222-3333', new CurrentUser());

        $this->assertNoViolation();
    }

    public function testValidationPassedByAllowedRoles(): void
    {
        $user = new InMemoryUser(username: '0000-1111-2222-3334', password: null, roles: ['ROLE_ADMIN']);
        $constraint = new CurrentUser();
        $constraint->roles = ['ROLE_ADMIN'];

        $this->security->method('getUser')->willReturn($user);
        $this->security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $this->validator->validate('0000-1111-2222-3333', $constraint);

        $this->assertNoViolation();
    }

    protected function createValidator(): CurrentUserValidator
    {
        return new CurrentUserValidator($this->security);
    }
}
