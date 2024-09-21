<?php

namespace App\Http\ExternalApiHelpers;

use App\Models\TradeMeToken;
use App\Models\User;
use Http;
use Str;

class TradeMeHelper
{
    public function getVerificationUrl(User $user): TradeMeToken | null
    {
        //remove the latest created token first
        TradeMeToken::first()?->delete();

        $env = config('trademe.environment');
        $isSandBox = $env === 'sandbox';

        $authUrl = $isSandBox ?
            config('trademe.sandbox_urls.auth_url') :
            config('trademe.production_urls.auth_url');

        $redirectUrl = $isSandBox ?
            config('trademe.sandbox_urls.verifier_url') :
            config('trademe.production_urls.verifier_url');

        $key = config('trademe.key');
        $secret = config('trademe.secret');

        //authorization
        if ($isSandBox) {
            if (config('app.env') === 'local') {
                $header = "oauth_callback=https://localhost:9000/trademe-register, oauth_consumer_key=".$key.", oauth_signature_method=PLAINTEXT, oauth_signature=".$secret."%26";
            } else {
                $header = "oauth_callback=https://staging.dodsonparts.online/trademe-register, oauth_consumer_key=".$key.", oauth_signature_method=PLAINTEXT, oauth_signature=".$secret."%26";
            }
        } else {
            $header = "oauth_callback=https://dodsonparts.online/trademe-register, oauth_consumer_key=".$key.", oauth_signature_method=PLAINTEXT, oauth_signature=".$secret."%26";
        }
        $response = Http::withHeaders(
            [
                'Authorization' => 'OAuth ' . $header
            ]
        )->post($authUrl);
        $authKeys = Str::replace(['oauth_token=', '&oauth_token_secret=', '&oauth_callback_confirmed=true'], ',', $response);
        $tokens = explode(',', $authKeys);
        if (isset($tokens[1], $tokens[2])) {
            $redirectUrl .= $tokens[1];
            return TradeMeToken::create([
                'oauth_token' => $tokens[1],
                'oauth_token_secret' => $tokens[2],
                'oauth_verifier' => null,
                'redirect_url' => $redirectUrl,
                'authorized_by' => $user->id,
                'authorized' => true,
                'environment' => $env,
            ]);
        }
        return null;
    }

    public function setAccessTokens(TradeMeToken $tradeMe): void
    {
        $secret = config('trademe.secret');
        $key = config('trademe.key');
        $env = config('trademe.environment');
        $isSandBox = $env === 'sandbox';
        $accessTokenUrl = $isSandBox ?
            config('trademe.sandbox_urls.auth_access_token_url') :
            config('trademe.production_urls.auth_access_token_url');

        $signature = $secret . '&' . $tradeMe->oauth_token_secret;
        $header = "OAuth oauth_verifier=$tradeMe->oauth_verifier, oauth_consumer_key=$key, oauth_token=$tradeMe->oauth_token, oauth_signature_method=PLAINTEXT, oauth_signature=$signature";
        $response = Http::withHeaders(
            [
                'Authorization' => $header,
            ])->post($accessTokenUrl);
        $str = \Str::replace(['oauth_token=', '&oauth_token_secret='], ',', $response);
        $tokens = explode(',', $str);
        if (isset($tokens[1], $tokens[2])) {
            $tradeMe->update([
                'access_token' => $tokens[1],
                'access_token_secret' => $tokens[2],
            ]);
        }
    }
}
