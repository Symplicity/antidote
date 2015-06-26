<?php

class UserTest extends TestCase
{
    /**
     * Checks whether user age attribute is correctly set
     *
     */
    public function testSetAgeAttribute()
    {
        $user = factory('App\User')->create(['age' => 34]);

        $this->assertSame(34, $user->age);
    }
}
