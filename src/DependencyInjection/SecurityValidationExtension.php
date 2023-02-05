<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\DependencyInjection;

use Sofyco\Bundle\SecurityValidationBundle\Validator\CurrentUserValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class SecurityValidationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $currentUserValidator = new Definition(CurrentUserValidator::class);
        $currentUserValidator->setAutowired(true);
        $currentUserValidator->addTag('validator.constraint_validator');
        $container->setDefinition(CurrentUserValidator::class, $currentUserValidator);
    }
}
