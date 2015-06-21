<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Hash;

class UserController extends Controller
{
    /**
     * Get signed in user's profile.
     */
    public function getUser(Request $request)
    {
        $user = User::find($request['user']['sub']);

        return $user;
    }

    /**
     * Update signed in user's profile.
     */
    public function updateUser(Request $request)
    {
        $user = User::find($request['user']['sub']);

        $old_password = $request->input('oldPassword');
        $new_password = $request->input('newPassword');

        if (Hash::check($old_password, $user->password)) {
            $user->name = $request->input('name');
            $user->email = $request->input('email');

            if ($new_password) {
                $user->password = Hash::make($new_password);
            }

            $user->save();

            return $user;
        } else {
            return response()->json(['message' => 'Please enter correct current password to update your profile'], 401);
        }
    }
}
