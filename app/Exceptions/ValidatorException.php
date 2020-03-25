<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\MessageProvider;

/**
 * Class ValidatorException
 * @package App\Core\Validator\Exceptions
 */
class ValidatorException extends InvalidArgumentException
{
    /**
     * @var MessageProvider
     */
    protected $provider;

    /**
     * Create a new validation exception instance.
     *
     * @param  \Illuminate\Contracts\Support\MessageProvider $provider
     */
    public function __construct(MessageProvider $provider)
    {
        parent::__construct($provider->getMessageBag());

        $this->provider = $provider;
    }

    /**
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function errors()
    {
        return $this->provider->getMessageBag();
    }
}
