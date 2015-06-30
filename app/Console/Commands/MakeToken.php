<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use App\User;
use JWT;

class MakeToken extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'make:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a JWT token for API authorization';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where('username', '=', $this->option('user'))->first();
        if ($user) {
            $this->info($this->makeToken($user));
        } else {
            $this->error('User not found.');
        }
    }

    private function makeToken($user)
    {
        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + $this->option('expire'),
        ];
        return JWT::encode($payload, env('APP_KEY'));
    }

    protected function getOptions()
    {
        return [
            ['user', 'u', InputOption::VALUE_REQUIRED, 'User name'],
            ['expire', 'e', InputOption::VALUE_REQUIRED, 'Number of seconds from current time when token will expire'],
        ];
    }
}
