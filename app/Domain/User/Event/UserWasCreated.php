<?php

namespace App\Domain\User\Event;

use App\Domain\User\Entity\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserWasCreated
 * @package App\Domain\User\Event
 */
class UserWasCreated
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * UserWasCreated constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
