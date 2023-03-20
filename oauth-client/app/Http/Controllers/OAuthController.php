<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class OAuthController extends Controller
{
    public function redirect(Request $request)
    {
        session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => config('oauth.client_id'),
            'redirect_uri' => route('oauth.callback'),
            'response_type' => 'code',
            'scope' => 'read-user',
            'state' => $state,
            'prompt' => 'consent', // "none", "consent", or "login"
        ]);

        return redirect(config('oauth.server_url') . "/oauth/authorize?{$query}");
    }

    public function callback(Request $request)
    {
        $state = session()->pull('state');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class
        );

        $tokenResponse = $this->getToken($request->code);
        $userResponse = $this->getUser($tokenResponse['access_token']);
        $user = User::firstOrCreate(['email' => $userResponse['email']]);
        if ($user->wasRecentlyCreated) {
            $user->fill(['name' => $userResponse['name']])->save();
        }
        $user->token()->updateOrCreate(['user_id' => $user->id], [
            'access_token' => $tokenResponse['access_token'],
            'refresh_token' => $tokenResponse['refresh_token'],
            'expires_in' => $tokenResponse['expires_in']
        ]);
        Auth::login($user);
        return redirect('home');
    }

    public function getToken($code)
    {
        $response = Http::asForm()->post(config('oauth.server_url') . "/oauth/token", [
            'grant_type' => 'authorization_code',
            'client_id' => config('oauth.client_id'),
            'client_secret' => config('oauth.client_secret'),
            'redirect_uri' => config('oauth.callback'),
            'code' => $code,
        ]);

        throw_if(
            !$response->ok() && config('app.debug'),
            new RuntimeException($response->toPsrResponse()?->getBody()?->getContents()['message'] ?? "Couldn't get token from the server.")
        );

        return $response->json();
    }

    public function getUser($accessToken)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])
            ->withToken($accessToken)
            ->get(config('oauth.server_url') . '/api/user');
        throw_if(
            !$response->ok() && config('app.debug'),
            new RuntimeException($response->toPsrResponse()?->getBody()?->getContents()['message'] ?? "Couldn't get user details from the server.")
        );

        return $response->json();
    }
}
