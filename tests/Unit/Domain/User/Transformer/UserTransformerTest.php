<?php

namespace Tests\Unit\Domain\User\Transformer;

use App\Domain\User\Entity\User;
use App\Domain\User\Transformer\UserTransformer;
use Tests\TestCase;

class UserTransformerTest extends TestCase
{
    public function testDataWasConvertedProperly()
    {
        // Data we want to test
        $user = new User([
            'name' => 'John',
            'email' => 'john.doe@hotmail.com',
            'job_title' => 'Software Engineer',
            'short_presentation' => 'I like PHP !'
        ]);
        $user->id = 1;

        // Transformer we want to test
        $transformer = new UserTransformer();

        // What we expect
        $this->assertSame([
            'id' => 1,
            'name' => 'John',
            'email' => 'john.doe@hotmail.com',
            'job_title' => 'Software Engineer',
            'short_presentation' => 'I like PHP !'
        ], $transformer->transform($user));

    }
}
