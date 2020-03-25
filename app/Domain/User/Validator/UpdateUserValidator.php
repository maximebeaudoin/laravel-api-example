<?php

namespace App\Domain\User\Validator;

use App\Core\Validator\Validator;

/**
 * Class UpdateUserValidator
 * @package App\Domain\User\Validator
 */
class UpdateUserValidator extends Validator
{
    /**
     * @var array
     */
    protected $rules = [
        'email' => 'max:255|unique:users,email',
        'name' => 'max:255',
    ];
}
