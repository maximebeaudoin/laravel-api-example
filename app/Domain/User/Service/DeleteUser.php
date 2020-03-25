<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\User;
use App\Domain\User\Event\UserWasDeleted;
use App\Domain\User\Repository\UserRepository;

/**
 * Class DeleteUser
 * @package App\Domain\User\Service
 */
class DeleteUser
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * DeleteUser constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $name
     * @param string $email
     */
    public function handle(User $user)
    {
        $this->userRepository->delete($user);

        event(new UserWasDeleted($user));
    }
}
