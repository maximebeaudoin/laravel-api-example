<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\User;
use App\Domain\User\Event\UserWasCreated;
use App\Domain\User\Event\UserWasUpdated;
use App\Domain\User\Repository\UserRepository;

/**
 * Class UpdateUser
 * @package App\Domain\User\Service
 */
class UpdateUser
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * CreateUser constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @param string $name
     * @param string $email
     * @param string $jobTitle
     * @param string|null $shortPresentation
     * @return User
     */
    public function handle(User $user, string $name, string $email, string $jobTitle, string $shortPresentation = null): User
    {
        $user = $this->userRepository->update($user, [
            'name' => $name,
            'email' => $email,
            'job_title' => $jobTitle,
            'short_presentation' => $shortPresentation
        ]);

        event(new UserWasUpdated($user));

        return $user;
    }
}
