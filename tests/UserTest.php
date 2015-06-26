<?php

class UserTest extends TestCase
{
    /**
     * @dataProvider getAges
     */
    public function testSetAgeAttribute($age, $expected_age)
    {
        $user = factory('App\User')->create(['age' => $age]);

        $user = \App\User::find($user->id);

        $this->assertSame($expected_age, $user->age);
    }

    public function getAges()
    {
        return [
            [0, 0],
            [1, 1],
            ['21', 21],
            [34, 34],
            [99, 99]
        ];
    }

    public function testSetEmailAttribute()
    {
        $user = factory('App\User')->create(['email' => 'foo@bar.com']);
        $user = \App\User::find($user->id);

        $this->assertSame('foo@bar.com', $user->email);
    }

    public function testReviews()
    {
        $user = factory('App\User')->create(['email' => 'foo@bar.com']);
        $reviews = $user->reviews();
        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\HasMany', $reviews);
    }
}
