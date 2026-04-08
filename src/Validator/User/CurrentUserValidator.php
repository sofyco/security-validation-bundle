<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Validator\User;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CurrentUserValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentUser) {
            throw new UnexpectedTypeException($constraint, CurrentUser::class);
        }

        if (null === $user = $this->security->getUser()) {
            return;
        }

        if ($value === $user->getUserIdentifier()) {
            return;
        }

        if (array_any($constraint->roles, fn($role) => $this->security->isGranted($role))) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
