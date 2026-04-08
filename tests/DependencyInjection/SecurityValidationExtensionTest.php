<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Tests\DependencyInjection;

use Sofyco\Bundle\SecurityValidationBundle\Validator\User\CurrentUserValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SecurityValidationExtensionTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        restore_exception_handler();
    }

    public function testServiceExists(): void
    {
        self::assertTrue(self::getContainer()->has(CurrentUserValidator::class));
    }
}
