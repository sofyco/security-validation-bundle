<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\DependencyInjection;

use Sofyco\Bundle\SecurityValidationBundle\Validator\Cloudflare\Turnstile\CaptchaValidator;
use Sofyco\Bundle\SecurityValidationBundle\Validator\User\CurrentPasswordValidator;
use Sofyco\Bundle\SecurityValidationBundle\Validator\User\CurrentUserValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class SecurityValidationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        foreach ($this->getValidatorClassNames() as $className) {
            $definition = new Definition(class: $className);
            $definition->setAutowired(autowired: true);
            $definition->setAutoconfigured(autoconfigured: true);
            $definition->addTag(name: 'validator.constraint_validator');
            $container->setDefinition(id: $className, definition: $definition);
        }
    }

    /**
     * @return class-string[]
     */
    private function getValidatorClassNames(): iterable
    {
        return [
            CaptchaValidator::class,
            CurrentUserValidator::class,
            CurrentPasswordValidator::class,
        ];
    }
}
