<?php

class UserTest extends TestCase
{
    public function testSetAgeAttribute()
    {
        $user = factory('App\User')->create(['age' => 34]);

        $this->assertSame(34, $user->age);
    }

    public function testSetEmailAttribute()
    {
        $user = factory('App\User')->create(['email' => 'foo@bar.com']);

        $this->assertSame('foo@bar.com', $user->email);
    }

    public function testReviews()
    {
        $user = factory('App\User')->create(['email' => 'foo@bar.com']);
        $reviews = $user->reviews();
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\HasMany', $reviews);
    }
}
