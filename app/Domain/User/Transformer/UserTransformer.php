<?php

namespace App\Domain\User\Transformer;

use App\Domain\User\Entity\User;
use League\Fractal\TransformerAbstract;

/**
 * Class UserTransformer
 * @package App\Domain\User\Transformer
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'job_title' => $user->job_title,
            'short_presentation' => $user->short_presentation,
        ];
    }
}
