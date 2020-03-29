<?php

namespace App\Domain\User\Event;

use App\Domain\User\Entity\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserWasDeleted
 * @package App\Domain\User\Event
 */
class UserWasDeleted
{
    use SerializesModels;

    /**
     * @var User
     */
    public User $user;

    /**
     * UserWasDeleted constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
