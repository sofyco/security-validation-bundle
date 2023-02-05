<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Tests\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();
        yield new \Symfony\Bundle\SecurityBundle\SecurityBundle();
        yield new \Sofyco\Bundle\SecurityValidationBundle\SecurityValidationBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', ['test' => true]);

        $container->services()
            ->load('Sofyco\Bundle\SecurityValidationBundle\Validator\\', __DIR__ . '/../../src/Validator')
            ->public();
    }
}
