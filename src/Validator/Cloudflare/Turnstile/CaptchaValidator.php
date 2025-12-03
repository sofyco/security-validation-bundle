<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Validator\Cloudflare\Turnstile;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Turnstile\Turnstile;

final class CaptchaValidator extends ConstraintValidator
{
    public function __construct(private readonly Turnstile $turnstile, private readonly RequestStack $requestStack)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Captcha) {
            throw new UnexpectedTypeException($constraint, Captcha::class);
        }

        if (false === is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $response = $this->turnstile->verify(
            token: $value,
            remoteIp: $this->requestStack->getCurrentRequest()?->getClientIp(),
        );

        if ($response->success) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
