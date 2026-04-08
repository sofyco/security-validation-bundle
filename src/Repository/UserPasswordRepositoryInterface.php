<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Repository;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface UserPasswordRepositoryInterface
{
    public function findByUserIdentifier(string $userIdentifier): ?PasswordAuthenticatedUserInterface;
}
