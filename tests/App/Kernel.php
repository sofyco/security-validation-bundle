<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Tests\App;

use Sofyco\Bundle\SecurityValidationBundle\SecurityValidationBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new SecurityBundle();
        yield new SecurityValidationBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', ['test' => true]);

        $container->extension('security', [
            'providers' => [
                'users_in_memory' => [
                    'memory' =>  null,
                ],
            ],
            'firewalls' => [
                'main' => [
                    'lazy' => true,
                    'provider' => 'users_in_memory',
                ],
            ],
        ]);

        $container
            ->services()
            ->load('Sofyco\Bundle\SecurityValidationBundle\Validator\\', __DIR__ . '/../../src/Validator')
            ->public();
    }
}
