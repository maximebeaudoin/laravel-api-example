<?php

namespace App\Domain\User\Validator;

use App\Core\Validator\Validator;

/**
 * Class CreateUserValidator
 * @package App\Domain\User\Validator
 */
class CreateUserValidator extends Validator
{
    /**
     * @var array
     */
    protected $rules = [
        'email' => 'required|max:255|unique:users,email',
        'name' => 'max:255',
        'password' => 'required|min:8|max:64'
    ];
}
