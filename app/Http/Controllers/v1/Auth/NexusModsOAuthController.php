<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1\Auth;

use App\Actions\v1\User\CreateUser;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Phinocio\Oauth2NexusMods\Provider\NexusMods;
use Phinocio\Oauth2NexusMods\Provider\NexusModsResourceOwner;
use RuntimeException;

final class NexusModsOAuthController
{
    const NEXUS_STATE_NAME = 'nexus_oauth2_state';

    private NexusMods $provider;

    public function __construct()
    {
        $this->provider = new NexusMods([
            'clientId' => config('oauth.nexusmods.clientId'),
            'clientSecret' => config('oauth.nexusmods.clientSecret'),
            'redirectUri' => config('oauth.nexusmods.redirectUri'),
        ]);
    }

    public function authenticate(): RedirectResponse
    {
        $authUrl = $this->provider->getAuthorizationUrl();
        session([self::NEXUS_STATE_NAME => $this->provider->getState()]);

        return redirect()->away($authUrl);

    }

    public function callback(Request $request, CreateUser $createUser): RedirectResponse
    {
        $code = $request->query('code');
        $state = $request->query('state');

        if (empty($code) || empty($state)
            || ! hash_equals((string) session(self::NEXUS_STATE_NAME, ''), $state)) {
            session()->forget(self::NEXUS_STATE_NAME);
            report(new RuntimeException('Invalid OAuth State'));

            $this->redirectError('invalid_state');
        }

        session()->forget(self::NEXUS_STATE_NAME);

        try {
            $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);

            /** @var NexusModsResourceOwner $resourceOwner */
            $resourceOwner = $this->provider->getResourceOwner($token);
            $userData = ['name' => $resourceOwner->getName(), 'password' => null];

            $user = User::whereName($userData['name'])->first();

            if ($user && $user->password !== null) {
                report(new RuntimeException('User already exists. Log in and merge accounts to sign in with Nexus.'));

                return $this->redirectError('account_exists');
            }

            if (! $user) {
                $user = $createUser->execute($userData, false, ['avatar' => $resourceOwner->getAvatar()]);
            }

            if (! Auth::loginUsingId($user->id, true)) {
                report(new RuntimeException('AUTH FAILED'));
                $this->redirectError('auth_failed');
            }

            session()->regenerate();

            return redirect()->away(config('app.frontend_url').'/nexus-oauth-complete');
        } catch (Exception $e) {
            report($e);

            return $this->redirectError('oauth_failed');
        }
    }

    private function redirectError(string $message): RedirectResponse
    {
        $url = config('app.frontend_url').'/nexus-oauth-complete?'.http_build_query(['error' => $message]);

        return redirect()->away($url);
    }
}
