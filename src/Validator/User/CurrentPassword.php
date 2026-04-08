<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Validator\User;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class CurrentPassword extends Constraint
{
    public function __construct(public string $message = 'user.password.current.invalid')
    {
        parent::__construct();
    }
}
