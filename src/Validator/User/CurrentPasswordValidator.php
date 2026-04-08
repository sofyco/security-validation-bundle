<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Validator\User;

use Sofyco\Bundle\SecurityValidationBundle\Repository\UserPasswordRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CurrentPasswordValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security, private readonly UserPasswordRepositoryInterface $userPasswordRepository, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentPassword) {
            throw new UnexpectedTypeException($constraint, CurrentPassword::class);
        }

        if (false === is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $user = $this->security->getUser();

        if (null === $user) {
            throw new UnauthorizedHttpException(challenge: $value);
        }

        $account = $this->userPasswordRepository->findPasswordByUser(user: $user);

        if (null !== $account && $this->passwordHasher->isPasswordValid(user: $account, plainPassword: $value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
