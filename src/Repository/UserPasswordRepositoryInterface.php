<?php declare(strict_types=1);

namespace Sofyco\Bundle\SecurityValidationBundle\Repository;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserPasswordRepositoryInterface
{
    public function findPasswordByUser(UserInterface $user): ?PasswordAuthenticatedUserInterface;
}
