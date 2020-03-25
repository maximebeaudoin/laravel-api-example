<?php

namespace App\Core\Validator;

use App\Exceptions\ValidatorException;

/**
 * Class Validator
 * @package App\Core\Validator
 */
abstract class Validator
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @param array $input
     * @return bool
     */
    public function validate(array $input)
    {
        $validator = app('validator')->make($input, $this->rules);

        if ($validator->fails()) {
            throw new ValidatorException($validator->messages());
        }

        return true;
    }
}
