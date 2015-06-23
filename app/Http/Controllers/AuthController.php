<?php

namespace App\Http\Controllers;

use Hash;
use Validator;
use Illuminate\Http\Request;
use App\User;
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
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Wrong email and/or password'], 401);
        }

        if (Hash::check($password, $user->password)) {
            unset($user->password);

            return response()->json(['token' => $this->createToken($user)]);
        } else {
            return response()->json(['message' => 'Wrong email and/or password'], 401);
        }
    }

    /**
     * Create Email and Password Account.
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], 400);
        }

        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['token' => $this->createToken($user)]);
    }

    public function verifyResetPasswordToken($token)
    {
        $user = $this->findUserByToken($token);

        if (!$user) {
            return redirect('/#/password/reset/invalid');
        }

        return redirect('/#/password/reset/'.$token);
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
        return User::whereRaw('reset_password_token = ? and reset_password_token_expiration > ?', [$token, new DateTime()])->first();//AND NOT EXPIRED!
    }

    public function processForgotPassword(Request $request)
    {
        $email = $request->input('email');

        // create random token
        $token = bin2hex(openssl_random_pseudo_bytes(16));

        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'No account with that email address exists.'], 400);
        }

        // add reset token
        $user->reset_password_token = $token;

        $now = new DateTime('now');
        $user->reset_password_token_expiration = $now->add(new DateInterval('PT1H30M'));
        $user->save();

        Mail::raw('Follow this link to reset your password: https://' . $_SERVER['HTTP_HOST'] . '/api/auth/reset/' . $token, function ($message) use ($email) {
            $message->from('antidote@symplicity-opensource.com');
            $message->to($email);
            $message->subject('Antidote Password Reset');
        });

        return ['message' => 'An email has been sent to the provided email address with further instructions'];
    }
}
