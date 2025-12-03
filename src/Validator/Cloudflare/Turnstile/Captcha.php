<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Validator\Cloudflare\Turnstile;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Captcha extends Constraint
{
    public function __construct(public string $message = 'captcha.invalid')
    {
        parent::__construct();
    }
}
