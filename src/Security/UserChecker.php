<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function checkPreAuth(UserInterface $user)
    {

    }

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function checkPostAuth(UserInterface $user)
    {
        if (! $user instanceof User) {
            return;
        }

        if (! $user->isEnabled()) {
            throw new UnauthorizedHttpException('Please enable your account');
        }
    }
}
