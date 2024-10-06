<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class CurrentUser extends Constraint
{
    public array $roles = [];
    public string $message = 'user.invalid';
}
