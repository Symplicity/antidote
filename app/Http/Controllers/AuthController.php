<?php

namespace App\Http\Controllers;

use Hash;
use Validator;
use Illuminate\Http\Request;
use App\Facades\User;
use JWT;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Generate JSON Web Token.
     */
    protected function createToken($user)
    {
        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + (2 * 7 * 24 * 60 * 60),
        ];

        return JWT::encode($payload, env('APP_KEY'));
    }

    /**
     * Log in with Email and Password.
     */
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('username', '=', $username)->first();

        if (!$user) {
            return response()->json(['message' => 'Wrong username and/or password'], 401);
        }

        if (Hash::check($password, $user->password)) {
            unset($user->password);

            return response()->json(['token' => $this->createToken($user)]);
        } else {
            return response()->json(['message' => 'Wrong username and/or password'], 401);
        }
    }

    /**
     * Create Email and Password Account.
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], 422);
        }

        try {
            $user = User::getModel();
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->gender = $request->input('gender');
            $user->age = $request->input('age');
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['token' => $this->createToken($user)]);
    }

    public function verifyResetPasswordToken($token)
    {
        if (!$this->findUserByToken($token)) {
            $token = 'invalid';
        }
        return redirect('/password/reset/' . $token);
    }

    public function updatePasswordFromResetToken($token, Request $request)
    {
        $user = $this->findUserByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Password reset token is invalid or has expired.'], 400);
        }

        $user->password = Hash::make($request->input('password'));
        $user->reset_password_token = null;
        $user->reset_password_token_expiration = null;
        $user->save();

        return response()->json(['token' => $this->createToken($user)]);
    }

    private function findUserByToken($token)
    {
        return User::whereRaw(
            'reset_password_token = ? and reset_password_token_expiration > ?',
            [$token, new DateTime()]
        )->first();
    }

    public function processForgotPassword(Request $request)
    {
        $username = $request->input('username');

        // create random token
        $token = bin2hex(openssl_random_pseudo_bytes(16));

        $user = User::where('username', '=', $username)->first();

        if (!$user) {
            return response()->json(['message' => 'No account with that username exists.'], 400);
        }

        // add reset token
        $user->reset_password_token = $token;

        $now = new DateTime('now');
        $user->reset_password_token_expiration = $now->add(new DateInterval('PT1H30M'));
        $user->save();

        $email = $user->email;
        $site_url = $request->root();

        $data = [
            'email' => $email,
            'site_url' => $site_url,
            'change_password_link' => $site_url . '/api/auth/reset/' . $token,
            'link_expiration_time' => '90 minutes'
        ];

        Mail::send('emails.forgot-password', $data, function ($message) use ($email) {
            $message->from('antidote@symplicity-opensource.com');
            $message->to($email);
            $message->subject('Antidote Password Reset');
        });

        return response()->json([
            'message' => 'An email has been sent to the provided email address with further instructions'
        ]);
    }
}
