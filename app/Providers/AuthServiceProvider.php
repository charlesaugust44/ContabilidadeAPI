<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $request->input('api_token');

            if ($token) {
                [$header, $payload, $signature] = explode(".", $token);

                if (token_expiration($payload))
                    return null;

                $user = User::where('token', $token)->first();

                if ($user == null)
                    return null;

                $serverSignature = hash_hmac('sha256', "$header.$payload", $user->secret, true);
                $serverSignature = base64_encode($serverSignature);

                if ($signature == $serverSignature)
                    return $user;
            }

            return null;
        });
    }
}
