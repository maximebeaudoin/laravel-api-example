<?php

namespace App\Domain\User\Event;

use App\Domain\User\Entity\User;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserWasUpdated
 * @package App\Domain\User\Event
 */
class UserWasUpdated
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * UserWasUpdated constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
