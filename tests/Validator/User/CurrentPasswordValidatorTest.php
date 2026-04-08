<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Tests\Validator\User;

use PHPUnit\Framework\MockObject\MockObject;
use Sofyco\Bundle\SecurityValidationBundle\Repository\UserPasswordRepositoryInterface;
use Sofyco\Bundle\SecurityValidationBundle\Validator\User\CurrentPassword;
use Sofyco\Bundle\SecurityValidationBundle\Validator\User\CurrentPasswordValidator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<CurrentPasswordValidator>
 */
final class CurrentPasswordValidatorTest extends ConstraintValidatorTestCase
{
    private MockObject & Security $security;
    private MockObject & UserPasswordHasherInterface $passwordHasher;
    private MockObject & UserPasswordRepositoryInterface $userPasswordRepository;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userPasswordRepository = $this->createMock(UserPasswordRepositoryInterface::class);

        parent::setUp();
    }

    public function testInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->security->expects($this->never())->method('getUser');
        $this->userPasswordRepository->expects($this->never())->method('findPasswordByUser');
        $this->passwordHasher->expects($this->never())->method('isPasswordValid');

        $this->validator->validate('password', new NotBlank());
    }

    public function testInvalidValueType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->security->expects($this->never())->method('getUser');
        $this->userPasswordRepository->expects($this->never())->method('findPasswordByUser');
        $this->passwordHasher->expects($this->never())->method('isPasswordValid');

        $this->validator->validate(123, new CurrentPassword());
    }

    public function testValidationFailedByNonLoggedUser(): void
    {
        $this->expectException(UnauthorizedHttpException::class);
        $this->security->expects($this->once())->method('getUser')->willReturn(null);
        $this->userPasswordRepository->expects($this->never())->method('findPasswordByUser');
        $this->passwordHasher->expects($this->never())->method('isPasswordValid');

        $this->validator->validate('password', new CurrentPassword());
    }

    public function testValidationPassedByCorrectCurrentPassword(): void
    {
        $user = new InMemoryUser(username: 'user@example.com', password: 'hashed-password');
        $account = new InMemoryUser(username: 'user@example.com', password: 'hashed-password');

        $this->security->expects($this->once())->method('getUser')->willReturn($user);
        $this->userPasswordRepository
            ->expects($this->once())
            ->method('findPasswordByUser')
            ->with('user@example.com')
            ->willReturn($account);
        $this->passwordHasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($account, 'password')
            ->willReturn(true);

        $this->validator->validate('password', new CurrentPassword());

        $this->assertNoViolation();
    }

    public function testValidationFailedByWrongCurrentPassword(): void
    {
        $user = new InMemoryUser(username: 'user@example.com', password: 'hashed-password');
        $account = new InMemoryUser(username: 'user@example.com', password: 'hashed-password');
        $constraint = new CurrentPassword();

        $this->security->expects($this->once())->method('getUser')->willReturn($user);
        $this->userPasswordRepository
            ->expects($this->once())
            ->method('findPasswordByUser')
            ->with('user@example.com')
            ->willReturn($account);
        $this->passwordHasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($account, 'password')
            ->willReturn(false);

        $this->validator->validate('password', $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }

    public function testValidationFailedByMissingAccount(): void
    {
        $user = new InMemoryUser(username: 'user@example.com', password: 'hashed-password');
        $constraint = new CurrentPassword();

        $this->security->expects($this->once())->method('getUser')->willReturn($user);
        $this->userPasswordRepository
            ->expects($this->once())
            ->method('findPasswordByUser')
            ->with('user@example.com')
            ->willReturn(null);
        $this->passwordHasher->expects($this->never())->method('isPasswordValid');

        $this->validator->validate('password', $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }

    protected function createValidator(): CurrentPasswordValidator
    {
        return new CurrentPasswordValidator($this->security, $this->userPasswordRepository, $this->passwordHasher);
    }
}
