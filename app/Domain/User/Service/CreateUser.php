<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\User;
use App\Domain\User\Event\UserWasCreated;
use App\Domain\User\Repository\UserRepository;

/**
 * Class CreateUser
 * @package App\Domain\User\Service
 */
class CreateUser
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
     * @param string $name
     * @param string $email
     * @return User
     */
    public function handle(string $name, string $email, string $password): User
    {
        $user = $this->userRepository->create([
            'name' => $name,
            'email' => $email,
            'password' =>  app('hash')->make($password)
        ]);

        event(new UserWasCreated($user));

        return $user;
    }
}
