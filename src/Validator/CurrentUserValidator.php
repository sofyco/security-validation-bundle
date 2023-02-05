<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception;

final class CurrentUserValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentUser) {
            throw new Exception\UnexpectedTypeException($constraint, CurrentUser::class);
        }

        if (null === $user = $this->security->getUser()) {
            return;
        }

        if ($value === $user->getUserIdentifier()) {
            return;
        }

        foreach ($constraint->roles as $role) {
            if ($this->security->isGranted($role)) {
                return;
            }
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
